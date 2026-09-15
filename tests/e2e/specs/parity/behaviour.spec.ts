import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { artisan, sqlScalar } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';

const location = (headers: Record<string, string>) => (headers['location'] ?? '').replace(BASE_URL, '');

async function postContact(request: APIRequestContext, locale: string, endpoint: string, fields: Record<string, string>) {
  const token = await anonymousCsrfToken(request);
  return request.post(`/${locale}/contact-us/${endpoint}`, { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token }, multipart: fields });
}

test.describe('contact endpoints', () => {
  const INVALID: Record<string, Record<string, string>> = {
    store: { fullname: 'ab', phone: 'abc', email: 'not-an-email', description: 'x', country_code: '+90' },
    'store-inner': { fullname: 'ab', phone: 'abc', email: 'not-an-email', description: 'x', country_code: '+90', language: '', time_from: '', time_to: '' },
    subscribe: { emails: 'not-an-email' },
  };

  test.beforeEach(() => {
    requireLocal('contact endpoint tests clear the cache and write leads');
    artisan('cache:clear'); // resets the contact.guard limiter between tests
  });

  for (const locale of ['en', 'ar']) {
    for (const endpoint of Object.keys(INVALID)) {
      test(`${locale} ${endpoint} answers empty input with the validation JSON`, async ({ request }) => {
        const response = await postContact(request, locale, endpoint, {});
        expect(response.status()).toBe(200);
        expect(JSON.stringify(await response.json(), null, 2)).toMatchSnapshot([locale, `${endpoint}-empty.json`]);
      });

      test(`${locale} ${endpoint} answers invalid input with the validation JSON`, async ({ request }) => {
        const response = await postContact(request, locale, endpoint, INVALID[endpoint]);
        expect(response.status()).toBe(200);
        expect(JSON.stringify(await response.json(), null, 2)).toMatchSnapshot([locale, `${endpoint}-invalid.json`]);
      });
    }
  }

  test('store-inner stores a valid submission', async ({ request }) => {
    const before = sqlScalar('SELECT COUNT(*) FROM contact_us');
    const response = await postContact(request, 'en', 'store-inner', {
      fullname: 'Parity Inner', phone: '5551234567', email: 'parity-inner@aldar.test', description: 'Parity suite lead',
      country_code: '+90', language: 'English', time_from: '10:00', time_to: '12:00',
    });
    expect(JSON.stringify(await response.json(), null, 2)).toMatchSnapshot(['en', 'store-inner-valid.json']);
    expect(sqlScalar('SELECT COUNT(*) FROM contact_us')).toBe(before + 1);
  });

  test('store-visit still has no controller method and answers 500', async ({ request }) => {
    const response = await postContact(request, 'en', 'store-visit', {});
    expect(response.status()).toBe(500);
  });
});

test('set_currency stores the choice in a cookie and redirects back', async ({ request }) => {
  const token = await anonymousCsrfToken(request);
  const symbol = async () => (await (await request.get('/en/contact-us')).text()).match(/id="currency-form"[\s\S]*?id="dropdownlang"[^>]*>([\s\S]*?)<\/button>/)![1].trim();
  expect(await symbol()).toBe('₺');

  const response = await request.post('/en/set_currency', {
    form: { _token: token, currency: 'USD' },
    headers: { Referer: `${BASE_URL}/en/contact-us` },
    maxRedirects: 0,
  });
  expect(response.status()).toBe(302);
  expect(location(response.headers())).toBe('/en/contact-us');
  expect(response.headersArray().filter(h => h.name.toLowerCase() === 'set-cookie').map(h => h.value.split(';')[0])).toContain('default-currency=USD');
  expect(await symbol()).toBe('$');
});

test('/cookies records consent as JSON and refuses a missing CSRF token', async ({ request, playwright }) => {
  const token = await anonymousCsrfToken(request);
  const accepted = await request.post('/cookies', { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token } });
  expect(accepted.status()).toBe(200);
  expect(await accepted.json()).toEqual({ success: true });
  expect(accepted.headersArray().some(h => h.name.toLowerCase() === 'set-cookie' && h.value.startsWith('cookies='))).toBe(true);

  const fresh = await playwright.request.newContext({ baseURL: BASE_URL });
  const refused = await fresh.post('/cookies', { maxRedirects: 0 });
  expect(refused.status()).toBe(302);
  expect(location(refused.headers())).toBe('/authenticate/login');
  await fresh.dispose();
});

interface Row { id: number; translations?: Array<{ id: number }> }
const byId = (rows: Row[]) => rows
  .map(row => ({ ...row, translations: row.translations?.slice().sort((a, b) => a.id - b.id) }))
  .sort((a, b) => a.id - b.id);

test('listing/regions-by-city-id returns the areas of a city', async ({ request }) => {
  const antalya = await request.get('/en/listing/regions-by-city-id/18');
  expect(antalya.status()).toBe(200);
  const body = await antalya.json();
  expect(JSON.stringify({ ...body, data: byId(body.data) }, null, 2)).toMatchSnapshot('regions-18.json');

  const turkey = await (await request.get('/en/listing/regions-by-city-id/turkey')).json();
  expect({ success: turkey.success, count: turkey.data.length, keys: Object.keys(turkey.data[0]).sort() })
    .toEqual({ success: true, count: 57, keys: expect.arrayContaining(['id', 'city_id', 'native_name', 'name', 'translations']) });
});

test('installments-by-payments returns the child payment categories', async ({ request }) => {
  const cash = await (await request.get('/en/installments-by-payments/518')).json();
  expect(JSON.stringify({ ...cash, data: byId(cash.data) }, null, 2)).toMatchSnapshot('installments-518.json');
  expect((await (await request.get('/en/installments-by-payments/519')).json()).data).toHaveLength(15);
  expect(await (await request.get('/en/installments-by-payments/999999')).json()).toEqual({ success: true, data: [] });
});

test.describe('redirects', () => {
  test('/ goes to /en for a new visitor and to /ar after an Arabic page', async ({ request }) => {
    const first = await request.get('/', { maxRedirects: 0 });
    expect([first.status(), location(first.headers())]).toEqual([302, '/en']);

    await request.get('/ar');
    const again = await request.get('/', { maxRedirects: 0 });
    expect([again.status(), location(again.headers())]).toEqual([302, '/ar']);
  });

  test('a path without a locale gets the /en prefix', async ({ request }) => {
    const response = await request.get('/articles', { maxRedirects: 0 });
    expect([response.status(), location(response.headers())]).toEqual([302, '/en/articles']);
  });

  for (const [from, to] of [['/en/', '/en'], ['/en/articles/', '/en/articles'], ['/ar/faqs/', '/ar/faqs'], ['/en/articles/?page=2', '/en/articles?page=2']]) {
    test(`trailing slash ${from} redirects permanently to ${to}`, async ({ request }) => {
      const response = await request.get(from, { maxRedirects: 0 });
      expect([response.status(), location(response.headers())]).toEqual([301, to]);
    });
  }
});

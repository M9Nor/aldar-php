import { test, expect } from '@playwright/test';
import { appShell } from '../../support/docker';
import { requireLocal } from '../../support/env';

const CACHE_DIR = 'storage/app/public/uploads/.cache/defaults/base.png';
const cachedFiles = () => Number(appShell(`find ${CACHE_DIR} -type f 2>/dev/null | wc -l`));

test('an allowed size is served', async ({ request }) => {
  const response = await request.get('/img/85x85/defaults/base.png');
  expect(response.status()).toBe(200);
  expect(response.headers()['content-type']).toMatch(/^image\//);
});

test('an unknown size with no cached variant is refused', async ({ request }) => {
  // A fresh size on every run, so an earlier run can never have cached it.
  const size = `${1100 + Math.floor(Math.random() * 800)}x${1100 + Math.floor(Math.random() * 800)}`;
  expect((await request.get(`/img/${size}/defaults/base.png`)).status()).toBe(404);
});

test('quality, extension and mark parameters create no new variants', async ({ request }) => {
  requireLocal('reads the cache directory');
  expect((await request.get('/img/85x85/defaults/base.png')).status()).toBe(200);
  const before = cachedFiles();

  const response = await request.get(`/img/85x85/defaults/base.png?quality=${Date.now() % 90 + 5}&extension=gif&mark=x.png`);
  expect(response.status()).toBe(200);
  expect(cachedFiles()).toBe(before);
});

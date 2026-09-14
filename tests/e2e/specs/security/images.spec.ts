import http from 'node:http';
import https from 'node:https';
import { test, expect } from '@playwright/test';
import { appShell } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';

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

// Sends the path byte for byte. Playwright's URL parser would resolve %2e segments
// before sending, so the non-canonical path would never reach the server.
function rawGet(path: string): Promise<number> {
  const base = new URL(BASE_URL);
  const client = base.protocol === 'https:' ? https : http;
  return new Promise((resolve, reject) => {
    const req = client.request({ host: base.hostname, port: base.port || undefined, path, method: 'GET' }, (res) => {
      res.resume();
      res.on('end', () => resolve(res.statusCode ?? 0));
    });
    req.on('error', reject);
    req.end();
  });
}

const allCachedFiles = () => Number(appShell('find storage/app/public/uploads/.cache -type f | wc -l'));

test.describe('non-canonical image paths are refused before Glide sees them', () => {
  test.beforeAll(async ({ request }) => {
    requireLocal('reads the cache directory');
    expect((await request.get('/img/85x85/defaults/base.png')).status()).toBe(200);
  });

  const cachedName = () => appShell(`ls ${CACHE_DIR} | head -n 1`);

  const paths: Array<[string, () => string]> = [
    ['a %2e segment', () => '/img/85x85/defaults/%2e/base.png'],
    ['an empty segment', () => '/img/85x85/defaults//base.png'],
    ['a %2e%2e segment', () => '/img/85x85/x/%2e%2e/defaults/base.png'],
    ['a backslash', () => '/img/85x85/defaults%5Cbase.png'],
    ['a cache file as the source', () => `/img/85x85/.cache/defaults/base.png/${cachedName()}`],
    ['a path in the query string', () => '/img/85x85/other.png?path=defaults/base.png'],
    ['a double-encoded slash', () => '/img/85x85/defaults%252Fbase.png'],
    ['a double-encoded dot segment', () => '/img/85x85/defaults/%252e/base.png'],
    ['a %2e segment at the original size', () => '/img/original/defaults/%2e/base.png'],
  ];

  for (const [label, path] of paths) {
    test(`${label} returns 404 and caches nothing`, async () => {
      const url = path();
      const before = allCachedFiles();
      expect(await rawGet(url), url).toBe(404);
      expect(allCachedFiles(), url).toBe(before);
    });
  }
});

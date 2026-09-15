import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './specs',
  // One worker: the specs share one database, one file cache and one rate limiter.
  workers: 1,
  fullyParallel: false,
  retries: 0,
  timeout: 60_000,
  reporter: [['list']],
  globalSetup: './support/global-setup.ts',
  // Parity baselines live in tests/e2e/snapshots/<spec path>/. UPDATE_PARITY=1 rewrites only the ones that differ.
  snapshotPathTemplate: '{testDir}/../snapshots/{testFilePath}/{arg}{ext}',
  updateSnapshots: process.env.UPDATE_PARITY === '1' ? 'changed' : 'none',
  expect: {
    toHaveScreenshot: { pathTemplate: '{testDir}/../snapshots/{testFilePath}/{platform}/{arg}{ext}' },
  },
  use: {
    baseURL: process.env.BASE_URL ?? 'http://localhost:8080',
    trace: 'retain-on-failure',
  },
  projects: [
    // Parity first: it must see the freshly reset database before the security specs change it.
    { name: 'parity', testMatch: 'parity/**/*.spec.ts', use: { ...devices['Desktop Chrome'] } },
    { name: 'chromium', testIgnore: 'parity/**', use: { ...devices['Desktop Chrome'] } },
  ],
});

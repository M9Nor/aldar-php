import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './specs',
  // One worker: the specs share one database and one rate limiter.
  workers: 1,
  fullyParallel: false,
  retries: 0,
  timeout: 60_000,
  reporter: [['list']],
  globalSetup: './support/global-setup.ts',
  use: {
    baseURL: process.env.BASE_URL ?? 'http://localhost:8080',
    trace: 'retain-on-failure',
  },
  projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});

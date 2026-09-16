import { test, expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import path from 'node:path';

// The guard in support/global-setup.ts is the only thing standing between a parity run and the
// live site, so it is tested directly.
//
// `playwright test --list` cannot exercise it on the installed Playwright (1.63.0): list mode's
// task list omits the global-setup task entirely (see the `options.listMode ? [...] : [...,
// ...createGlobalSetupTasks(config2), ...]` split in node_modules/playwright/lib/runner/index.js,
// runTests()), so `globalSetup()` never runs during a `--list` invocation and every BASE_URL
// "passes" a --list run whether or not the guard would actually refuse it — confirmed empirically:
// `BASE_URL=<a production host> npx playwright test --list --project=parity ...` exits 0 today,
// unmodified guard and all. A real (non-`--list`) run would exercise the guard correctly, but for
// an allowed remote host it would then go on to open real connections to that host while running
// behaviour.spec.ts — exactly what must never happen here, staging included.
//
// So each case below runs the exported `globalSetup()` function directly, in its own child
// process (a fresh module load is required per BASE_URL/PARITY_ALLOWED_HOST, since support/env.ts
// reads them once at import time) via `tsx`, and nothing else: no playwright runner, no browser,
// no test. This is also network-safe for every case exercised here — none of these hosts is
// local, so `globalSetup()` either throws on the host check before touching the network, or (the
// allowed-staging case) returns immediately at its `if (!IS_LOCAL || ...) return;` guard, never
// reaching the fetch that only runs for a local BASE_URL.
const E2E_DIR = path.resolve(__dirname, '../..');
const GLOBAL_SETUP_PATH = path.resolve(E2E_DIR, 'support/global-setup.ts');

const RUN_GLOBAL_SETUP = [
  "const { pathToFileURL } = require('node:url');",
  'import(pathToFileURL(process.env.GLOBAL_SETUP_PATH).href).then((m) => {',
  '  const fn = typeof m.default === "function" ? m.default : m.default.default;',
  '  return fn();',
  '}).then(() => process.exit(0)).catch((e) => {',
  '  process.stderr.write(String((e && e.message) || e) + "\\n");',
  '  process.exit(1);',
  '});',
].join('\n');

function runGuard(env: Record<string, string>): { code: number; output: string } {
  try {
    const output = execFileSync('npx', ['tsx', '-e', RUN_GLOBAL_SETUP], {
      cwd: E2E_DIR,
      env: { ...process.env, GLOBAL_SETUP_PATH, SKIP_DB_RESET: '1', ...env },
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'pipe'],
    });
    return { code: 0, output };
  } catch (error) {
    const e = error as { status: number; stdout: string; stderr: string };
    return { code: e.status, output: `${e.stdout}${e.stderr}` };
  }
}

test('the live site is refused even with an opt-in', () => {
  for (const host of ['https://aldar-emlak.com', 'https://www.aldar-emlak.com']) {
    const run = runGuard({ BASE_URL: host, PARITY_ALLOWED_HOST: new URL(host).hostname });
    expect(run.code, `${host} must not run`).not.toBe(0);
    expect(run.output).toContain('Refusing to run against the live site');
  }
});

test('a non-local host without the opt-in is refused', () => {
  const run = runGuard({ BASE_URL: 'https://staging.aldar-emlak.com' });
  expect(run.code).not.toBe(0);
  expect(run.output).toContain('set PARITY_ALLOWED_HOST');
});

test('staging runs only with the matching opt-in', () => {
  const run = runGuard({ BASE_URL: 'https://staging.aldar-emlak.com', PARITY_ALLOWED_HOST: 'staging.aldar-emlak.com' });
  expect(run.code, run.output).toBe(0);
});

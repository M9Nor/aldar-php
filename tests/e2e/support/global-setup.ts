import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { BASE_URL, IS_LOCAL } from './env';

/** Strips a trailing-dot FQDN and lower-cases, so "aldar-emlak.com." matches like "aldar-emlak.com". */
function normalizeHost(host: string): string {
  return host.replace(/\.$/, '').toLowerCase();
}

export default async function globalSetup(): Promise<void> {
  const host = normalizeHost(new URL(BASE_URL).hostname);

  // Phase 0 never talks to the live site, not even read-only. Specific message kept because
  // the existing test expectations grep for "Refusing".
  if (host === 'aldar-emlak.com' || host.endsWith('.aldar-emlak.com')) {
    throw new Error(`Refusing to run against the live site (BASE_URL=${BASE_URL})`);
  }

  // Allowlist, not a denylist: only localhost/127.0.0.1 (any port) or an explicit opt-in via
  // PARITY_ALLOWED_HOST (for a future read-only staging run in Phase 3) may run. This also
  // catches IP literals and hosting aliases that a denylist on the hostname would miss.
  const allowedHost = process.env.PARITY_ALLOWED_HOST ? normalizeHost(process.env.PARITY_ALLOWED_HOST) : undefined;
  const isAllowed = host === 'localhost' || host === '127.0.0.1' || (allowedHost !== undefined && host === allowedHost);
  if (!isAllowed) {
    throw new Error(`Refusing to run against ${host}: set PARITY_ALLOWED_HOST to allow a non-local host`);
  }

  if (!IS_LOCAL || process.env.SKIP_DB_RESET === '1') return;
  execFileSync('bash', [path.resolve(__dirname, '../../../scripts/e2e/db-reset.sh')], { stdio: 'inherit' });

  // A fresh .env (APP_DEBUG=true, no DEBUGBAR_ENABLED=false) or a Phase 1 config merge would
  // inject barryvdh/laravel-debugbar into every HTML response and fail ~228 snapshots with no
  // pointer to the cause. Fail fast, right after the reset, instead.
  const html = await (await fetch(`${BASE_URL}/en`)).text();
  if (html.includes('phpdebugbar')) {
    throw new Error(
      `The Laravel Debugbar is injecting markup into ${BASE_URL}/en ("phpdebugbar" found in the response). ` +
        'Set DEBUGBAR_ENABLED=false in .env — see PARITY.md, "Environment the baselines assume".',
    );
  }
}

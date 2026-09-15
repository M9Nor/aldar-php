import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { BASE_URL, IS_LOCAL } from './env';

export default function globalSetup(): void {
  // Phase 0 never talks to the live site, not even read-only.
  if (/(^|\.)aldar-emlak\.com$/i.test(new URL(BASE_URL).hostname)) {
    throw new Error(`Refusing to run against the live site (BASE_URL=${BASE_URL})`);
  }
  if (!IS_LOCAL || process.env.SKIP_DB_RESET === '1') return;
  execFileSync('bash', [path.resolve(__dirname, '../../../scripts/e2e/db-reset.sh')], { stdio: 'inherit' });
}

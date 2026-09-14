import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { IS_LOCAL } from './env';

export default function globalSetup(): void {
  if (!IS_LOCAL || process.env.SKIP_DB_RESET === '1') return;
  execFileSync('bash', [path.resolve(__dirname, '../../../scripts/e2e/db-reset.sh')], { stdio: 'inherit' });
}

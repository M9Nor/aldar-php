import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { requireLocal } from './env';

const REPO_ROOT = path.resolve(__dirname, '../../..');

function compose(args: string[]): string {
  requireLocal('Docker access');
  return execFileSync('docker', ['compose', ...args], { cwd: REPO_ROOT, encoding: 'utf8' });
}

export function sql(query: string): string {
  return compose(['exec', '-T', 'db', 'mariadb', '-ualdar', '-paldar', 'aldar', '-N', '-B', '-e', query]).trim();
}

export function sqlScalar(query: string): number {
  return Number(sql(query));
}

export function artisan(...args: string[]): string {
  return compose(['exec', '-T', 'app', 'php', 'artisan', ...args]);
}

export function appShell(command: string): string {
  return compose(['exec', '-T', 'app', 'sh', '-c', command]).trim();
}

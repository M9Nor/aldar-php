export const BASE_URL = process.env.BASE_URL ?? 'http://localhost:8080';
export const IS_LOCAL = /^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/?$/.test(BASE_URL);
/** True when the suite targets a non-local host that was explicitly allowed (Phase 3 staging probe). */
export const IS_REMOTE_PROBE = !IS_LOCAL && process.env.PARITY_ALLOWED_HOST !== undefined;
export const E2E_PASSWORD = process.env.E2E_PASSWORD ?? 'e2e-local-password';

export function requireLocal(what: string): void {
  if (!IS_LOCAL) {
    throw new Error(`${what} only runs against the local Docker stack (BASE_URL=${BASE_URL})`);
  }
}

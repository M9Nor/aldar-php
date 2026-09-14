export const BASE_URL = process.env.BASE_URL ?? 'http://localhost:8080';
export const IS_LOCAL = /^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/?$/.test(BASE_URL);
export const E2E_PASSWORD = process.env.E2E_PASSWORD ?? 'e2e-local-password';

export function requireLocal(what: string): void {
  if (!IS_LOCAL) {
    throw new Error(`${what} only runs against the local Docker stack (BASE_URL=${BASE_URL})`);
  }
}

import { readFileSync } from 'node:fs';
import path from 'node:path';

export interface InventoryEntry {
  kind: string;
  path: string;
}

export const INVENTORY_FILE = path.resolve(__dirname, 'url-inventory.json');

export function loadInventory(): InventoryEntry[] {
  return JSON.parse(readFileSync(INVENTORY_FILE, 'utf8')) as InventoryEntry[];
}

/** '/en/search?q=villa&type=projects' becomes ['en', 'search@q=villa_type=projects.html']. */
export function snapshotName(urlPath: string): string[] {
  const [pathname, query] = urlPath.split('?');
  const segments = pathname.split('/').filter(Boolean);
  const last = segments.pop() ?? 'root';
  const suffix = query ? `@${query.replace(/[^A-Za-z0-9=_-]+/g, '_')}` : '';
  return [...segments, `${last}${suffix}.html`];
}

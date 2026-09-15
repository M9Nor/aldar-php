import { readdirSync, statSync } from 'node:fs';
import path from 'node:path';
import { test, expect } from '@playwright/test';
import { loadInventory, snapshotName } from '../../parity/inventory';

const inventory = loadInventory();

/** Every .html file under `dir`, recursively, as a '/'-joined path relative to `dir`. */
function listHtmlFiles(dir: string): string[] {
  const out: string[] = [];
  for (const entry of readdirSync(dir)) {
    const full = path.join(dir, entry);
    if (statSync(full).isDirectory()) out.push(...listHtmlFiles(full).map(f => `${entry}/${f}`));
    else if (entry.endsWith('.html')) out.push(entry);
  }
  return out;
}

test('the inventory holds 150-250 URLs, half English and half Arabic', () => {
  expect(inventory.length).toBeGreaterThanOrEqual(150);
  expect(inventory.length).toBeLessThanOrEqual(250);
  const en = inventory.filter(e => e.path.startsWith('/en')).map(e => e.path.slice(3));
  const ar = inventory.filter(e => e.path.startsWith('/ar')).map(e => e.path.slice(3));
  expect(en).toEqual(ar);
});

test('every URL is unique and maps to a unique snapshot file', () => {
  expect(new Set(inventory.map(e => e.path)).size).toBe(inventory.length);
  expect(new Set(inventory.map(e => snapshotName(e.path).join('/'))).size).toBe(inventory.length);
});

test('entries are slug paths only, never personal data', () => {
  for (const { path } of inventory) expect(path).toMatch(/^\/(en|ar)(\/[a-z0-9_-]+)*(\?[a-z_]+=[a-z0-9_]+(&[a-z_]+=[a-z0-9_]+)*)?$/);
});

test('every kind the spec names is covered', () => {
  const kinds = new Set(inventory.map(e => e.kind));
  for (const kind of ['static', 'search', 'property', 'opportunity', 'listing', 'filter-page', 'service', 'page', 'story', 'article', 'article-category', 'landing-page', 'not-found']) {
    expect(kinds, kind).toContain(kind);
  }
});

test('every golden-master snapshot file maps back to an inventory entry, so none are orphaned', () => {
  const snapshotDir = path.resolve(__dirname, '../../snapshots/parity/golden-master.spec.ts');
  const actual = new Set(listHtmlFiles(snapshotDir));
  const expected = new Set(inventory.map(e => snapshotName(e.path).join('/')));
  expect(actual).toEqual(expected);
});

import type { Page } from '@playwright/test';

/**
 * A region whose order is not stable between requests on the unchanged Laravel 7 app.
 * Every rule must cite the evidence (the query behind it and the probe that showed it).
 */
export interface OrderRule {
  reason: string;
  /** Inventory kinds the rule applies to. Omit to apply to every page. */
  kinds?: string[];
  /** CSS selector for the element whose children are reordered. */
  container: string;
  /** CSS selector a child must match to take part. */
  child: string;
  /** sort: order children by their markup. count: replace them with a count. */
  mode: 'sort' | 'count';
  /** Position-derived attribute values to rewrite before sorting (regex source). */
  renumber?: string;
}

export const ORDER_RULES: OrderRule[] = [
  {
    reason: 'Footer menus come from the cached footer_menu_items query (MenuComposer), ordered by sort_order with ties; the tie order changed in 1 of 5 cache-cleared rounds.',
    container: '.nav-footer ul',
    child: 'li',
    mode: 'sort',
  },
  {
    reason: 'Header submenus come from the same query shape as the footer (MenuComposer headerMenuItems: eager-loaded filter contents ordered by tied sort_order). Not seen flipping yet; kept because the footer did.',
    container: '#navigation ul',
    child: 'li',
    mode: 'sort',
  },
  {
    reason: 'FAQ answers are eager-loaded category contents ordered by sort_order, which is NULL for all 7 FAQs; /en/faqs alternated between two orders within one cache lifetime.',
    kinds: ['static'],
    container: '.faq [role="tablist"]',
    child: '.panel',
    mode: 'sort',
    renumber: 'tab-\\d+-\\d+',
  },
  {
    reason: 'Article category pages paginate 10 articles ordered by sort_order, NULL for all 53 articles; with 15-31 articles in a category even the set shown on page 1 changes between requests.',
    kinds: ['article-category'],
    container: '.blog-section > .row > .col-lg-8 > .row:first-child',
    child: 'div',
    mode: 'count',
  },
];

/** String-level masks for values that change on every request or every environment. */
export function maskVolatileValues(html: string, baseUrl: string): string {
  const host = new URL(baseUrl).host.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  return html
    .replace(new RegExp(`https?://${host}`, 'g'), '{origin}')
    .replace(new RegExp(`//${host}`, 'g'), '//{host}')
    .replace(/(name="_token"\s+value=")[^"]*"/g, '$1{csrf}"')
    .replace(/(c_token\s*=\s*")[^"]*"/g, '$1{csrf}"')
    .replace(/(<meta\s+name="csrf-token"\s+content=")[^"]*"/g, '$1{csrf}"')
    .replace(/(\.(?:css|js)\?id=)[0-9a-f]{20}/g, '$1{mix-hash}')
    .replace(/(&copy;\s*(?:<\/span>)?\s*)\d{4}/g, '$1{year}');
}

type BrowserRule = Pick<OrderRule, 'container' | 'child' | 'mode' | 'renumber'>;

/** Runs inside the browser: parse without executing scripts, reorder, serialise. */
function canonicalizeInBrowser(arg: { html: string; rules: BrowserRule[] }): string {
  const doc = new DOMParser().parseFromString(arg.html, 'text/html');
  for (const rule of arg.rules) {
    const renumber = rule.renumber ? new RegExp(rule.renumber, 'g') : null;
    // Deepest containers first, so a parent list sorts on already-sorted children.
    for (const container of Array.from(doc.querySelectorAll(rule.container)).reverse()) {
      const children = Array.from(container.children).filter(c => c.matches(rule.child));
      if (children.length === 0) continue;
      children.forEach(c => c.remove());
      if (rule.mode === 'count') {
        container.appendChild(doc.createComment(` parity: ${children.length} x ${rule.child} `));
        continue;
      }
      if (renumber) {
        for (const el of children.flatMap(c => [c, ...Array.from(c.querySelectorAll('*'))])) {
          for (const attr of Array.from(el.attributes)) el.setAttribute(attr.name, attr.value.replace(renumber, '{n}'));
        }
      }
      children
        .map(el => ({ el, key: el.outerHTML.replace(/\s+/g, ' ') }))
        .sort((a, b) => (a.key < b.key ? -1 : a.key > b.key ? 1 : 0))
        .forEach(({ el }) => container.appendChild(el));
    }
  }
  const doctype = doc.doctype ? `<!DOCTYPE ${doc.doctype.name}>` : '';
  return doctype + doc.documentElement.outerHTML;
}

/** One tag or text run per line, whitespace collapsed, so snapshot diffs stay readable. */
export function collapseWhitespace(html: string): string {
  return html
    .replace(/\s+/g, ' ')
    .replace(/\s*(<[^>]+>)\s*/g, '\n$1\n')
    .split('\n')
    .map(line => line.trim())
    .filter(Boolean)
    .join('\n');
}

export async function normalizeHtml(page: Page, html: string, kind: string, baseUrl: string): Promise<string> {
  const rules = ORDER_RULES
    .filter(r => !r.kinds || r.kinds.includes(kind))
    .map(({ container, child, mode, renumber }) => ({ container, child, mode, renumber }));
  const canonical = await page.evaluate(canonicalizeInBrowser, { html: maskVolatileValues(html, baseUrl), rules });
  return collapseWhitespace(canonical) + '\n';
}

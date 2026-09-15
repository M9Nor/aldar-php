import { test, expect } from '@playwright/test';
import { snapshotName } from '../../parity/inventory';
import { collapseWhitespace, maskVolatileValues, normalizeHtml } from '../../parity/normalize';

const BASE = 'http://localhost:8080';

test.describe('maskVolatileValues', () => {
  test('masks per-request tokens', () => {
    const html = '<input type="hidden" name="_token" value="6p5MRvpIjcaeglxVwUcTAj9UV2i4KCn8JoRrSn56"> let c_token = "bhjaEkyW";';
    expect(maskVolatileValues(html, BASE)).toBe('<input type="hidden" name="_token" value="{csrf}"> let c_token = "{csrf}";');
  });

  test('masks the origin but keeps other hosts', () => {
    const html = '<a href="http://localhost:8080/en"><img src="//localhost:8080/a.svg"><a href="https://aldar-emlak.com/en/villa">';
    expect(maskVolatileValues(html, BASE)).toBe('<a href="{origin}/en"><img src="//{host}/a.svg"><a href="https://aldar-emlak.com/en/villa">');
  });

  test('masks Mix hashes and the copyright year, not literal ?v= versions', () => {
    const html = '<script src="/js/frontend.min.js?id=1ef7f9ff9741408c0f9f"></script><link href="/css/main.css?v=7.02"> &copy; </span>2026 -';
    expect(maskVolatileValues(html, BASE)).toBe('<script src="/js/frontend.min.js?id={mix-hash}"></script><link href="/css/main.css?v=7.02"> &copy; </span>{year} -');
  });
});

test('collapseWhitespace puts one tag or text run on each line', () => {
  expect(collapseWhitespace('<p>\n   Hello   <b>world</b>\n</p>')).toBe('<p>\nHello\n<b>\nworld\n</b>\n</p>');
});

test.describe('normalizeHtml', () => {
  test('sorts footer menu items so a tie-order swap gives the same output', async ({ page }) => {
    const a = '<div class="nav-footer"><ul><li><a href="/b">B</a></li><li><a href="/a">A</a></li></ul></div>';
    const b = '<div class="nav-footer"><ul><li><a href="/a">A</a></li><li><a href="/b">B</a></li></ul></div>';
    expect(await normalizeHtml(page, a, 'static', BASE)).toBe(await normalizeHtml(page, b, 'static', BASE));
  });

  test('keeps the order of lists that are not tie-ordered', async ({ page }) => {
    const a = '<ul class="pagination"><li>2</li><li>1</li></ul>';
    expect(await normalizeHtml(page, a, 'static', BASE)).toContain('<li>\n2\n</li>\n<li>\n1\n</li>');
  });

  test('sorts FAQ panels and replaces their position-based ids', async ({ page }) => {
    const panel = (i: number, q: string) => `<div class="panel"><a href="#tab-0-${i}">${q}</a><div id="tab-0-${i}">x</div></div>`;
    const a = `<article class="faq"><div role="tablist">${panel(0, 'Q1')}${panel(1, 'Q2')}</div></article>`;
    const b = `<article class="faq"><div role="tablist">${panel(0, 'Q2')}${panel(1, 'Q1')}</div></article>`;
    const out = await normalizeHtml(page, a, 'static', BASE);
    expect(out).toBe(await normalizeHtml(page, b, 'static', BASE));
    expect(out).toContain('href="#{n}"');
  });

  test('ignores whitespace between tags when computing the sort key', async ({ page }) => {
    const a = '<div class="nav-footer"><ul><li> <a href="/b">B</a></li><li><a href="/a">A</a></li></ul></div>';
    const b = '<div class="nav-footer"><ul><li><a href="/b">B</a></li><li> <a href="/a">A</a></li></ul></div>';
    expect(await normalizeHtml(page, a, 'static', BASE)).toBe(await normalizeHtml(page, b, 'static', BASE));
  });

  test('reduces article-category cards to a count, and only on article-category pages', async ({ page }) => {
    const html = '<div class="blog-section"><div class="row"><div class="col-lg-8"><div class="row"><div>A</div><div>B</div></div><div class="row">pages</div></div></div></div>';
    expect(await normalizeHtml(page, html, 'article-category', BASE)).toContain('<!-- parity: 2 x div -->');
    expect(await normalizeHtml(page, html, 'static', BASE)).toContain('<div>\nA\n</div>');
  });
});

test('snapshotName maps URL paths to file names', () => {
  expect(snapshotName('/en')).toEqual(['en.html']);
  expect(snapshotName('/ar/articles/turkey')).toEqual(['ar', 'articles', 'turkey.html']);
  expect(snapshotName('/en/search?q=villa&type=projects')).toEqual(['en', 'search@q=villa_type=projects.html']);
});

import { test, expect, Page } from '@playwright/test';
import { blockProduction } from '../../support/network';

// Eight key pages x ar/en x desktop/mobile. Viewport-sized shots: they catch missing CSS,
// fonts and images without committing multi-megabyte full-page PNGs.
const PAGES: Record<string, string> = {
  home: '',
  property: '/apartments/rose-marine-butik',
  listing: '/properties/for-sale/istanbul',
  articles: '/articles',
  article: '/articles/realestate-index',
  'contact-us': '/contact-us',
  services: '/services',
  'who-we-are': '/who-we-are',
};
const DEVICES = {
  desktop: { viewport: { width: 1366, height: 800 }, isMobile: false, hasTouch: false },
  mobile: { viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true },
};

/** Stop carousels on their first slide, then wait for fonts and the images in view. */
async function settle(page: Page): Promise<void> {
  await page.waitForLoadState('networkidle');
  await page.evaluate(async () => {
    const w = window as unknown as { jQuery?: any };
    document.querySelectorAll<HTMLElement & { swiper?: any }>('.swiper-container').forEach(el => {
      el.swiper?.autoplay?.stop();
      el.swiper?.slideTo(0, 0, false);
    });
    if (w.jQuery) {
      w.jQuery('.owl-carousel').trigger('stop.owl.autoplay').trigger('to.owl.carousel', [0, 0]);
      w.jQuery('.slick-initialized').slick('slickPause').slick('slickGoTo', 0, true);
    }
    await document.fonts.ready;
    const inView = (el: Element) => { const r = el.getBoundingClientRect(); return r.width > 0 && r.bottom > 0 && r.top < innerHeight; };
    await Promise.all(Array.from(document.images).filter(img => inView(img) && !img.complete)
      .map(img => new Promise(done => { img.onload = img.onerror = done; })));
  });
}

for (const [device, options] of Object.entries(DEVICES)) {
  for (const locale of ['en', 'ar']) {
    for (const [name, path] of Object.entries(PAGES)) {
      test(`${device} ${locale} ${name}`, async ({ browser }) => {
        const context = await browser.newContext({ ...options, deviceScaleFactor: 1 });
        await blockProduction(context);
        const page = await context.newPage();
        try {
          expect((await page.goto(`/${locale}${path}`))?.status()).toBe(200);
          await settle(page);
          await expect(page).toHaveScreenshot([device, locale, `${name}.png`], {
            animations: 'disabled',
            maxDiffPixelRatio: 0.01,
            mask: [page.locator('iframe')], // Google Maps embeds
            timeout: 30_000,
          });
        } finally {
          await context.close();
        }
      });
    }
  }
}

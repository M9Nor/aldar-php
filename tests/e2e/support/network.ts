import { BrowserContext } from '@playwright/test';

/** Abort every browser request to the live site (article bodies embed images from it). */
export async function blockProduction(context: BrowserContext): Promise<void> {
  await context.route(/^https?:\/\/([^/]+\.)?aldar-emlak\.com(\/|$)/, route => route.abort());
}

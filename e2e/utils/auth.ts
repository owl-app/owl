import { Page } from '@playwright/test';
import config from 'config';

export async function loginAsUser(page: Page) {
  await page.goto(config.baseURL);
  await page.fill('input[name="_username"]', config.userName);
  await page.fill('input[name="_password"]', config.userPassword);
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard');
}

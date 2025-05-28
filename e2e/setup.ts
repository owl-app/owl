import { chromium, FullConfig } from '@playwright/test';
import { loginAsUser } from './utils/auth';

async function globalSetup(config: FullConfig) {
  const browser = await chromium.launch();
  const page = await browser.newPage();

  await loginAsUser(page);

  await page.context().storageState({ path: 'storageState.json' });

  await browser.close();
}

export default globalSetup;

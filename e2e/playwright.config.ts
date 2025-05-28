import { defineConfig } from '@playwright/test';
import '@dotenvx/dotenvx/config';

export default defineConfig({
  testDir: './tests',
  fullyParallel: true,
  timeout: 30 * 1000,
  retries: 0,
  maxFailures: 10,
  globalSetup: './setup.ts',
  use: {
    baseURL: process.env.BASE_URL,
    storageState: 'storageState.json',

    headless: true,
  },
});

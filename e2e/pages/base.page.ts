import type { Locator, Page } from "@playwright/test";

export class BasePage {
  protected page: Page;

  constructor(page: Page) {
    this.page = page;
  }

  getByTestAttr(name: string): Locator {
    const selector = `[data-test-${name}]`;
    const element = this.page.locator(selector);

    return element;
  }
}
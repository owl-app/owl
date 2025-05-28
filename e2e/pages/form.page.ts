import { expect, type Locator, type Page } from "@playwright/test";
import { BasePage } from "./base.page";

export class FormPage extends BasePage {
  constructor(
    page: Page,
    readonly loader: Locator = page.locator('.owl-loader'),
  ) {
    super(page);
  }

  async submitForm(action: string = 'referer'): Promise<void>{
    await this.page.click(`[data-form-save-action-param="${action}"]`);
  }

  async waitForFormLoad(): Promise<void> {
    await expect(this.loader).toBeVisible();
    await this.loader.waitFor({ state: "hidden" });
  }

  async expectFieldErrorState(field: Locator, hasError: boolean) {
    const feedback = field.locator('xpath=following-sibling::*[contains(@class, "invalid-feedback")]');
  
    if (hasError) {
      await expect(field).toHaveClass(/is-invalid/);
      await expect(feedback).toBeVisible();
    } else {
      await expect(field).not.toHaveClass(/is-invalid/);
      await expect(feedback).not.toBeVisible();
    }
  } 

  async selectAutcompleteByName(field: Locator, value: string, query: string = '') {
    const tsWrapper = field.locator(':scope + .ts-wrapper');
    const input = tsWrapper.locator('input[role="combobox"]');

    await input.click();

    if (query) {
      await input.fill(query);
    }
  
    const dropdownOption = tsWrapper.locator('.ts-dropdown-content .option', { hasText: value });
    await dropdownOption.waitFor({ state: 'visible', timeout: 5000 });
  
    await dropdownOption.click();
  }
}
import { type Page } from "@playwright/test";
import { FormPage } from "@pages/form.page";

export class CreateInvoicePage extends FormPage {

  constructor(
    page: Page,
    readonly company = page.getByTestId('company'),
    readonly contractor = page.getByTestId('contractor'),
    readonly issueDate = page.getByTestId('issue-date'),
  ) {
    super(page);
  }

  selectCompany(name: string): Promise<void> {
    return this.selectAutcompleteByName(this.company, name);
  }

  selectContractor(name: string): Promise<void> {
    return this.selectAutcompleteByName(this.contractor, name);
  }

  async hasIsuueDateError(): Promise<void> {
    await this.expectFieldErrorState(this.issueDate, false);
  }

  async submitForm(action: string = 'referer'): Promise<void>{
    await this.page.click(`[data-form-save-action-param="${action}"]`);
  }
}
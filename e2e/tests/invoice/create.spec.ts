import { test, expect } from '@playwright/test';
import { CreateInvoicePage } from '@pages/invoice/create.page';

let createInvoicePage: CreateInvoicePage;

test.beforeEach(({ page }) => {
  createInvoicePage = new CreateInvoicePage(page);
});

test.describe('Invoice - create', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/invoices/new');
  });

  test('validation all', async ({ page }) => {
    await createInvoicePage.selectCompany('Paweł Kęska');
    await createInvoicePage.selectContractor('Poland');
    await page.pause();
    await createInvoicePage.submitForm();
    await createInvoicePage.waitForFormLoad();
    await page.pause();
    await createInvoicePage.hasIsuueDateError();
  });
});

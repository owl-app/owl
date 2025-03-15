import { syliusCheckAll } from './check-all';

document.addEventListener('turbo:load', () => {
    document.querySelectorAll('[data-check-all]').forEach(syliusCheckAll);
});
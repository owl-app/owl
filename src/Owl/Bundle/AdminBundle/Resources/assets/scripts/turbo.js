import { runAllAnimations } from './animations';
import { initializeBootstrap } from './bootstrap';
import { syliusCheckAll } from './check-all';
import { showLoader } from './loader';
import { initializeTinyMce } from './tinymce';

document.addEventListener('turbo:load', () => {
    document.querySelectorAll('[data-check-all]').forEach(syliusCheckAll);

    runAllAnimations();
    initializeBootstrap();
    initializeTinyMce();
});

document.addEventListener('turbo:before-fetch-request', () => {
    showLoader(document.querySelector('.owl-grid'), { class: { loader: 'content' }});
});
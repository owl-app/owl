import { runAllAnimations } from './animations';
import { initializeBootstrap } from './bootstrap';
import { showLoader } from './loader';
import { initializeTinyMce } from './tinymce';

document.addEventListener('turbo:load', () => {
    runAllAnimations();
    initializeBootstrap();
    initializeTinyMce();
});

document.addEventListener('turbo:before-fetch-request', () => {
    showLoader(document.querySelector('.owl-grid').querySelector('.content'), { class: { loader: 'content' }});
});
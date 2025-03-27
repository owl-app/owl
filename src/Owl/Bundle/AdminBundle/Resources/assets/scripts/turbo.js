import { runAllAnimations } from './animations';
import { initializeBootstrap } from './bootstrap';
import { initializeTinyMce } from './tinymce';

document.addEventListener('turbo:load', () => {
    runAllAnimations();
    initializeBootstrap();
    initializeTinyMce();
});
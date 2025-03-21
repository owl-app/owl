import { debounce } from '../utils/debounce';

export const runAllAnimations = () => {
    debounce(() => {
        slideDown();
    }, 100)();
};

export const slideDown = () => {
    document.querySelectorAll('.slide-down').forEach((element) => {
        element.classList.add('active');
    });
};
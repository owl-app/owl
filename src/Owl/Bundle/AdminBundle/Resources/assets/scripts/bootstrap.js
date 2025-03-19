/* eslint-env browser */
import * as bootstrap from 'bootstrap';

export const initializeBootstrap = () => {
    document.querySelectorAll('.dropdown-static').forEach((dropdownToggleEl) => {
        const parent = dropdownToggleEl.closest('[data-bs-toggle="dropdown"]');
        if (parent) {
            new bootstrap.Dropdown(parent, {
                popperConfig(defaultBsPopperConfig) {
                    return { ...defaultBsPopperConfig, strategy: 'fixed' };
                }
            });
        }
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((tooltipTriggerEl) => {
        if (tooltipTriggerEl != null) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        }
        
    });
};

window.bootstrap = bootstrap;

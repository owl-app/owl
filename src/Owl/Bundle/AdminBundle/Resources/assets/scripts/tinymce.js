import { debounce } from "../utils/debounce";

export const initializeTinyMce = () => {
    const settings = document.querySelectorAll('[data-tinymce-setting]') ?? null;

    settings.forEach((setting) => {
        const parsedSettings = JSON.parse(setting.textContent);

        if (document.querySelector(parsedSettings.selector)) {
            parsedSettings.setup = function (editor) {
                editor.on('blur', function () {
                    const editorElm = document.querySelector(`[name="${editor.targetElm.name}"]`);
                    editorElm.value = editor.getContent();
                    editorElm.dispatchEvent(new Event('change', { bubbles: true }));
                });
            };

            debounce(() => initTinyMCE(parsedSettings), 50)();
        }
    });
};
export const initializeTinyMce = () => {
    const contentConfig = document.querySelector('tinymce-config')?.textContent ?? null;

    if (contentConfig) {
        const config = JSON.parse(contentConfig);

        // fix validation live component
        config.setup = function (editor) {
            editor.on('blur', function () {
                const editorElm = document.querySelector(`[name="${editor.targetElm.name}"]`);
                editorElm.value = editor.getContent();
                editorElm.dispatchEvent(new Event('change', { bubbles: true }));
            });
        };

        initTinyMCE(config);
    }
};
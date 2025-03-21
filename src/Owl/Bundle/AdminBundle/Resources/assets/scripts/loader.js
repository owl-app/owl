export function showLoader(element, options = {}) {
    if (element) {
        element.insertAdjacentHTML(
            'beforeend',
            `
                <div class="owl-loader ${options.class.loader ?? ''}" data-turbo-temporary>
                    <div 
                        class="spinner-border ${options.class.spinner ?? ''}"
                        style="width: ${options.width ?? '3rem'}; height: ${options.height ?? '3rem'};"
                    >
                    </div>
                </div>
            `
        );
    }
}

export function hideLoader(element) {
    if (element) {
        element.querySelector('.owl-loader').remove();
    }
}
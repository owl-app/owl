export function showLoader(element, options = {}) {
    element.insertAdjacentHTML(
        'beforeend',
        `
            <div class="owl-loader ${options.class.loader ?? ''}">
                <div 
                    class="spinner-border ${options.class.spinner ?? ''}"
                    style="width: ${options.width ?? '3rem'}; height: ${options.height ?? '3rem'};"
                >
                </div>
            </div>
        `
    );
}

export function hideLoader(element) {
    element.querySelector('.owl-loader').remove();
}
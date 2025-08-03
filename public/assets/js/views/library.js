// Consolidated function for Library page view
export function loadLibraryView() {
    const url = 'library';
    mainContainer.html('<div class="col-12 text-center">Loading library...</div>');
    window.history.pushState({ view: 'library' }, '', `${URL_ROOT}/library`);

    $.ajax({
        url: url,
        success: (data) => displayLibraryContent(data),
        error: () => showError('Error loading library')
    });

    togglePlayerbar();
}
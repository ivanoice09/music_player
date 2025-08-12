// Consolidated function for Album page view
export function loadAlbumView(albumId) {
    if (!albumId) {
        showError('Invalid album ID');
        return;
    }

    const url = `${URL_ROOT}/album/${albumId}`;
    mainContainer.html('<div class="col-12 text-center">Loading album...</div>');

    $.ajax({
        url: url,
        dataType: 'json',
        success: (data) => {
            if (data.error) {
                showError(data.error);
            } else {
                window.history.pushState(
                    { view: 'album', albumId },
                    '',
                    `${URL_ROOT}/album/${albumId}`
                );
                displayAlbumContent(data);
            }
        },
        error: (xhr) => {
            const errorMsg = xhr.responseJSON?.error || 'Error loading album';
            showError(`${errorMsg} (Status: ${xhr.status})`);
        }
    });

    togglePlayerbar();
}
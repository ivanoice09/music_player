// Consolidated function for Artist page view
export function loadArtistView(artistId) {
    if (!artistId) {
        showError('Invalid artist ID');
        return;
    }

    const url = `${URL_ROOT}/artist/${artistId}`;
    mainContainer.html('<div class="col-12 text-center">Loading artist...</div>');

    $.ajax({
        url: url,
        dataType: 'json',
        success: (data) => {
            if (data.error) {
                showError(data.error);
            } else {
                window.history.pushState(
                    { view: 'artist', artistId },
                    '',
                    `${URL_ROOT}/artist/${artistId}`
                );
                displayArtistContent(data);
            }
        },
        error: (xhr) => {
            const errorMsg = xhr.responseJSON?.error || 'Error loading artist';
            showError(`${errorMsg} (Status: ${xhr.status})`);
        }
    });

    togglePlayerbar();
}
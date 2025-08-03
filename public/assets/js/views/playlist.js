// Consolidated function for Playlist page view
export function loadPlaylistView(playlistId) {
    // Validate input
    if (!playlistId) {
        showError('Invalid playlist ID');
        return;
    }

    // Abort any pending request
    if (activePlaylistRequest) {
        activePlaylistRequest.abort();
    }

    const url = `${URL_ROOT}/playlist/${playlistId}`;

    // Show loading state
    mainContainer.html('<div class="col-12 text-center">Loading playlist...</div>');

    // Make the request
    activePlaylistRequest = $.ajax({
        url: url,
        dataType: 'json',
        success: (data) => {
            console.log('Playlist data received:', data);
            if (data.error) {
                showError(data.error);
                mainContainer.html(''); // Clear loading state
            } else {
                // Only update URL after successful load
                window.history.pushState(
                    { view: 'playlist', playlistId },
                    '',
                    `${URL_ROOT}/playlist/${playlistId}`
                );
                displayPlaylistContent(data);
            }
        },
        error: (xhr) => {
            // Don't show error if request was aborted
            if (xhr.statusText !== 'abort') {
                const errorMsg = xhr.responseJSON?.error || 'Error loading playlist';
                showError(`${errorMsg} (Status: ${xhr.status})`);
                mainContainer.html(''); // Clear loading state
            }
        },
        complete: () => {
            activePlaylistRequest = null;
        }
    });

    togglePlayerbar();
}
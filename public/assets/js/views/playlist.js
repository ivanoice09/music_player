import { mainContainer, templateCache } from "../config/config.js";
import { loadTemplate } from '../core/template.js';
import { showError, showToast } from "../core/error-handler.js";
import { togglePlayerbar } from '../player/playerbar.js';

let activePlaylistRequest = null;

// Displays Playlist's UI
async function displayPlaylistContent(data) {
    try {
        console.log('Displaying playlist data:', data); // Debug log

        const templateName = 'playlist-view';
        await loadTemplate(templateName);

        const source = templateCache[templateName];
        const template = Handlebars.compile(source);

        // Ensure proper data structure
        const viewData = {
            playlist: {
                id: data.id,
                name: data.name,
                image_url: data.image_url,
                songs: data.songs || [] // Ensure songs array exists
            }
        };

        console.log('Songs in playlist:', viewData.playlist.songs);

        mainContainer.html(template(viewData));

        // Setup editable playlist name
        $('#playlistName').on('blur', function () {
            const newName = $(this).text();
            updatePlaylistName(data.id, newName);
        });

        // Setup image upload
        $('.change-image-btn').on('click', function () {
            $('#playlistImageUpload').click();
        });
        $('#playlistImageUpload').on('change', handleImageUpload);

    } catch (error) {
        console.error('Error displaying playlist:', error);
        showError('Failed to load playlist view');
    }
}

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

    const url = `${window.APP_CONFIG.URL_ROOT}/playlist/${playlistId}`;

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
                    `${window.APP_CONFIG.URL_ROOT}/playlist/${playlistId}`
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

export function createNewPlaylist() {
    // added URL_ROOT, because when in the Playlist view, 
    // and the user clicks on create playlist, 
    // the url 'playlist/create' becomes 'playlist/playlist/create'
    const url = `${window.APP_CONFIG.URL_ROOT}/playlist/create`;

    $.ajax({
        url: url,
        method: 'POST',
        success: (data) => {
            if (data.success && data.id) {
                loadPlaylistView(data.id);
            }
        },
        error: () => showError('Error creating playlist')
    });
}

export function updatePlaylistName(playlistId, newName) {
    $.ajax({
        url: URL_ROOT + 'playlist/update',
        method: 'POST',
        data: JSON.stringify({
            id: playlistId,
            name: newName
        }),
        contentType: 'application/json',
        success: (data) => {
            if (data.success) {
                showToast('Playlist name updated');
            } else {
                showError(data.message || 'Failed to update playlist name');
            }
        },
        error: () => showError('Error updating playlist name')
    });
}

export function handleImageUpload(e) {
    const file = e.target.files[0];
    const playlistId = $('#playlistContainer').data('id');
    const url = `${window.APP_CONFIG.URL_ROOT}/playlist/update`;

    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);
    formData.append('id', playlistId);

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: (data) => {
            if (data.success) {
                $('#playlistImage').attr('src', URL.createObjectURL(file));
                showToast('Playlist image updated');
            } else {
                showError(data.message || 'Failed to update image');
            }
        },
        error: () => showError('Error updating playlist image')
    });
}
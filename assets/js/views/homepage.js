import { setViewTemplate } from '../core/template.js';
import { mainContainer } from '../config/config.js';
import { showError } from '../core/error-handler.js';
import { togglePlayerbar } from '../player/playerbar.js';

// Function to fetch popular songs
export function fetchPopularSongs() {
    $.ajax({
        url: 'popular',
        success: (data) => setViewTemplate(data, 'Popular Songs', 'song-grid'),
        error: () => showError('Error loading popular songs')
    });
}

// Consolidated function for songs
export function showPopularSongs() {
    mainContainer.html('<div class="col-12 text-center">Loading popular songs...</div>');
    window.history.pushState({ view: 'popular' }, '', `${window.APP_CONFIG.URL_ROOT}/`);
    fetchPopularSongs();
    togglePlayerbar();
}

// Check URL on page load for popular songs request
export function loadInitialView() {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('q')) {
        performSearch(urlParams.get('q'));
    } else {
        showPopularSongs(); // Always show popular songs by default
    }
}
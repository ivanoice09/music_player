import { showPopularSongs } from '../views/homepage.js';
import { loadSearchView } from '../views/search.js';
import { checkAuth, showAuthRequiredModal } from '../core/auth.js';

let debounceTimer;

// Handle popular songs anchor click
export function setupHomeLink() {
    $('#homeLink').click(function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        showPopularSongs(); // New consolidated function
    });
}

// Auto-search with debounce (500ms delay)
export function setupSearchInput() {
    $('#searchInput').keyup(function () {
        const query = $(this).val().trim();
        clearTimeout(debounceTimer);

        if (query.length >= 2) {
            debounceTimer = setTimeout(() => {
                loadSearchView(query);
            }, 500);
        } else if (query.length === 0) {
            // Return to popular view when search is cleared
            showPopularSongs();
        }
    });
}

// Initialize when library link is clicked
export function setupLibraryLink() {
    $('#library-link').on('click', function (e) {
        e.preventDefault();
        if (!checkAuth()) {
            showAuthRequiredModal();
            return;
        }
        loadLibraryView();
    });
}

// Add event listener to create playlist
export function setupCreatePlaylistBtn() {
    $('#createPlaylistBtn').click(function (e) {
        e.preventDefault();
        if (!checkAuth()) {
            showAuthRequiredModal();
            return;
        }
        createNewPlaylist();
    });
}

// Export all functions as a single object for convenience
export const clickHandlers = {
    setupHomeLink,
    setupSearchInput,
    setupLibraryLink,
    setupCreatePlaylistBtn
};
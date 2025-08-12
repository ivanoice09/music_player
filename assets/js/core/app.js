import { loadTemplate } from './template.js'
import { playerBar } from '../config/config.js';
import { showPopularSongs, loadInitialView } from '../views/homepage.js';
import { loadLibraryView } from '../views/library.js';
import { loadPlaylistView } from '../views/playlist.js';
import { loadArtistView } from '../views/artist.js';
import { loadAlbumView } from '../views/album.js';
import { showError, showToast } from './error-handler.js';
import { playerState } from '../player/playerbar.js';
import { navbarClickHandlers } from '../components/navbarClickHandlers.js';
import { libraryClickHandlers } from '../components/libraryClickHandlers.js';
import '../utils/handlebars-helpers.js';

// app.js - Core application initialization
export function initApp(config) {
    window.APP_CONFIG = config;

    $(document).ready(function () {

        // Initialize essential components
        playerBar.hide();

        // Initialize player state
        playerState.init(); // Initialize the player

        // Pre-load essential templates
        const essentialTemplates = [
            'song-grid',
            'song-list',
            'library-grid',
            'library-list',
            'playlist-view',
            'artist-view',
            'album-view'
        ];

        Promise.all(essentialTemplates.map(t => loadTemplate(t)))
            .then(() => {
                loadInitialView();
                // Initialize all navbar click handlers
                navbarClickHandlers.setupHomeLink();
                navbarClickHandlers.setupSearchInput();
                navbarClickHandlers.setupLibraryLink();
                navbarClickHandlers.setupCreatePlaylistBtn();
                // Initialize library item click handlers
                libraryClickHandlers.setupLibraryItemClickHandlers();
                libraryClickHandlers.setupLibraryLayoutToggle();
                libraryClickHandlers.setupLibrarySorting();
                libraryClickHandlers.setupPinButton();
                libraryClickHandlers.setupRemoveItemButton();

            })
            .catch(err => {
                console.error('Failed to pre-load templates:', err);
                showError('Failed to initialize application');
            });

        // Global scope exposure
        window.LibraryFunctions = {
            loadLibraryView,
            loadPlaylistView,
            loadArtistView,
            loadAlbumView,
            showError,
            showToast
        };

        //=========
        // POPSTATE
        //=========
        window.addEventListener('popstate', (event) => {
            if (event.state?.view === 'search') {
                performSearch(event.state.query);
            } else if (event.state?.view === 'artist') {
                loadArtistView(event.state.artistId);
            } else if (event.state?.view === 'album') {
                loadAlbumView(event.state.albumId);
            } else if (event.state?.view === 'playlist') {
                loadPlaylistView(event.state.playlistId);
            } else {
                showPopularSongs();
            }
        });
    });
}
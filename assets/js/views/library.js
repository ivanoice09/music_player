import { mainContainer, templateCache } from "../config/config.js";
import { loadTemplate } from "../core/template.js";
import { showError } from "../core/error-handler.js";
import { togglePlayerbar } from "../player/playerbar.js";

const DEFAULT_LIBRARY_LAYOUT = 'grid';
const DEFAULT_LIBRARY_SORT = 'recent';

function sortLibraryData(data, sortMethod = 'recent') {

    // soft copy of array
    const sortedData = [...data];

    // Helper function for safe comparison
    const safeCompare = (a, b) => {
        a = a || ''; // Convert null/undefined to empty string
        b = b || '';
        return a.localeCompare(b);
    };

    // Implement your sorting logic
    switch (sortMethod) {
        case 'recent':
            return sortedData.sort((a, b) => {
                const dateA = new Date(a.created_at || 0);
                const dateB = new Date(b.created_at || 0);
                return dateB - dateA;
            });

        case 'pinned':
            return sortedData.sort((a, b) => {
                // Pinned items first, then by recent
                if (a.is_pinned !== b.is_pinned) {
                    return b.is_pinned - a.is_pinned;
                }
                return new Date(b.created_at || 0) - new Date(a.created_at || 0);
            });

        case 'artist':
            return sortedData.sort((a, b) => {
                // Handle artist comparison
                let aArtist, bArtist;

                if (a.item_type === 'artist') {
                    aArtist = a.name;
                } else if (a.item_type === 'playlist') {
                    aArtist = ''; // Playlists have no artist
                } else {
                    try {
                        const metadata = JSON.parse(a.metadata || '{}');
                        aArtist = metadata.artist || '';
                    } catch {
                        aArtist = '';
                    }
                }

                if (b.item_type === 'artist') {
                    bArtist = b.name;
                } else if (b.item_type === 'playlist') {
                    bArtist = '';
                } else {
                    try {
                        const metadata = JSON.parse(b.metadata || '{}');
                        bArtist = metadata.artist || '';
                    } catch {
                        bArtist = '';
                    }
                }

                return safeCompare(aArtist, bArtist);
            });

        case 'alpha':
            return sortedData.sort((a, b) => safeCompare(a.name, b.name));

        default:
            return sortedData;
    }
}

// Displays Library's UI
async function displayLibraryContent(data, layout, sort) {

    // Get user preferences or use defaults for layout and sort
    const userLayoutPreference = localStorage.getItem('libraryLayoutPreference') || DEFAULT_LIBRARY_LAYOUT;
    const userSortPreference = localStorage.getItem('librarySortPreference') || DEFAULT_LIBRARY_SORT;

    // Use provided values or fall back to preferences
    layout = layout || userLayoutPreference;
    sort = sort || userSortPreference;

    // Sort data
    const sortedData = sortLibraryData(data, sort);

    // Load appropriate template
    const templateName = `library-${layout}`;
    await loadTemplate(templateName);
    const source = templateCache[templateName];
    const template = Handlebars.compile(source);

    mainContainer.html(template({ items: sortedData }));

    // Update active layout/sort buttons
    $(`.layout-option[data-layout="${layout}"]`).addClass('active');
    $(`.sort-option[data-sort="${sort}"]`).addClass('active');
}

// Consolidated function for Library page view
export function loadLibraryView() {
    const url = 'library';
    mainContainer.html('<div class="col-12 text-center">Loading library...</div>');
    window.history.pushState({ view: 'library' }, '', `${window.APP_CONFIG.URL_ROOT}/library`);

    $.ajax({
        url: url,
        success: (data) => displayLibraryContent(data),
        error: () => showError('Error loading library')
    });

    togglePlayerbar();
}


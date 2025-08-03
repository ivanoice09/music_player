import { togglePlayerbar } from "../player/playerbar.js";
import { mainContainer } from "../config/config.js";
import { setViewTemplate } from "../core/template.js";
import { showError } from "../core/error-handler.js";

// Function to perform search
function performSearch(query) {
    $.ajax({
        url: 'search',
        data: { q: query },
        success: (data) => setViewTemplate(data, `Results for "${query}"`, 'song-list'), // Template is changeable(in 3rd argument)
        error: () => showError('Error loading results')
    });
}

// Consolidated function for search
export function loadSearchView(query) {
    mainContainer.html('<div class="col-12 text-center">Searching...</div>');
    window.history.pushState({ view: 'search', query }, '', `${window.APP_CONFIG.URL_ROOT}/search?q=${encodeURIComponent(query)}`);
    performSearch(query);
    togglePlayerbar();
}
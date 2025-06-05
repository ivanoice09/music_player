$(document).ready(function () {
    const playerBar = $('#playerBar');
    const mainContainer = $('#mainView'); // Consistent container reference
    const templateCache = {};
    let debounceTimer;

    playerBar.hide(); // Initialize - hide player bar
    loadInitialView(); // Renamed from checkInitialView for clarity

    // ======================= DIVIDER =======================

    // Handle popular songs anchor click
    $('#homeLink').click(function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        showPopularSongs(); // New consolidated function
    });

    // Auto-search with debounce (500ms delay)
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

    // Event listener for library nav link
    $('#library-link').click(function (e) {
        e.preventDefault();
        loadLibraryView();
    });

    // Add event listener for create playlist
    $('#create-playlist-link').on('click', function (e) {
        e.preventDefault();
        createNewPlaylist();
    });

    // ======================= DIVIDER =======================

    // Consolidated function for popular songs
    function showPopularSongs() {
        mainContainer.html('<div class="col-12 text-center">Loading popular songs...</div>');
        window.history.pushState({ view: 'popular' }, '', `${URL_ROOT}/`);

        fetchPopularSongs();
    }

    // Consolidated function for search
    function loadSearchView(query) {
        mainContainer.html('<div class="col-12 text-center">Searching...</div>');
        window.history.pushState({ view: 'search', query }, '', `${URL_ROOT}/search?q=${encodeURIComponent(query)}`);

        performSearch(query);
    }

    // Consolidated function for Library's page view
    function loadLibraryView() {
        if (!isLoggedIn()) {
            showAuthRequiredModal();
            return;
        }

        mainContainer.html('<div class="col-12 text-center">Loading library...</div>');
        window.history.pushState({ view: 'library' }, '', `${URL_ROOT}/library`);

        $.ajax({
            url: 'library',
            success: (data) => displayLibraryContent(data),
            error: () => showError('Error loading library')
        });
    }

    // Consolidated function for Playlists' page view
    function loadPlaylistView(playlistId) {
        if (!isLoggedIn()) {
            showAuthRequiredModal();
            return;
        }

        mainContainer.html('<div class="col-12 text-center">Loading playlist...</div>');
        window.history.pushState({ view: 'playlist', playlistId }, '', `${URL_ROOT}/playlist/${playlistId}`);

        $.ajax({
            url: `playlist/${playlistId}`,
            success: (data) => displayPlaylistContent(data),
            error: () => showError('Error loading playlist')
        });
    }

    function createNewPlaylist() {
        $.ajax({
            url: 'playlist/create',
            method: 'POST',
            success: (data) => loadPlaylistView(data.id),
            error: () => showError('Error creating playlist')
        });
    }

    // ======================= DIVIDER =======================

    // Function to fetch popular songs
    function fetchPopularSongs() {
        $.ajax({
            url: 'popular',
            success: (data) => displayResults(data, 'Popular Songs', 'song-grid'), // Template is changeable(in 3rd argument)
            error: () => showError('Error loading popular songs')
        });
    }

    // Function to perform search
    function performSearch(query) {
        $.ajax({
            url: 'search',
            data: { q: query },
            success: (data) => displayResults(data, `Results for "${query}"`, 'song-list'), // Template is changeable(in 3rd argument)
            error: () => showError('Error loading results')
        });
    }

    // ======================= DIVIDER =======================

    // load template for songs' layout structures (grid, list)
    async function loadTemplate(templateName) {
        if (!templateCache[templateName]) {
            try {
                const url = `templates?name=${templateName}`;
                const response = await fetch(url);

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const html = await response.text();
                document.body.insertAdjacentHTML('beforeend', html);

                const templateElement = document.getElementById(`${templateName}-template`);
                if (!templateElement) throw new Error(`Template ${templateName}-template not found in response`);

                templateCache[templateName] = templateElement.innerHTML;
            } catch (error) {
                console.error('Failed to load template:', error);
                throw error; // Re-throw to handle in displayResults()
            }
        }
        return templateCache[templateName];
    }

    // ======================= DIVIDER =======================

    // Function to display searching songs results
    async function displayResults(results, title = '', templateName = 'song-grid') {
        const mainContainer = $('#mainView');
        if (results?.length > 0) {
            await loadTemplate(templateName);
            const source = templateCache[templateName];
            const template = Handlebars.compile(source);
            mainContainer.html(template({ title, songs: results }));
        } else {
            mainContainer.html('<div class="alert alert-warning">No results found.</div>');
        }
    }

    // After loading display it
    async function displayLibraryContent(data, layout = 'grid', sort = 'recent') {
        // Sort data
        const sortedData = sortLibraryData(data, sort);

        // Load appropriate template
        const templateName = `library-${layout}`;
        await loadTemplate(templateName);
        const source = templateCache[templateName];
        const template = Handlebars.compile(source);

        // Register helper for item_type comparison
        Handlebars.registerHelper('eq', function (a, b, options) {
            return a === b ? options.fn(this) : options.inverse(this);
        });

        mainContainer.html(template({ items: sortedData }));
    }

    async function displayPlaylistContent(data) {
        await loadTemplate('playlist-view');
        const source = templateCache['playlist-view'];
        const template = Handlebars.compile(source);
        mainContainer.html(template({ playlist: data }));

        // Setup editable playlist name
        $('#playlistName').on('blur', function () {
            const newName = $(this).text();
            updatePlaylistName(data.id, newName);
        });

        // Setup image upload
        $('#playlistImageUpload').on('change', handleImageUpload);
    }

    // ======================= DIVIDER =======================

    // Check URL on page load for popular songs request
    function loadInitialView() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('q')) {
            performSearch(urlParams.get('q'));
        } else {
            showPopularSongs(); // Always show popular songs by default
        }
    }

    window.addEventListener('popstate', (event) => {
        if (event.state?.view === 'search') {
            performSearch(event.state.query);
        } else {
            showPopularSongs();
        }
    });
});
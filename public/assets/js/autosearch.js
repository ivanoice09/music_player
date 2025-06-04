$(document).ready(function () {
    const playerBar = $('#playerBar');
    const mainContainer = $('#mainView'); // Consistent container reference
    const templateCache = {};
    let debounceTimer;

    // Initialize - hide player bar
    playerBar.hide();
    loadInitialView(); // Renamed from checkInitialView for clarity

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

    // Function to fetch popular songs
    function fetchPopularSongs() {
        $.ajax({
            url: 'popular',
            success: (data) => displayResults(data, 'Popular Songs', 'song-grid'),
            error: () => showError('Error loading popular songs')
        });
    }

    // Function to perform search
    function performSearch(query) {
        $.ajax({
            url: 'search',
            data: { q: query },
            success: (data) => displayResults(data, `Results for "${query}"`, 'song-list'),
            error: () => showError('Error loading results')
        });
    }

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

    // Function to display search results
    async function displayResults(results, title = '', templateName = '') {
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
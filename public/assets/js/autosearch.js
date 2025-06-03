$(document).ready(function () {
    const playerBar = $('#playerBar');
    const mainContainer = $('#mainView'); // Consistent container reference
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
            success: (data) => displayResults(data, 'Popular Songs'),
            error: () => showError('Error loading popular songs')
        });
    }

    // Function to perform search
    function performSearch(query) {
        $.ajax({
            url: 'search',
            data: { q: query },
            success: (data) => displayResults(data, 'Search Songs'),
            error: () => showError('Error loading results')
        });
    }

    // Function to display search results
    function displayResults(results, title = '') {
        const mainContainer = $('#mainView');
        if (results?.length > 0) {
            // Get the template source
            const source = document.getElementById('song-template').innerHTML;
            // Compile the template
            const template = Handlebars.compile(source);
            // Render the template with data
            const html = template({
                title,  // Optional title (e.g., "Popular Songs")
                songs: results // Array of song objects
            });
            // Update the DOM
            mainContainer.html(html);
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
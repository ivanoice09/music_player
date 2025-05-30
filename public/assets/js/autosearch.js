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

    // New function to fetch popular songs
    function fetchPopularSongs() {
        $.ajax({
            url: 'popular',
            success: (data) => displayResults(data),
            error: () => showError('Error loading popular songs')
        });
    }

    // Function to perform search
    function performSearch(query) {
        $.ajax({
            url: 'search',
            data: { q: query },
            success: (data) => displayResults(data),
            error: () => showError('Error loading results')
        });
    }

    // Function to display search results
    function displayResults(results) {
        if (results?.length > 0) {
            let html = results.map(song => `
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="song-card card h-100"
                        data-audio="${song.audio}" 
                        data-title="${song.name}" 
                        data-artist="${song.artist_name}" 
                        data-artwork="${song.image}">
                        <img src="${song.image}" class="card-img-top" alt="${song.name}">
                        <div class="card-body">
                            <h5 class="card-title">${song.name}</h5>
                            <p class="card-text text-muted">${song.artist_name}</p>
                        </div>
                    </div>
                </div>
            `).join('');
            mainContainer.html(html);
        } else {
            showError('No results found');
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
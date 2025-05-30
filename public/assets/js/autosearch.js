$(document).ready(function () {
    const playerBar = $('#playerBar');
    let debounceTimer;

    // Initialize - hide player bar
    playerBar.hide();

    // Handle popular songs anchor click
    $('#popularSongsLink').click(function(e) {
        e.preventDefault(); // Prevent default anchor behavior
        fetchPopularSongs();
    });

    // Auto-search with debounce (500ms delay)
    $('#searchInput').keyup(function () {
        const query = $(this).val().trim();

        if (query.length > 0) {
            // Change URL and load search page
            loadSearchPage(query);

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                performSearch(query);
            }, 500);
        }
    });

    function loadSearchPage(query) {
        // Passing the URL and the query into variable searchUrl
        const searchUrl = `${URL_ROOT}/search?q=${encodeURIComponent(query)}`;

        // Only navigate if we're not already on the search page
        if (!window.location.pathname.includes('/search')) {
            window.location.href = searchUrl;
        } else {
            // If already on search page, just update URL without reload
            window.history.pushState({}, '', searchUrl);
        }
    }

    // New function to fetch popular songs
    function fetchPopularSongs() {
        $('#searchResults').html('<div class="col-12 text-center">Loading...</div>');
        $.ajax({
            url: 'music/popularSongs', // This should match your backend route
            type: 'GET',
            success: function (data) {
                console.log('Popular songs response:', data);
                displayResults(data);
                // Update URL to reflect we're viewing popular songs
                window.history.pushState({}, '', `${URL_ROOT}/search#popular`);
            },
            error: function (xhr, status, error) {
                console.error(error);
                $('#searchResults').html('<div class="col-12 text-center text-muted">Error loading popular songs</div>');
            }
        });
    }

    // Function to perform search
    function performSearch(query) {
        $('#searchResults').html('<div class="col-12 text-center">Searching...</div>');
        $.ajax({
            url: 'music/searchResults',
            type: 'GET',
            data: { q: query },
            success: function (data) {
                // See Raw API response
                console.log('Raw API response:', data);
                displayResults(data);
            },
            error: function (xhr, status, error) {
                console.error(error);
                $('#searchResults').html('<div class="col-12 text-center text-muted">Error loading results</div>');
            }
        });
    }

    // Function to display search results
    function displayResults(results) {
        if (results && results.length > 0) {
            let html = '';
            results.forEach(song => {
                html += `
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="song-card card h-100" data-audio="${song.audio}" data-title="${song.name}" data-artist="${song.artist_name}" data-artwork="${song.image}">
                        <img src="${song.image}" class="card-img-top" alt="${song.name}">
                        <div class="card-body">
                            <h5 class="card-title">${song.name}</h5>
                            <p class="card-text text-muted">${song.artist_name}</p>
                        </div>
                    </div>
                </div>
                `;
            });
            $('#searchResults').html(html);

        } else {
            $('#searchResults').html('<div class="col-12 text-center text-muted">No results found</div>');
        }
    }

    // popstate event handler
    // window.addEventListener('popstate', function (event) {
    //     if (window.location.pathname === '/search') {
    //         // Load search results based on current URL parameters
    //         const urlParams = new URLSearchParams(window.location.search);
    //         const query = urlParams.get('q');
    //         if (query) performSearch(query);
    //     }
    // });

    // Check URL on page load for popular songs request
    function checkInitialView() {
        const urlParams = new URLSearchParams(window.location.search);
        const hash = window.location.hash;
        
        if (urlParams.has('q')) {
            const query = urlParams.get('q');
            if (query) performSearch(query);
        } 
        else if (urlParams.has('popular') || hash === '#popular') {
            fetchPopularSongs();
        }
    }

    // Run this check when page loads
    checkInitialView();

    // Update popstate handler
    window.addEventListener('popstate', function(event) {
        checkInitialView();
    });

});
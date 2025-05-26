$(document).ready(function () {
    const audioPlayer = document.getElementById('audioPlayer');
    const playerBar = $('#playerBar');
    let debounceTimer;

    // Initialize - hide player bar
    playerBar.hide();

    // Search on button click
    $('#searchButton').click(function() {
        performSearch();
    });

    // Search on Enter key
    $('#searchInput').keypress(function(e) {
        if (e.which === 13) {
            performSearch();
        }
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
        // Use your configured URL_ROOT
        const searchUrl = `${URL_ROOT}/search?q=${encodeURIComponent(query)}`;

        // Only navigate if we're not already on the search page
        if (!window.location.pathname.includes('/search')) {
            window.location.href = searchUrl;
        } else {
            // If already on search page, just update URL without reload
            window.history.pushState({}, '', searchUrl);
        }
    }

    // Function to perform search
    function performSearch(query) {
        $.ajax({
            url: 'music/searchResults',
            type: 'GET',
            data: { q: query },
            // See Raw API response
            success: function (data) {
                console.log('Raw API response:', data);
                displayResults(data);
            },
            success: function (data) {
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

    // Volume control
    $('#volumeControl').click(function () {
        if (audioPlayer.muted) {
            audioPlayer.muted = false;
            $(this).html('<i class="fas fa-volume-up"></i>');
        } else {
            audioPlayer.muted = true;
            $(this).html('<i class="fas fa-volume-mute"></i>');
        }
    });
});

// popstate event handler
window.addEventListener('popstate', function (event) {
    if (window.location.pathname === '/search') {
        // Load search results based on current URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('q');
        if (query) performSearch(query);
    }
});
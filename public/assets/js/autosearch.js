$(document).ready(function () {

    // ========================
    // HANDLEBARS CONFIGURATION
    // ========================

    // Equality helper
    Handlebars.registerHelper('eq', function (a, b, options) {
        // When used as inline helper (no options parameter)
        if (arguments.length === 2 || !options.fn) {
            return a === b;
        }
        // When used as block helper
        return a === b ? options.fn(this) : options.inverse(this);
    });

    // JSON stringify helper
    Handlebars.registerHelper('json', function (context) {
        return JSON.stringify(context);
    });

    // Format duration (seconds to MM:SS)
    Handlebars.registerHelper('formatDuration', function (seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    });

    // ==============
    // ERROR HANDLERS
    // ==============

    function showError(message, duration = 3000) {
        const errorElement = $(`<div class="alert alert-danger">${message}</div>`);
        $('#mainView').prepend(errorElement);
        setTimeout(() => errorElement.fadeOut(), duration);
    }

    // =================
    // APPLICATION SETUP
    // =================

    const playerBar = $('#playerBar');
    const mainContainer = $('#mainView'); // Consistent container reference
    const templateCache = {};
    let activePlaylistRequest = null;
    const DEFAULT_LIBRARY_LAYOUT = 'grid';
    const DEFAULT_LIBRARY_SORT = 'recent';
    let debounceTimer;

    // simple check if logged in
    function checkAuth() {
        return authStatus.isLoggedIn;
    }

    playerBar.hide(); // Initialize - hide player bar

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
        .then(() => loadInitialView())
        .catch(err => {
            console.error('Failed to pre-load templates:', err);
            showError('Failed to initialize application');
        });

    // ====================
    // CLICKABLE COMPONENTS
    // ====================

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

    // Initialize when library link is clicked
    $('#library-link').on('click', function (e) {
        e.preventDefault();
        if (!checkAuth()) {
            showAuthRequiredModal();
            return;
        }
        loadLibraryView();
    });

    // Add event listener to create playlist
    $('#createPlaylistBtn').click(function (e) {
        e.preventDefault();
        if (!checkAuth()) {
            showAuthRequiredModal();
            return;
        }
        createNewPlaylist();
    });

    // ==================================
    // LIBRARY ADDITIONAL FUNCTIONALITIES
    // ==================================

    // This gets invoked by displayLibraryContent()
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

    // function togglePinItem(e) {
    //     const itemElement = e.target.closest('.library-item');
    //     const itemId = itemElement.dataset.id;

    //     $.ajax({
    //         url: 'library/pin',
    //         method: 'POST',
    //         contentType: 'application/json',
    //         data: JSON.stringify({ itemId }),
    //         success: (data) => {
    //             if (data.success) {
    //                 itemElement.classList.toggle('pinned');
    //                 e.target.classList.toggle('text-primary');
    //             }
    //         },
    //         error: () => showError('Failed to toggle pin')
    //     });
    // }

    // ================
    // TEMPLATE LOADERS
    // ================

    // load template for songs' layout structures (grid, list)
    async function loadTemplate(templateName) {
        if (!templateCache[templateName]) {
            try {
                const url = `templates?name=${templateName}`;
                const response = await fetch(url);

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const templateElement = doc.getElementById(`${templateName}-template`);
                if (!templateElement) {
                    throw new Error(`Template ${templateName}-template not found in response`);
                }

                templateCache[templateName] = templateElement.innerHTML;

                // Add to DOM only if not already present
                if (!document.getElementById(`${templateName}-template`)) {
                    document.body.insertAdjacentHTML('beforeend', html);
                }

            } catch (error) {
                console.error(`Failed to load template ${templateName}:`, error);
                throw error;
            }
        }
        return templateCache[templateName];
    }

    // =================
    // DISPLAY FUNCTIONS
    // =================

    // Displays searching songs results
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

    // Displays Library's UI
    async function displayLibraryContent(data, layout, sort) {

        // Get user preferences or use defaults
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

    // Displays Playlist's UI
    async function displayPlaylistContent(data) {
        try {
            console.log('Displaying playlist data:', data); // Debug log

            const templateName = 'playlist-view';
            await loadTemplate(templateName);

            const source = templateCache[templateName];
            const template = Handlebars.compile(source);

            // Ensure proper data structure
            const viewData = {
                playlist: {
                    id: data.id,
                    name: data.name,
                    image_url: data.image_url,
                    songs: data.songs || [] // Ensure songs array exists
                }
            };

            mainContainer.html(template(viewData));

            // Setup editable playlist name
            $('#playlistName').on('blur', function () {
                const newName = $(this).text();
                updatePlaylistName(data.id, newName);
            });

            // Setup image upload
            $('.change-image-btn').on('click', function () {
                $('#playlistImageUpload').click();
            });
            $('#playlistImageUpload').on('change', handleImageUpload);

        } catch (error) {
            console.error('Error displaying playlist:', error);
            showError('Failed to load playlist view');
        }
    }

    // Display Artist View
    async function displayArtistContent(data) {
        try {
            const templateName = 'artist-view';
            await loadTemplate(templateName);

            const source = templateCache[templateName];
            const template = Handlebars.compile(source);

            const viewData = {
                artist: {
                    id: data.id,
                    name: data.name,
                    image: data.image,
                    songs: data.songs || []
                }
            };

            mainContainer.html(template(viewData));

        } catch (error) {
            console.error('Error displaying artist:', error);
            showError('Failed to load artist view');
        }
    }

    // Display Album View
    async function displayAlbumContent(data) {
        try {
            const templateName = 'album-view';
            await loadTemplate(templateName);

            const source = templateCache[templateName];
            const template = Handlebars.compile(source);

            const viewData = {
                album: {
                    id: data.id,
                    name: data.name,
                    image: data.image,
                    artist_name: data.artist_name,
                    songs: data.songs || []
                }
            };

            mainContainer.html(template(viewData));

        } catch (error) {
            console.error('Error displaying album:', error);
            showError('Failed to load album view');
        }
    }


    // ===============
    // VIEW GENERATORS
    // ===============

    // Function to fetch popular songs
    function fetchPopularSongs() {
        $.ajax({
            url: 'popular',
            success: (data) => displayResults(data, 'Popular Songs', 'song-grid'), // Template is changeable(in 3rd argument)
            error: () => showError('Error loading popular songs')
        });
    }

    // Consolidated function for songs
    function showPopularSongs() {
        mainContainer.html('<div class="col-12 text-center">Loading popular songs...</div>');
        window.history.pushState({ view: 'popular' }, '', `${URL_ROOT}/`);

        fetchPopularSongs();
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

    // Consolidated function for search
    function loadSearchView(query) {
        mainContainer.html('<div class="col-12 text-center">Searching...</div>');
        window.history.pushState({ view: 'search', query }, '', `${URL_ROOT}/search?q=${encodeURIComponent(query)}`);

        performSearch(query);
    }

    // Consolidated function for Library page view
    function loadLibraryView() {
        const url = 'library';
        console.log('Loading playlist from:', url);

        mainContainer.html('<div class="col-12 text-center">Loading library...</div>');
        window.history.pushState({ view: 'library' }, '', `${URL_ROOT}/library`);

        $.ajax({
            url: url,
            success: (data) => displayLibraryContent(data),
            error: () => showError('Error loading library')
        });
    }

    // Consolidated function for Playlist page view
    function loadPlaylistView(playlistId) {

        // Validate input
        if (!playlistId) {
            showError('Invalid playlist ID');
            return;
        }

        // Abort any pending request
        if (activePlaylistRequest) {
            activePlaylistRequest.abort();
        }

        const url = `${URL_ROOT}/playlist/${playlistId}`;
        console.log('Loading playlist from:', url);  // Debug log

        // Show loading state
        mainContainer.html('<div class="col-12 text-center">Loading playlist...</div>');

        // Make the request
        activePlaylistRequest = $.ajax({
            url: url,
            dataType: 'json',
            success: (data) => {
                console.log('Playlist data received:', data);
                if (data.error) {
                    showError(data.error);
                    mainContainer.html(''); // Clear loading state
                } else {
                    // Only update URL after successful load
                    window.history.pushState(
                        { view: 'playlist', playlistId },
                        '',
                        `${URL_ROOT}/playlist/${playlistId}`
                    );
                    displayPlaylistContent(data);
                }
            },
            error: (xhr) => {
                // Don't show error if request was aborted
                if (xhr.statusText !== 'abort') {
                    const errorMsg = xhr.responseJSON?.error || 'Error loading playlist';
                    showError(`${errorMsg} (Status: ${xhr.status})`);
                    mainContainer.html(''); // Clear loading state
                }
            },
            complete: () => {
                activePlaylistRequest = null;
            }
        });
    }

    // Consolidated function for Artist page view
    function loadArtistView(artistId) {
        if (!artistId) {
            showError('Invalid artist ID');
            return;
        }

        const url = `${URL_ROOT}/artist/${artistId}`;
        mainContainer.html('<div class="col-12 text-center">Loading artist...</div>');

        $.ajax({
            url: url,
            dataType: 'json',
            success: (data) => {
                if (data.error) {
                    showError(data.error);
                } else {
                    window.history.pushState(
                        { view: 'artist', artistId },
                        '',
                        `${URL_ROOT}/artist/${artistId}`
                    );
                    displayArtistContent(data);
                }
            },
            error: (xhr) => {
                const errorMsg = xhr.responseJSON?.error || 'Error loading artist';
                showError(`${errorMsg} (Status: ${xhr.status})`);
            }
        });
    }

    // Consolidated function for Album page view
    function loadAlbumView(albumId) {
        if (!albumId) {
            showError('Invalid album ID');
            return;
        }

        const url = `${URL_ROOT}/album/${albumId}`;
        mainContainer.html('<div class="col-12 text-center">Loading album...</div>');

        $.ajax({
            url: url,
            dataType: 'json',
            success: (data) => {
                if (data.error) {
                    showError(data.error);
                } else {
                    window.history.pushState(
                        { view: 'album', albumId },
                        '',
                        `${URL_ROOT}/album/${albumId}`
                    );
                    displayAlbumContent(data);
                }
            },
            error: (xhr) => {
                const errorMsg = xhr.responseJSON?.error || 'Error loading album';
                showError(`${errorMsg} (Status: ${xhr.status})`);
            }
        });
    }


    // ================= 
    //      MODALS
    // =================

    function showAuthRequiredModal() {
        // Check if modal already exists
        if ($('#authRequiredModal').length) {
            $('#authRequiredModal').modal('show');
            return;
        }

        // Load modal via AJAX if not in DOM
        $.get('partials/authModal', function (html) {
            $('body').append(html);
            $('#authRequiredModal').modal('show');

            // Remove modal from DOM when hidden
            $('#authRequiredModal').on('hidden.bs.modal', function () {
                $(this).remove();
            });
        }).fail(function () {
            // Fallback if AJAX fails
            alert('Please sign in to access this feature.');
            window.location.href = authStatus.loginUrl;
        });
    }

    // =======================
    // PLAYLIST FUNCTIONALITES
    // =======================

    function createNewPlaylist() {
        $.ajax({
            url: 'playlist/create',
            method: 'POST',
            success: (data) => {
                if (data.success && data.id) {
                    loadPlaylistView(data.id);
                }
            },
            error: () => showError('Error creating playlist')
        });
    }

    function updatePlaylistName(playlistId, newName) {
        $.ajax({
            url: 'playlist/update',
            method: 'POST',
            data: JSON.stringify({
                id: playlistId,
                name: newName
            }),
            contentType: 'application/json',
            success: (data) => {
                if (data.success) {
                    showToast('Playlist name updated');
                } else {
                    showError(data.message || 'Failed to update playlist name');
                }
            },
            error: () => showError('Error updating playlist name')
        });
    }

    // User can upload their own playlist image/profile from local storage
    function handleImageUpload(e) {
        const file = e.target.files[0];
        const playlistId = $('#playlistContainer').data('id');

        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('id', playlistId);

        $.ajax({
            url: 'playlist/update',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (data) => {
                if (data.success) {
                    $('#playlistImage').attr('src', URL.createObjectURL(file));
                    showToast('Playlist image updated');
                } else {
                    showError(data.message || 'Failed to update image');
                }
            },
            error: () => showError('Error updating playlist image')
        });
    }

    function showToast(message) {
        // Implement a simple toast notification
        const toast = $(`<div class="toast">${message}</div>`);
        $('body').append(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    //============================
    // INITIALIZE THE INITIAL VIEW
    //============================

    // Check URL on page load for popular songs request
    function loadInitialView() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('q')) {
            performSearch(urlParams.get('q'));
        } else {
            showPopularSongs(); // Always show popular songs by default
        }
    }

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

    //========================
    // GLOBAL SCOPE EXPOSITION
    //========================

    window.LibraryFunctions = {
        loadLibraryView,
        loadPlaylistView,
        loadArtistView,
        loadAlbumView,
        showError,
        showToast
    };

});


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
    let debounceTimer;

    // simple check if logged in
    function checkAuth() {
        return authStatus.isLoggedIn;
    }

    playerBar.hide(); // Initialize - hide player bar

    // Pre-load essential templates
    const essentialTemplates = ['song-grid', 'song-list', 'library-grid', 'library-list', 'playlist-view'];
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
    // Add event listener for library nav link
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
        // Implement your sorting logic
        switch (sortMethod) {
            case 'recent':
                return [...data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            case 'pinned':
                return [...data].sort((a, b) => b.is_pinned - a.is_pinned);
            case 'name':
                return [...data].sort((a, b) => a.name.localeCompare(b.name));
            default:
                return data;
        }
    }

    function togglePinItem(e) {
        const itemElement = e.target.closest('.library-item');
        const itemId = itemElement.dataset.id;

        $.ajax({
            url: 'library/pin',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ itemId }),
            success: (data) => {
                if (data.success) {
                    itemElement.classList.toggle('pinned');
                    e.target.classList.toggle('text-primary');
                }
            },
            error: () => showError('Failed to toggle pin')
        });
    }

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
    async function displayLibraryContent(data, layout = 'grid', sort = 'recent') {
        // Sort data
        const sortedData = sortLibraryData(data, sort);

        // Load appropriate template
        const templateName = `library-${layout}`;
        await loadTemplate(templateName);
        const source = templateCache[templateName];
        const template = Handlebars.compile(source);

        mainContainer.html(template({ items: sortedData }));

        // Add event listeners for pin buttons
        document.querySelectorAll('.pin-btn').forEach(btn => {
            btn.addEventListener('click', togglePinItem);
        });
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
        const url = 'playlist';
        console.log('Loading playlist from:', url);  // Debug log

        mainContainer.html('<div class="col-12 text-center">Loading playlist...</div>');
        window.history.pushState({ view: 'playlist', playlistId }, '', `${URL_ROOT}/playlist`);

        $.ajax({
            url: url,
            dataType: 'json',
            success: (data) => {
                console.log('Playlist data received:', data); // Add this line
                if (data.error) {
                    showError(data.error);
                } else {
                    displayPlaylistContent(data);
                }
            },
            error: (xhr) => {
                const errorMsg = xhr.responseJSON?.error || 'Error loading playlist';
                showError(`${errorMsg} (Status: ${xhr.status})`);
            }
        });
    }

    // ======
    // MODALS
    // ======
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

    // ========================
    // PLAYLIST FUNCTIONALITES
    // ========================
    function createNewPlaylist() {
        $.ajax({
            url: 'create',
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


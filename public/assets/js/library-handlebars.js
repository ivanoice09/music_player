// library-handlers.js
$(document).ready(function () {

    // Reference the global functions
    const {
        loadLibraryView,
        loadPlaylistView,
        loadArtistView,
        loadAlbumView,
        showError,
        showToast
    } = window.LibraryFunctions || {};

    // ===================
    // LIBRARY ITEM CLICKS
    // ===================
    $(document).on('click', '.library-item', function (e) {
        // Don't trigger if clicking on pin button
        if ($(e.target).closest('.pin-btn').length) {
            return;
        }

        const itemId = $(this).data('id');
        const itemType = $(this).data('type');

        switch (itemType) {
            case 'playlist':
                loadPlaylistView(itemId);
                break;
            case 'album':
                loadAlbumView(itemId);
                break;
            case 'artist':
                loadArtistView(itemId);
                break;
            default:
                console.log('Unknown item type:', itemType);
        }
    });

    // =============
    // LAYOUT TOGGLE
    // =============
    $(document).on('click', '.layout-option', function () {
        const layout = $(this).data('layout');
        toggleLibraryLayout(layout);
    });

    function toggleLibraryLayout(layout) {
        // Save preference to localStorage
        localStorage.setItem('libraryLayoutPreference', layout);

        // Reload library with new layout
        loadLibraryView();
    }

    // =======
    // SORTING
    // =======
    $(document).on('click', '.sort-option', function () {
        const sortMethod = $(this).data('sort');
        sortLibrary(sortMethod);
    });

    function sortLibrary(sortMethod) {
        // Save preference to localStorage
        localStorage.setItem('librarySortPreference', sortMethod);

        // Reload library with new sort
        loadLibraryView();
    }

    // =====================
    // PINNING FUNCTIONALITY
    // =====================
    $(document).on('click', '.pin-btn', function (e) {
        e.stopPropagation();
        const itemElement = $(this).closest('.library-item');
        const itemId = itemElement.data('id');
        const isCurrentlyPinned = itemElement.hasClass('pinned');

        // Don't allow pinning/unpinning the default playlist
        if (itemElement.data('type') === 'playlist' &&
            itemElement.find('.card-title').text() === 'Favorites') {
            showToast('Default playlist cannot be unpinned');
            return;
        }

        $.ajax({
            url: 'library/pin',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                itemId: itemId,
                pinned: !isCurrentlyPinned
            }),
            success: (data) => {
                if (data.success) {
                    itemElement.toggleClass('pinned');
                    $(this).find('i').toggleClass('text-primary');
                    showToast(data.pinned ? 'Item pinned' : 'Item unpinned');
                }
            },
            error: () => showError('Failed to toggle pin status')
        });
    });

    //====================
    // Remove item handler
    //====================
    $(document).on('click', '.remove-item-btn', function (e) {
        e.stopPropagation();
        const itemElement = $(this).closest('.library-item');
        const itemId = itemElement.data('id');
        const itemType = itemElement.data('type');

        // Check if it's a default playlist
        if (itemElement.data('is-default')) {
            showToast('Default playlist cannot be removed');
            return;
        }

        if (confirm('Are you sure you want to remove this item?')) {
            $.ajax({
                url: 'library/remove',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ itemId }),
                success: (data) => {
                    if (data.success) {
                        itemElement.fadeOut(300, () => itemElement.remove());
                        showToast('Item removed');
                    }
                },
                error: () => showError('Failed to remove item')
            });
        }
    });
});
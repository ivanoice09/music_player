import { loadLibraryView } from "../views/library.js";
import { loadPlaylistView } from "../views/playlist.js";
import { loadArtistView } from "../views/artist.js";
import { loadAlbumView } from "../views/album.js";
import { showError, showToast } from "../core/error-handler.js";

// Item selection handler for library items
function setupLibraryItemClickHandlers() {
    $(document).on('click', '.library-item', function (e) {
        // Don't trigger if clicking on pin button
        if ($(e.target).closest('.pin-btn').length) {
            return;
        }

        const itemId = $(this).data('itemId'); // library_items.item_id (the actual playlist/album/artist ID)
        const itemType = $(this).data('type');

        // Log the itemId and itemType to see their values
        // console.log('Actual Reference ID:', itemId);
        // console.log('Item type:', itemType);

        switch (itemType) {
            case 'playlist':
                loadPlaylistView(itemId); // Pass the CORRECT ID (itemId, not libraryItemId)
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
}

// Change library layout handler
function toggleLibraryLayout(layout) {
    // Save preference to localStorage
    localStorage.setItem('libraryLayoutPreference', layout);
    // Reload library with new layout
    loadLibraryView();
}

function setupLibraryLayoutToggle() {
    $(document).on('click', '.layout-option', function () {
        const layout = $(this).data('layout');
        toggleLibraryLayout(layout);
    });
}

// Sorting library item handler
function sortLibrary(sortMethod) {
    // Save preference to localStorage
    localStorage.setItem('librarySortPreference', sortMethod);
    // Reload library with new sort
    loadLibraryView();
}

function setupLibrarySorting() {
    $(document).on('click', '.sort-option', function () {
        const sortMethod = $(this).data('sort');
        sortLibrary(sortMethod);
    });
}

function setupPinButton() {
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
}

function setupRemoveItemButton() {
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
}

export const libraryClickHandlers = {
    setupLibraryItemClickHandlers,
    setupLibraryLayoutToggle,
    setupLibrarySorting,
    setupPinButton,
    setupRemoveItemButton
}
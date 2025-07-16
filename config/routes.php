<?php
// Define routes
$routes = [

    // AUTH 
    'auth/register' => [
        'controller' => 'AuthController',
        'action' => 'register'
    ],
    'auth/login' => [
        'controller' => 'AuthController',
        'action' => 'login'
    ],
    'auth/logout' => [
        'controller' => 'AuthController',
        'action' => 'logout'
    ],
    'partials/authModal' => [
        'controller' => 'AuthController',
        'action' => 'authModal'
    ],

    // MAIN PAGE
    'main' => [
        'controller' => 'BaseController',
        'action' => 'loadView'
    ],

    // TRACKS
    'search' => [
        'controller' => 'TrackController',
        'action' => 'getAndShowSearchedTracks'
    ],
    'popular' => [
        'controller' => 'TrackController',
        'action' => 'getAndShowPopularTracks'
    ],

    // LIBRARY
    'library' => [
        'controller' => 'LibraryController',
        'action' => 'index'
    ],
    'library/add' => [
        'controller' => 'LibraryController',
        'action' => 'addItem'
    ],
    'library/remove' => [
        'controller' => 'LibraryController',
        'action' => 'removeItem'
    ],
    'library/pin' => [
        'controller' => 'LibraryController',
        'action' => 'pinItem'
    ],

    /**
     *  PLAYLISTS
     * 
     *  How my playlist flow works:
     * 
     *  1) user clicks on plus button "create playlist"
     * 
     *  2) the playlist/create route gets used to create
     *     a playlist and to return user's ID through javascript's event listener
     * 
     *  3) PlaylistController invokes create() method
     * 
     *  4) create() method passes the data to javascript's loadPlaylistView function
     * 
     *  5) loadPlaylistView function invokes the `playlist/${playlistId}`
     * 
     *  6) this `playlist/${playlistId}` route would show the playlist-view template
     */
    'playlists' => [
        'controller' => 'PlaylistController',
        'action' => 'getPlaylists'
    ],
    'playlist/create' => [
        'controller' => 'PlaylistController',
        'action' => 'createPlaylist'
    ],
    'playlist/(\d+)' => [
        'controller' => 'PlaylistController',
        'action' => 'showPlaylist'
    ],
    'playlist/add-song' => [
        'controller' => 'PlaylistController',
        'action' => 'addTrack'
    ],
    'playlist/remove-song' => [
        'controller' => 'PlaylistController',
        'action' => 'removeTrack'
    ],
    'playlist/update' => [
        'controller' => 'PlaylistController',
        'action' => 'updatePlaylist'
    ],

    // DEBBUGGING
    'debbug' => [
        'controller' => 'HomeController',
        'action' => 'index'
    ],

    // TEMPLATES
    'templates' => [
        'controller' => 'BaseController',
        'action' => 'loadTemplate'
    ],
];

// Helper functions
function redirect($page)
{
    header('location: ' . URL_ROOT . '/' . $page);
    exit();
}

function flash($name = '', $message = '', $class = 'alert alert-success')
{
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

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

    // MAIN PAGES ROUTES
    'main' => [
        'controller' => 'MusicController',
        'action' => 'loadView'
    ],
    'search' => [ // route for searching function
        'controller' => 'MusicController',
        'action' => 'getSearchedSongs'
    ],
    'popular' => [ // route for home page
        'controller' => 'MusicController',
        'action' => 'getPopularSongs'
    ],

    // DEBBUGGING
    'debbug' => [
        'controller' => 'HomeController',
        'action' => 'index'
    ]
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

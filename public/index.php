<?php
// Debugging setup
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define path constants
define('ROOT', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

// Load BaseController first (since other controllers extend it)
require_once ROOT . '/app/controllers/BaseController.php';
// Load configuration
require_once ROOT . '/config/config.php';
// Load routes
require_once ROOT . '/config/routes.php';

// Simple autoloader
/**
 * Why use autoloader?
 * Without one, you'd have to manually require or include every class file
 * before using it, which becomes messy and inefficient as your project grows.
 */
spl_autoload_register(function ($className) {

    // Make an array to define multiple directories where classes might be
    $directories = [
        ROOT . '/app/controllers/',
        ROOT . '/app/models/',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return; // Stop searching once found
        }
    }

    // If not found in any directory
    die("<!-- Debug: Class '$className' not found in any directory -->");
});

// Parse URL
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'main';
$url = filter_var($url, FILTER_SANITIZE_URL);

// Debugging output
error_log("Debug: Full URL: $url");

// Route handling
if (isset($routes[$url])) {
    $route = $routes[$url];
    $controllerName = $route['controller'];
    $actionName = $route['action'];

    // Load and execute the controller
    $controllerFile = ROOT . '/app/controllers/' . $controllerName . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $controllerName();
        $controller->$actionName();
    } else {
        die("Controller not found: $controllerFile");
    }
} else {
    // 404 - Route not found
    header("HTTP/1.0 404 Not Found");
    require_once ROOT . '/app/controllers/BaseController.php';
    $controller = new BaseController();
    $controller->view('error');
}

// Debugging output
error_log("Debug: Loading controller: $controllerName");
error_log("Debug: Calling action: $actionName");

// Load controller file
$controllerFile = ROOT . '/app/controllers/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Instantiate controller
    $controller = new $controllerName();

    // Call action
    if (method_exists($controller, $actionName)) {
        $controller->$actionName();
    } else {
        die("<!-- Debug: Action method not found: $actionName -->");
    }
} else {
    die("<!-- Debug: Controller file not found: $controllerFile -->");
}

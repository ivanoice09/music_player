<?php
// Debugging setup
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//======================
// Define path constants
//======================
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

// Load BaseController first (since other controllers extend it)
require_once ROOT . '/app/controllers/BaseController.php';
// Load configuration
require_once ROOT . '/config/config.php';
// Load routes
require_once ROOT . '/config/routes.php';

//==================
// Simple autoloader
//==================
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
        ROOT . '/app/service/',
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

//===============
// Route handling
//===============
$matched = false;
foreach ($routes as $pattern => $route) {
    // Convert route pattern to regex
    $regex = '#^' . str_replace('/', '\/', $pattern) . '$#';
    
    if (preg_match($regex, $url, $matches)) {
        $controllerName = $route['controller'];
        $actionName = $route['action'];
        
        // Debugging output
        error_log("Debug: Loading controller: $controllerName");
        error_log("Debug: Calling action: $actionName");

        // Load controller file
        $controllerFile = ROOT . '/app/controllers/' . $controllerName . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            // Instantiate controller
            $controller = new $controllerName();

            // Remove the full match (index 0) and keep only parameters
            array_shift($matches);
            
            // Call action with parameters
            if (method_exists($controller, $actionName)) {
                call_user_func_array([$controller, $actionName], $matches);
            } else {
                // Use BaseController directly for errors
                $baseController = new BaseController();
                $baseController->showErrorPage(404, 'Not Found', "Action method not found: $actionName");
            }
            
            $matched = true;
            break;
        } else {
            // Use BaseController directly for errors
            $baseController = new BaseController();
            $baseController->showErrorPage(500, 'Internal Server Error', "Controller file not found: $controllerFile");
        }
    }
}

if (!$matched) {
    // Use BaseController directly for 404 errors
    $baseController = new BaseController();
    $baseController->showErrorPage(404, 'Not Found');
}
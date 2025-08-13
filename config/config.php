<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'musyk_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Jamendo API configuration
define('JAMENDO_CLIENT_ID', 'c7db4016'); // Replace with your actual client ID
define('JAMENDO_BASE_URL', 'https://api.jamendo.com/v3.0');

// App configuration
define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', '//kovicmusic.kesug.com');
define('SITE_NAME', 'simple music player');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
<?php

class BaseController
{

    // This function is used to render views
    function view($view, $data = [])
    {
        extract($data);

        if ($this->isAjaxRequest()) {
            // For AJAX requests, only load the main content
            require_once APP_ROOT . '/app/views/' . $view . '.php';
        } else {
            // For full page loads
            require_once APP_ROOT . '/app/views/layouts/header.php';
            require_once APP_ROOT . '/app/views/' . $view . '.php';
            require_once APP_ROOT . '/app/views/partials/playerbar.php';
            require_once APP_ROOT . '/app/views/layouts/footer.php';
        }
    }

    // This function is used to load the main view 
    // and data for the music page
    public function loadView()
    {
        // Get search query if it exists
        $query = $_GET['q'] ?? '';

        // Prepare data for the view
        $data = [
            'is_music_page' => true,
            'query' => htmlspecialchars($query),
            'search_performed' => !empty($query)
        ];

        $this->view('layouts/main', $data);
    }

    // This function is used to load templates
    public function loadTemplate()
    {
        $template = $_GET['name'] ?? 'song-grid';
        $templatePath = APP_ROOT . "/app/views/templates/{$template}.php";

        if (file_exists($templatePath)) {
            header('Content-Type: text/html');
            readfile($templatePath);
            exit;
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Template not found.";
        }
    }

    // This function is used to check if the request is an AJAX request
    function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    // This function is used to handle errors and show error pages
    function showErrorPage($code, $title, $message = '')
    {
        http_response_code($code);
        $errorView = APP_ROOT . '/app/views/error.php';

        if (file_exists($errorView)) {
            require $errorView;
        } else {
            die("<h1>$code $title</h1><p>$message</p>");
        }
        exit;
    }
}

<?php

class BaseController
{
    public function __construct() {}

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

    function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

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

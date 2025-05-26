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
}

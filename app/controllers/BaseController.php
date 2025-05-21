<?php

class BaseController
{
    public function __construct() {}

    function view($view, $data = [])
    {
        require_once APP_ROOT . '/app/views/' . $view . '.php';
    }
}
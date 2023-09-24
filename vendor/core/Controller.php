<?php

namespace vendor\core;

abstract class Controller
{
    public $route = [];
    public $view = '';
    public $layout = '';
    public $vars = [];
    public $getParams = [];
    public $postParams = [];
    public $isAjax;

    public function __construct($route)
    {
        $this->route = $route;
        $this->view = empty($this->view) ? $route['action'] : $this->view;
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? true : false;
    }

    public function getView()
    {
        if (!$this->isAjax) {
            $vObj = new View($this->route, $this->layout, $this->view);
            $vObj->render($this->vars);
        } else {
            echo json_encode($this->vars);
        }
    }

    public function set($vars)
    {
        $this->vars = $vars;
    }
}

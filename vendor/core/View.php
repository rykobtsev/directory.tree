<?php

namespace vendor\core;

class View
{
    public $route = [];
    public $view = '';
    public $layout = '';

    public function __construct($route, $layout = '', $view = '')
    {
        $this->route = $route;

        if ($layout === false) {
            $this->layout = false;
        } else {
            $this->layout = $layout ?: LAYOUT;
        }

        $this->view = $view;
    }

    public function render($vars)
    {
        if (is_array($vars)) extract($vars);

        $fileView = APP . "/views/{$this->route['controller']}/{$this->view}.php";

        ob_start();

        if (file_exists($fileView)) {
            require $fileView;
        } else {
            echo "<p>Не найден вид <b>$fileView</b></p>";
        }

        $content = ob_get_clean();

        if ($this->layout !== false) {
            $fileLayout = APP . "/views/Layouts/{$this->layout}.php";

            if (file_exists($fileLayout)) {
                require $fileLayout;
            } else {
                echo "<p>Не найден шаблон <b>$fileLayout</b></p>";
            }
        }
    }
}

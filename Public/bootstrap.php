<?php
require_once 'settings/config.php';

use vendor\core\Router;

spl_autoload_register(function ($class) {

    $file = ROOT . '/' . str_replace('\\', '/', $class) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

Router::add('^$', ['controller' => 'Main', 'action' => 'index']);
Router::add('(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?');

Router::dispatch(QUERY);

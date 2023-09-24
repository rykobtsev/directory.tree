<?php

namespace vendor\core;

class Router
{
    private static $routes = [];
    private static $route = [];

    public static function add($regexp, $route = [])
    {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function getRoute()
    {
        return self::$route;
    }

    private static function matchRoute($url)
    {
        $url = trim($url, '/');

        foreach (self::$routes as $pattern => $route) {
            if (preg_match("#^$pattern#ui", $url, $matches)) {

                foreach ($matches as $k => $v) {
                    if (is_string($k)) {
                        $route[$k] = $v;
                    }
                }

                if (empty($route['action'])) {
                    $route['action'] = 'index';
                }

                $route['controller'] = self::upperCamelCase($route['controller']);
                self::$route = $route;
                return true;
            }
        }

        return false;
    }

    public static function dispatch($url)
    {
        $url = self::removeQueryString($url);

        if (self::matchRoute($url)) {
            $controller = 'app\controllers\\' . self::$route['controller'] . 'Controller';

            if (class_exists($controller)) {
                $cObj = new $controller(self::$route);
                $action = self::lowerCamelCase(self::$route['action']) . 'Action';

                if (method_exists($cObj, $action)) {
                    $cObj->$action();
                    $cObj->getView();
                } else {
                    echo "Метод <b>$controller::$action</b> не найден";
                }
            } else {
                echo "Контроллер <b>$controller</b> не найден";
            }
        } else {
            http_response_code(404);
            include('404.php');
        }
    }

    private static function upperCamelCase($name)
    {
        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));

        return $name;
    }

    private static function lowerCamelCase($name)
    {
        $name = lcfirst(self::upperCamelCase($name));

        return $name;
    }

    private static function removeQueryString($url)
    {
        if ($url) {
            $strPosition = stripos($url, "?");

            if ($strPosition === false) {
                $params = explode('&', $url, 2);

                if (strpos($params[0], '=') === false) {
                    $url = rtrim($params[0], '/');
                } else {
                    $url = '';
                }
            } else {
                $url = rtrim(mb_substr($url, 0, $strPosition), '/');
            }
        }

        return $url;
    }
}

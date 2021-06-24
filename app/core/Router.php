<?php

namespace app\core;

use \Exception;

class Router
{
    protected $routes = [];
    protected $params = [];

    public function __construct()
    {
        $routes = require 'app/config/routes.php';

        foreach ($routes as $key => $value) {
            $this->add($key, $value);
        }
    }

    public function add($route, $params)
    {
        $this->routes[$route] = $params;
    }

    public function coincidence()
    {
        $uri = strtok(trim($_SERVER['REQUEST_URI'], '/'), '?');
        foreach ($this->routes as $route => $params) {
            if (preg_match('#^' . $route . '$#', $uri, $matches)) {
                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    public function run()
    {
        $view = new View();

        if ($this->coincidence()) {
            $path = 'app\controllers\\' . ucfirst($this->params['controller']) . 'Controller';
            if (class_exists($path)) {
                $action = $this->params['action'] . 'Action';
                if (method_exists($path, $action)) {
                    $controller = new $path($this->params);
                    $controller->$action();
                } else {
                    $view->errorCode(404);
                    // throw new Exception('Action not found');
                }
            } else {
                $view->errorCode(404);
                // throw new Exception('Controller not found');
            }
        } else {
            $view->errorCode(404);
            // throw new Exception('Route not found');
        }
    }
}
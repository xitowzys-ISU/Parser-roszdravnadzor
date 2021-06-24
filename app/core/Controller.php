<?php

namespace app\core;

class Controller
{
    /**
     * Page Route
     *
     * @var array
     */
    public $route;

    public $view;

    /**
     * Constructor of the Сontroller class
     *
     * @param array $route
     */
    public function __construct($route)
    {
        $this->route = $route;
        $this->view = new View($route);
        $this->model = $this->loadModel($route['controller']);
    }

    /**
     * Loading a module object by name by the controller
     *
     * @param string $name
     * @return object
     */
    public function loadModel($name)
    {
        $path = 'app\models\\' . ucfirst($name);

        if (class_exists($path)) {
            return new $path;
        }
    }
}

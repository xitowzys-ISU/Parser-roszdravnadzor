<?php

namespace app\core;

use \Exception;

class View
{
    /**
     * Page Route
     *
     * @var array
     */
    public $route;

    /**
     * Path to the page body
     *
     * @var string
     */
    public $pathTemplate;

    /**
     * The template used in the application
     *
     * @var string
     */
    public $template;

    /**
     * Constructor of the View class
     *
     * @param array $route
     */
    public function __construct($route = [])
    {
        $this->route = $route;

        if (!empty($this->route)) {
            $this->pathTemplate = $route['controller'] . '/' . $route['action'];
        } else {
            $this->pathTemplate = NULL;
        }

        $this->template = TEMPLATE;
    }

    /**
     * Rendering a template to a page
     *
     * @param string $title
     * @param array $data
     * @return void
     */
    public function render($data = [], $body = '')
    {
        $data['{TEMPLATE_SRC}'] = '../' . TEMPLATES_DIR . TEMPLATE;

        $path = '';
        $content = '';

        if (empty($body) && $this->pathTemplate != NULL) {
            $body = TEMPLATES_DIR . $this->template  . $this->pathTemplate . '.tpl';
        } else {
            $body = TEMPLATES_DIR . $this->template  . $body . '.tpl';
        }

        $pagePath = [
            'header' => TEMPLATES_DIR . $this->template  . 'header' . '.tpl',
            'body' => $body,
            'footer' => TEMPLATES_DIR . $this->template . 'footer' . '.tpl'
        ];

        foreach ($pagePath as $key => $value) {
            if (file_exists($value)) {

                $path = file_get_contents($value);

                foreach ($data as $find => $replace) {
                    $path = str_replace($find, $replace, $path);
                }

                $path = preg_replace('/{.+}/', '', $path);

                $content .= $path;
            } else {
                throw new Exception('Template not found:' . $value);
                break;
            }
        }

        echo $content;
    }

    /**
     * Throw out the error and display it on the page
     *
     * @param integer $code
     * @return void
     */
    public function errorCode($code)
    {
        http_response_code($code);

        $data = [
            '{TITLE}' => 'Ошибка ' . $code
        ];

        $this->render($data, 'errors/' . $code);
    }
}
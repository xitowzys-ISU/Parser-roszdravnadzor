<?php
use app\core\Router;
use app\models\Table;

require 'app/config/config.php';
require 'app/lib/Dev.php';

spl_autoload_register(function ($class){
    $path = str_replace('\\', '/', $class . '.php');
    if (file_exists($path))
    {
        require $path;
    }
});
//
//$router = new Router();
$tb = new Table();
//$tb->setDate();
$tb->setName('fileNameTest');
 $tb->generateJSON();

//$router->run();


<?php

namespace app\core;

use app\models\UpdateData;

require 'app/config/config.php';

spl_autoload_register(function ($class) {
    $path = str_replace('\\', '/', $class . '.php');
    if (file_exists($path)) {
        require $path;
    }
});

$update = new UpdateData();
echo $update->updateDataWeek();

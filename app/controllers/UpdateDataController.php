<?php

namespace app\controllers;

use app\core\Controller;

class UpdateDataController extends Controller
{
    public function indexAction()
    {
        echo $this->model->getJSON();
    }
}

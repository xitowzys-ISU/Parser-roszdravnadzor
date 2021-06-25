<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Database;

class UpdateDataController extends Controller
{

    protected $json;

    public function indexAction()
    { 
        echo $this->model->checkTableDB();  
        // $this->json = $this->model->getJSON();

        // echo gettype($this->json);
    }
}

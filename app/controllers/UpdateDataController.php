<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Database;

class UpdateDataController extends Controller
{

    protected $json;

    public function indexAction()
    { 
        // if(!$this->model->checkTableDB())
        //     $this->model->createTableDB();
        // else
        //     echo "ok";

        debug($this->model->getJSON(NULL));

        // $this->model->getNumberRecordsYear();
    }
}

<?php

namespace app\controllers;

use app\core\Controller;

class UpdateDataController extends Controller
{

    protected $json;

    public function indexAction()
    {

        // Filling in the database
        if (!$this->model->checkTableDB()) {
            $this->model->createTableDB();
            $amountData = $this->model->getNumberRecordsPeriod("01-01-1990", date("Y-m-d"));

            $this->model->saveData($amountData);
            $this->model->addUniqueIndex();
        }
        $this->model->updateDataWeek();
    }
}

<?php

namespace app\controllers;

use app\core\Controller;
use \TypeError;

class UpdateDataController extends Controller
{

    protected $json;

    public function indexAction()
    {
        $data = require 'app/config/parser.php';

        // Filling in the database
        if (!$this->model->checkTableDB()) {
            $this->model->createTableDB();

            $amountData = $this->model->getNumberRecordsPeriod("01-01-1990", date("Y-m-d"));

            foreach ($amountData as $key => $value) {
                $numberPages = intval(ceil($value / $data['length']));

                for ($i = 0; $i < $numberPages; $i++) {
                    if ($i === 0)
                        $json = $this->model->getData($key);
                    else
                        $json = $this->model->getData($key, $data['length'] * $i);


                    if ($json === -1) {
                        sleep(30);
                        --$i;
                        continue;
                    }

                    if (array_key_exists('message', $json['data'])) {
                        sleep(30);
                        --$i;
                        continue;
                    }

                    $this->model->saveDataInDB($json);
                }
            }

            $this->model->deleteDuplicateRows();
        }
    }
}

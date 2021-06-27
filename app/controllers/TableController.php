<?php


namespace app\controllers;

use app\core\Controller;
use app\core\Database;

class TableController extends Controller
{
    protected $json;
    public function indexAction()
    {
        $this->data["{TITLE}"] = 'form';
//        $this->model-
    }

    // (Button в форме) -> MVC Table (вызвать генерацию json в TableModel -> отобразить json в url table)
}
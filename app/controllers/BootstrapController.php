<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Table;

class BootstrapController extends Controller
{
    public function indexAction()
    {
        $tb = new Table();

        if(isset($_POST['dateFrom']) && isset($_POST['dateTo']))
        {
            $tb->setDate($_POST);
            $tb->generateJSON('file');

            header("Content-type: application/json");
            header('Content-disposition: attachment; filename=file.json');

            session_start();
            unset($_SESSION['ls_sleep_test']);

            die();
        }

        if(isset($_POST['progBar']))
        {
            $tb->progress();
            die();
        }

        $this->data['{TITLE}'] = 'Регистрация';
        // $update = new UpdateDataController(['controller' => 'updatedata', 'action' => 'index']);

        // $update->indexAction();

        $this->view->render($this->data);
    }
}

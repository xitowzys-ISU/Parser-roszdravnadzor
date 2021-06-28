<?php

namespace app\controllers;

use app\core\Controller;

class BootstrapController extends Controller
{
    public function indexAction()
    {

        if(isset($_POST['dateFrom']))
        {
            echo json_encode(array('success' => 1));
            exit();
        }

        $this->data['{TITLE}'] = 'Регистрация';
        // $update = new UpdateDataController(['controller' => 'updatedata', 'action' => 'index']);

        // $update->indexAction();

        $this->view->render($this->data);
    }
}

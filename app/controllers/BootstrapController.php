<?php

namespace app\controllers;

use app\core\Controller;

class BootstrapController extends Controller
{
    public function indexAction()
    {
        $update = new UpdateDataController(['controller' => 'updatedata', 'action' => 'index']);

        $update->indexAction();
    }
}

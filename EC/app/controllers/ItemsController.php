<?php

class ItemsController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $this->view->disable(); 
        $this->response->setContentType('application/json'); 
        echo json_encode(["apple" => "aka"]);
    }

    public function showAction()
    {
        $this->view->disable(); 
        $this->response->setContentType('application/json'); 
        echo json_encode(["grape" => "perple"]);
    }

}


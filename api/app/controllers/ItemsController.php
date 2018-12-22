<?php

class ItemsController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $this->view->disable(); 
        $this->response->setContentType('application/json'); 
        echo json_encode(["apple" => "aka"]);
    }

    public function showAction($id)
    {
        $this->view->disable(); 

        $item = Items::findFirst($id);

        if ($item->name == null) {
            return json_encode([
                'status' => 'NOT-FOUND'
            ]);
        }

        // return $item->name;
       return json_encode([
            'status' => 'FOUND',
            'data'   => [
                'id'            => $item->id,
                'name'          => $item->name,
                'description'   => $item->description,
                'price'         => $item->price,
                'mime'          => $item->mime,
                'raw_data'      => base64_encode($item->raw_data)
            ]
        ]);
        
    }

    public function searchAction($name)
    {
        $this->view->disable(); 

        $items = Items::find('name LIKE %'.$name.'%');

        $data = [];

        foreach ($items as $item) {
            $data[] = [
                'id'    => $item->id,
                'name'  => $item->name,
                'price' => $item->price,
            ];
        }

        return json_encode($data);
    }

}


<?php

class ItemsController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $this->view->disable(); 
        $this->response->setContentType('application/json'); 
        echo json_encode(["apple" => "akk"]);
    }

    public function showAction($id)
    {
        $this->view->disable(); 

        $item = Items::findFirst($id);

        if ($item->name == null) {
            return json_encode(array(
                'status-code' => 200,
                'status' => 'NOT-FOUND',
            ));
        }

        // return $item->name;
       return json_encode([
            'status-code' => 200,
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

        $items = Items::find("name LIKE '%" . $name . "%'");
        

        if (count($items)<=0){
            return json_encode(array(
                'status-code' => 200,
                'status' => 'NOT-FOUND',
            ));
        }

        $data = [];

        foreach ($items as $item) {
            $data[] = [
                'id'    => $item->id,
                'name'  => $item->name,
                'price' => $item->price,
            ];
        }

        return json_encode(array(
            'status-code' => 200,
            'status' => 'FOUND',
            'data' => $data
        ));
    }

    public function addAction()
    {
        $this->view->disable(); 
        $request_item = $this->request->getJsonRawBody();

        $item = new Items();

        $item->name = $request_item->name;
        $item->description = $request_item->description;
        $item->price = $request_item->price;
        
        if ($request_item->img != null){
            // 与えられたファイルのMIME-typeを取得
            $mime = Checkimage::getMimeType(base64_decode($request_item->img));
            // mimetypeのエラーチェック
            try{
                Checkimage::checkMimeType($mime);
            } catch (RuntimeException $e) {
                return json_encode(array(
                    'status-code' => 415,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ));
            }
        
            // 画像ファイルの情報を取得
            $img_info = new ImageDataBase64($request_item->img);

            $item->mime = $img_info->getMime();
            $item->raw_data = base64_decode($request_item->img);

        }

        if ($item->save() === false) {
            // echo "Umh, We can't store robots right now: \n";

            // $messages = $robot->getMessages();

            // foreach ($messages as $message) {
            //     echo $message, "\n";
            // }
            return json_encode(array(
                'status-code' => 500,
                'status' => 'error',
                'message' => 'can not be saved'
            ));
        } else {
            return json_encode(array(
                'status-code' => 200,
                'status' => 'create',
                'name' => $item->name
            ));
        }
    }

    public function editAction($id)
    {
        $this->view->disable(); 

        $request_item = $this->request->getJsonRawBody();
        $item = Items::findFirst($id);

        $item->name = $request_item->name;
        $item->description = $request_item->description;
        $item->price = $request_item->price;
        
        if ($request_item->img != null){
            // 与えられたファイルのMIME-typeを取得
            $mime = Checkimage::getMimeType(base64_decode($request_item->img));
            // mimetypeのエラーチェック
            try{
                Checkimage::checkMimeType($mime);
            } catch (RuntimeException $e) {
                return json_encode(array(
                    'status-code' => 415,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ));
            }
        
            // 画像ファイルの情報を取得
            $img_info = new ImageDataBase64($request_item->img);

            $item->mime = $img_info->getMime();
            $item->raw_data = base64_decode($request_item->img);

        }

        if ($item->save() === false) {
            // echo "Umh, We can't store robots right now: \n";

            // $messages = $robot->getMessages();

            // foreach ($messages as $message) {
            //     echo $message, "\n";
            // }
            return json_encode(array(
                'status-code' => 500,
                'status' => 'error',
                'message' => 'can not be saved'
            ));
        } else {
            return json_encode(array(
                'status-code' => 200,
                'status' => 'update',
                'name' => $item->name
            ));
        }
    }

}


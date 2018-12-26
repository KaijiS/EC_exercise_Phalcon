<?php

class ItemsController extends \Phalcon\Mvc\Controller
{

    // public function indexAction()
    // {

    // }

    public function showAction($id)
    {
        $this->view->disable(); 

        $item = Items::findFirst($id);

        if ($item->name == null) {
            return $this->responseJson($status_code = 200, $status = 'NOT-FOUND');
        }

        $data = array(
            'id'            => $item->id,
            'name'          => $item->name,
            'description'   => $item->description,
            'price'         => $item->price,
            'mime'          => $item->mime,
            'raw_data'      => base64_encode($item->raw_data)
        );

        return $this->responseJson($status_code = 200, $status = 'FOUND', $data = $data);
    }

    public function searchAction($name)
    {
        $this->view->disable(); 

        $items = Items::find("name LIKE '%" . $name . "%'");
        

        if (count($items)<=0){
            return $this->responseJson($status_code = 200, $status = 'NOT-FOUND');
        }

        $data = [];

        foreach ($items as $item) {
            $data[] = [
                'id'    => $item->id,
                'name'  => $item->name,
                'price' => $item->price,
            ];
        }

        return $this->responseJson($status_code = 200, $status = 'FOUND', $data = $data);
    }

    public function addAction()
    {
        $this->view->disable(); 
        $request_item = $this->request->getJsonRawBody();
        
        // リクエストパラメータの(json)の内容の確認
        if (! $this->checkJsonElements($request_item)){
            return $this->responseJson($status_code = 400, $status = 'query parameter error', $error_message = 'The elements of json are bad');
        }

        $item = new Items();

        $item->name = $request_item->name;
        $item->description = $request_item->description;
        $item->price = $request_item->price;

        if (!(array_key_exists('img', $request_item))){
            $request_item->img = null;
        }
        if ($request_item->img != null){
            // 与えられたファイルのMIME-typeを取得
            $mime = Checkimage::getMimeType(base64_decode($request_item->img));
            // mimetypeのエラーチェック
            try{
                Checkimage::checkMimeType($mime);
            } catch (RuntimeException $e) {
                return $this->responseJson($status_code = 415, $status = 'error', $error_message = $e->getMessage());
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
            return $this->responseJson($status_code = 500, $status = 'error', $error_message = 'can not be created');

        } else {
            return $this->responseJson($status_code = 200, $status = 'create', $name = $item->name);
        }
    }

    public function editAction($id)
    {
        $this->view->disable(); 

        $request_item = $this->request->getJsonRawBody();

        // リクエストパラメータの(json)の内容の確認
        if (! $this->checkJsonElements($request_item)){
            return $this->responseJson($status_code = 400, $status = 'query parameter error', $error_message = 'The elements of json are bad');
        }

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
                return $this->responseJson($status_code = 415, $status = 'error', $error_message = $e->getMessage());
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
            return $this->responseJson($status_code = 500, $status = 'error', $error_message = 'can not be edited');

        } else {
            return $this->responseJson($status_code = 200, $status = 'update', $name = $item->name);
        }
    }

    public function deleteAction($id)
    {
        $this->view->disable(); 

        $item = Items::findFirst($id);

        if ($item !== false) {
            if ($item->delete() === false) {
                // echo "Sorry, we can't delete the robot right now: \n";
        
                // $messages = $robot->getMessages();
        
                // foreach ($messages as $message) {
                //     echo $message, "\n";
                return $this->responseJson($status_code = 500, $status = 'error', $error_message = 'can not be deleted');

            } else {
                return $this->responseJson($status_code = 200, $status = 'delete', $name = $item->name);
            }
        }
    }


    public function route404Action()
    {
        return $this->responseJson($status_code = 404, $status = 'NOT-FOUND', $error_message = 'Not found route');
    }

    private function checkJsonElements($request)
    {
        if ((array_key_exists('name',        $request)) && 
            (array_key_exists('description', $request)) && 
            (array_key_exists('price',       $request)))
        {
            return true;
        } else{
            return false;
        }
    }

    private function responseJson($status_code, $status, $data = null, $name = null, $error_message = null)
    {
        if ($status_code == 200) {
            if ($data==null and $name){
                return json_encode(array(
                    'status-code' => $status_code,
                    'status' => $status,
                    'name' => $$name
                ));
            } else if ($data and $name==null){
                return json_encode(array(
                    'status-code' => $status_code,
                    'status' => $status,
                    'data' => $data
                ));
            } else if ($data==null and $name==null){
                return json_encode(array(
                    'status-code' => $status_code,
                    'status' => $status
                ));
            }
        } else {
            return json_encode(array(
                'status-code' => $status_code,
                'status' => $status,
                'message' => $error_message
            ));
        }
        
    }

}


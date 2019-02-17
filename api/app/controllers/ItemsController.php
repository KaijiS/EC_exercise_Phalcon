<?php

// class ItemsController extends \Phalcon\Mvc\Controller
class ItemsController extends ControllerBase
{

    /**
     * 商品情報一覧を，jsonで返す
     *
     * @param
     * @return json {status-code:int, status:steing, data:array, message:array:string}
     */
    public function indexAction()
    {
        // APIなので画面に表示させない
        $this->view->disable(); 

        // 指定したIDの商品情報を取得
        $items = Items::find();

        // 指定したIDがデータベースに存在しなければ，その旨をjsonで返す
        if (count($items)==0) {
            $this->response->setStatusCode(200);
            return json_encode(array(
                'status-code' => 200,
                'status'      => 'NOT-FOUND',
                'data'        => NULL,
                'message'     => NULL
            ));
        }

        $data = [];

        // Traversing with a foreach
        foreach ($items as $item) {
            // 情報があればjsonに埋め込んで返す
            $data[] = array(
                'id'            => $item->id,
                'name'          => $item->name,
                'description'   => $item->description,
                'price'         => $item->price,
            );
        }
        $this->response->setStatusCode(200);
        return json_encode(array(
            'status-code' => 200,
            'status'      => 'FOUND',
            'data'        => $data,
            'message'     => NULL
        ));
    }


    /**
     * 指定したIDの商品情報を取得し，jsonで返す
     *
     * @param $id
     * @return json {status-code:int, status:steing, data:array, message:array:string}
     */
    public function showAction($id)
    {
        // APIなので画面に表示させない
        $this->view->disable(); 

        // 指定したIDの商品情報を取得
        $item = Items::findFirst($id);

        // 指定したIDがデータベースに存在しなければ，その旨をjsonで返す
        if ($item->name == null) {
            $this->response->setStatusCode(200);
            return json_encode(array(
                'status-code' => 200,
                'status'      => 'NOT-FOUND',
                'data'        => NULL,
                'message'     => NULL
            ));
        }

        // 情報があればjsonに埋め込んで返す
        $data = array(
            'id'            => $item->id,
            'name'          => $item->name,
            'description'   => $item->description,
            'price'         => $item->price,
            'mime'          => $item->mime,
            'raw_data'      => base64_encode($item->raw_data)
        );
        $this->response->setStatusCode(200);
        return json_encode(array(
            'status-code' => 200,
            'status'      => 'FOUND',
            'data'        => $data,
            'message'     => NULL
        ));
    }


    /**
     * 商品名で検索し，ヒットした商品のid，名前，価格をjsonで返す
     *
     * @param $name
     * @return json {status-code:int, status:steing, data:array, message:array:string}
     */
    public function searchAction($name)
    {
        // APIなので画面に表示させない
        $this->view->disable(); 

        // 引数の$nameの文字列を含んでいる商品を検索し，一覧を取得
        $items = Items::find("name LIKE '%" . $name . "%'");
        
        // 検索した結果，なかったらその旨をjsonで返す
        if (count($items)<=0){
            $this->response->setStatusCode(200);
            return json_encode(array(
                'status-code' => 200,
                'status'      => 'NOT-FOUND',
                'data'        => NULL,
                'message'     => NULL
            ));
        }

        // 検索した結果、あったらそれらの情報を整理し，jsonに埋め込んで返す
        $this->response->setStatusCode(200);
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
            'status'      => 'FOUND',
            'data'        => $data,
            'message'     => NULL
        ));
    }


    /**
     * 商品情報を追加する
     *
     * @param
     * @return json {status-code:int, status:steing, data:array, message:array:string}
     */
    public function addAction()
    {
        // APIなので画面に表示させない
        $this->view->disable(); 

        // ヘッダーを取得
        $header = $this->request->getHeaders();

        // "Authorization"のチェック
        $check_json  = $this->checkAuth($header["Authorization"]);

        // チェックで引っかかればその旨をjsonで返す
        if (json_decode($check_json,true)['status-code'] != null){
            return $check_json;
        }


        // json形式のリクエストパラメータを受け取る
        $request_item = $this->request->getJsonRawBody();
        
        // リクエストパラメータの(json)の内容の確認し， 形式が誤っていれば，その旨をjsonで返す
        if (! $this->checkJsonElements($request_item)){
            $this->response->setStatusCode(400);
            return json_encode(array(
                'status-code' => 400,
                'status'      => 'query parameter error',
                'data'        => NULL,
                'message'     => array('The elements of json are bad',)
            ));
        }

        // まずは必須情報(商品名，説明文，価格)を Itemのインスタンスにセット
        $item               = new Items();
        $item->name         = $request_item->name;
        $item->description  = $request_item->description;
        $item->price        = $request_item->price;

        // 任意項目である"img"がjson内に存在しなければ，nullを代入し追加
        if (!(array_key_exists('img', $request_item))){
            $request_item->img = null;
        }

        // 任意項目である"img"がnullでなければ，すなわち画像情報を受け取っていれば以下を実行する
        if ($request_item->img != null){

            // 与えられたファイルのMIME-typeを取得
            $mime = Checkimage::getMimeType(base64_decode($request_item->img));

            // mimetypeのエラーチェック，画像系のmimeでなければ，その旨をjsonで返す
            try{
                Checkimage::checkMimeType($mime);
            } catch (RuntimeException $e) {
                $this->response->setStatusCode(415);
                return json_encode(array(
                    'status-code' => 415,
                    'status'      => 'error',
                    'data'        => NULL,
                    'message'     => array($e->getMessage())
                ));
            }
        
            // 画像ファイルの情報を取得
            $img_info = new ImageDataBase64($request_item->img);

            // 画像の情報をそれぞれ，Itemのインスタンスにセットする
            $item->mime     = $img_info->getMime();
            $item->raw_data = base64_decode($request_item->img);

        }

        // データベースへ保存を行う
        if ($item->save() === false) {
            // 保存が失敗した時，バリデーションエラーの場合はその旨をjsonで返し，それ以外の例外時もその旨をjsonで返す
            $messages = $item->getMessages();
            if(count($messages) > 0){
                $this->response->setStatusCode(400);
                $errors = [];
                foreach ($messages as $message) {
                    $errors[] = $message->getMessage();
                }
                return json_encode(array(
                    'status-code' => 400,
                    'status'      => 'error',
                    'data'        => NULL,
                    'message'     => $errors
                ));
            }
            $this->response->setStatusCode(500);
            return json_encode(array(
                'status-code' => 500,
                'status'      => 'error',
                'data'        => NULL,
                'message'     => array('can not be created')
            ));

        } else {
            // 保存が成功した場合は，保存した商品名をjsonに埋め込み返す
            $this->response->setStatusCode(201, 'Created');
            return json_encode(array(
                'status-code' => 201,
                'status'      => 'create',
                'data'        => array('name'=>$item->name),
                'message'     => NULL,
            ));
        }
    }


    /**
     * 商品情報を編集する
     *
     * @param $id
     * @return json {status-code:int, status:steing, data:array, message:array:string}
     */
    public function editAction($id)
    {
        // APIなので画面に表示させない
        $this->view->disable(); 

        // ヘッダーを取得
        $header = $this->request->getHeaders();

        // "Authorization"のチェック
        $check_json  = $this->checkAuth($header["Authorization"]);

        // チェックで引っかかればその旨をjsonで返す
        if (json_decode($check_json,true)['status-code'] != null){
            return $check_json;
        }

        // json形式のリクエストパラメータを受け取る
        $request_item = $this->request->getJsonRawBody();

        // リクエストパラメータの(json)の内容の確認し， 形式が誤っていれば，その旨をjsonで返す
        if (! $this->checkJsonElements($request_item)){
            $this->response->setStatusCode(400);
            return json_encode(array(
                'status-code' => 400,
                'status'      => 'query parameter error',
                'data'        => NULL,
                'message'     => array('The elements of json are bad')
            ));
        }

        // まずは必須情報(商品名，説明文，価格)を 編集元の情報($idで取得)に上書き
        $item               = Items::findFirst($id);
        $item->name         = $request_item->name;
        $item->description  = $request_item->description;
        $item->price        = $request_item->price;
        
        // 任意項目である"img"がnullでなければ，すなわち画像情報を受け取っていれば以下を実行する
        if ($request_item->img != null){

            // 与えられたファイルのMIME-typeを取得
            $mime = Checkimage::getMimeType(base64_decode($request_item->img));

            // mimetypeのエラーチェック，画像系のmimeでなければ，その旨をjsonで返す
            try{
                Checkimage::checkMimeType($mime);
            } catch (RuntimeException $e) {
                $this->response->setStatusCode(415);
                return json_encode(array(
                    'status-code' => 415,
                    'status'      => 'error',
                    'data'        => NULL,
                    'message'     => array($e->getMessage())
                ));
            }
        
            // 画像ファイルの情報を取得
            $img_info = new ImageDataBase64($request_item->img);

            // 画像の情報をそれぞれ，編集元の情報に上書き
            $item->mime     = $img_info->getMime();
            $item->raw_data = base64_decode($request_item->img);

        }

        // データベースへ保存を行う
        if ($item->save() === false) {
            // 保存が失敗した時，バリデーションエラーの場合はその旨をjsonで返し，それ以外の例外時もその旨をjsonで返す
            $messages = $item->getMessages();
            if(count($messages) > 0){
                $this->response->setStatusCode(400);
                $errors = [];
                foreach ($messages as $message) {
                    $errors[] = $message->getMessage();
                }
                return json_encode(array(
                    'status-code' => 400,
                    'status'      => 'error',
                    'data'        => NULL,
                    'message'     => $errors
                ));
            }
            $this->response->setStatusCode(500);
            return json_encode(array(
                'status-code' => 500,
                'status'      => 'error',
                'data'        => NULL,
                'message'     => array('can not be edited')
            ));

        } else {
            // 保存が成功した場合は，保存した商品名をjsonに埋め込んで返す
            $this->response->setStatusCode(200);
            return json_encode(array(
                'status-code' => 200,
                'status'      => 'update',
                'data'        => array('name'=>$item->name),
                'message'     => NULL
            ));
        }
    }


    /**
     * 商品情報を削除する
     *
     * @param $id
     * @return json {status-code:int, status:steing, data:array, message:array:string}
     */
    public function deleteAction($id)
    {
        // APIなので画面に表示させない
        $this->view->disable(); 

        // ヘッダーを取得
        $header = $this->request->getHeaders();

        // "Authorization"のチェック
        $check_json  = $this->checkAuth($header["Authorization"]);

        // チェックで引っかかればその旨をjsonで返す
        if (json_decode($check_json,true)['status-code'] != null){
            return $check_json;
        }

        // 指定したIDの商品情報を取得
        $item = Items::findFirst($id);

        // 指定したIDがデータベースに存在しなければ，その旨をjsonで返す
        if ($item->name == null) {
            $this->response->setStatusCode(500);
            return json_encode(array(
                'status-code' => 500,
                'status'      => 'NOT-FOUND',
                'data'        => NULL,
                'message'     => array('this id is nothig')
            ));
        }

        // データベースからの削除を行う
        if ($item->delete() === false) {
            // 削除が失敗すれば，その旨をjsonで返す
            $this->response->setStatusCode(500);
            return json_encode(array(
                'status-code' => 500,
                'status'      => 'error',
                'data'        => NULL,
                'message'     => array('can not be deleted')
            ));

        } else {
            // 削除が成功した場合は，保存した商品名をjsonに埋め込んで返す
            $this->response->setStatusCode(200);
            return json_encode(array(
                'status-code' => 200,
                'status'      => 'delete',
                'data'        => NULL,
                'message'     => NULL
            ));
        }
    }


    /**
     * リクエストパラメータ中の必須項目があるか否かを判定
     *
     * @param  json形式のリクエストパラメータのから変換された連想配列
     * @return bool
     */
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
    

}


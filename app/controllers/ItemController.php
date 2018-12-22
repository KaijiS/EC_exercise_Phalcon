<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        // $this->request->get('user_id');
        // echo '<h1>Hello!</h1>';
        $this->view->disable();
        $this->response->setContentType('application/json'); 
        $this->response->setJsonContent(
            [
                'status' => 'OK'
            ]
        );
        return $this->response;
        // echo json_encode([
        //     'status' => 'OK'
        // ]); 
        
        // try {
        //     print_r("kkk");
        // } catch (ServiceException $e) {
        //     throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        // }

        return "sss";
    }

    public function show($id)
    {   
        $this->response->setJsonContent(
            [
                'status' => 'OK'
            ]
        );
        return $this->response;
        echo 'wwwww';
    }

    public function add()
    {
        echo '<h1>He</h1>';
    }

    public function edit($id)
    {
        echo '<h1>lo!</h1>';
    }

    public function delete($id)
    {
        echo '<h1>H</h1>';
    }

    public function search()
    {
        echo '<h1>Helo!</h1>';
    }
}



/* 
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Response;

require_once __DIR__."/ImageDataInfo/ImageData.php";


// Loader() を使ってモデルをオートロード
$loader = new Loader();

$loader->registerNamespaces(
    [
        'EC\Products' => __DIR__ . '../models/',
    ]
);

$loader->register();

$di = new FactoryDefault();

// データベースサービスのセットアップ
$di->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'dbname'   => 'EC',
            ]
        );
    }
);

// DI を作成し、アプリケーションにバインド
$app = new Micro($di);



// ここからはそれぞれのルートを定義

// 全ての item を取得
// $app->get(
//     '/api/items',
//     function () {
//         // 全 item を取得する操作
//     }
// );



// itemの検索(その名前を $name で検索)
$app->get(
    '/api/items/search/{name}',
    function ($name) use ($app) {
        $phql = 'SELECT * FROM EC\Products\Items WHERE name LIKE :name: ORDER BY name';

        $items = $app->modelsManager->executeQuery(
            $phql,
            [
                'name' => '%' . $name . '%'
            ]
        );

        $data = [];

        foreach ($items as $item) {
            $data[] = [
                'id'    => $item->id,
                'name'  => $item->name,
                'price' => $item->price,
            ];
        }

        echo json_encode($data);
    }
);


// プライマリーキーでitemを取得
$app->get(
    '/api/items/{id:[0-9]+}',
    function ($id) use ($app) {
        $phql = 'SELECT * FROM EC\Products\Items WHERE id = :id:';

        $item = $app->modelsManager->executeQuery(
            $phql,
            [
                'id' => $id,
            ]
        )->getFirst();

        // レスポンスを作成
        $response = new Response();

        if ($item === false) {
            $response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } else {
            $response->setJsonContent(
                [
                    'status' => 'FOUND',
                    'data'   => [
                        'id'            => $item->id,
                        'name'          => $item->name,
                        'description'   => $item->description,
                        'price'         => $item->price,
                        'mime'          => $item->mime,
                        'raw_data'      => base64_encode($item->raw_data)
                    ]
                ]
            );
        }
        return $response;
    }
);





// 新しい item の追加
$app->post(
    '/api/items',
    function () use ($app) {
        $item = $app->request->getJsonRawBody();


        // レスポンスの作成
        $response = new Response();


        // 与えられたファイルのMIME-typeを取得
        $mime = getMimeType(base64_decode($item->img));
        // mimetypeのエラーチェック
        try{
            checkMimeType($mime);
        } catch (RuntimeException $e) {
            // HTTP ステータスの変更
            $response->setStatusCode(415, 'Unsupported Media Type');
            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $e->getMessage(),
                ]
            );
            return $response;
        }


        // 画像ファイルの情報を取得
        $img_info = new ImageDataBase64($item->img);
        

        $phql = 'INSERT INTO EC\Products\Items (name, description, price, mime, raw_data) VALUES (:name:, :description:, :price:, :mime:, :raw_data:)';
        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                'name'          => $item->name,
                'description'   => $item->description,
                'price'         => $item->price,
                'mime'          => $img_info->getMime(),
                'raw_data'      => base64_decode($item->img),
            ]
        );


        // 挿入が成功したかを確認
        if ($status->success() === true) {
            // HTTPステータスの変更
            $response->setStatusCode(201, 'Created');

            $item->id = $status->getModel()->id;

            $response->setJsonContent(
                [
                    'status' => 'OK',
                    'data'   => $item,
                ]
            );
        } else {
            // HTTPステータスの変更
            $response->setStatusCode(409, 'Conflict');

            // クライアントにエラーを送信
            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors,
                ]
            );
        }

        return $response;
    }
);




// プライマリーキーで指定した item を更新する
$app->put(
    '/api/items/{id:[0-9]+}',
    function ($id) use ($app) {
        $item = $app->request->getJsonRawBody();

        // レスポンスの作成
        $response = new Response();


        // 与えられたファイルのMIME-typeを取得
        $mime = getMimeType(base64_decode($item->img));
        // mimetypeのエラーチェック
        try{
            checkMimeType($mime);
        } catch (RuntimeException $e) {
            // HTTP ステータスの変更
            $response->setStatusCode(415, 'Unsupported Media Type');
            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $e->getMessage(),
                ]
            );
            return $response;
        }

        // 画像ファイルの情報を取得
        $img_info = new ImageDataBase64($item->img);

        $phql = 'UPDATE EC\Products\Items SET name = :name:, description = :description:, price = :price:, mime = :mime:, raw_data = :raw_data: WHERE id = :id:';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                'id'            => $id,
                'name'          => $item->name,
                'description'   => $item->description,
                'price'         => $item->price,
                'mime'          => $img_info->getMime(),
                'raw_data'      => base64_decode($item->img),
            ]
        );


        // この挿入が成功したか確認する
        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    'status' => 'OK'
                ]
            );
        } else {
            // HTTP ステータスの変更
            $response->setStatusCode(409, 'Conflict');

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors,
                ]
            );
        }

        return $response;
    }
);

// プライマリーキーによってitemを削除する
$app->delete(
    '/api/items/{id:[0-9]+}',
    function ($id) use ($app) {
        $phql = 'DELETE FROM EC\Products\Items WHERE id = :id:';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                'id' => $id,
            ]
        );

        // レスポンスの作成
        $response = new Response();

        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    'status' => 'OK'
                ]
            );
        } else {
            // HTTPステータスの変更
            $response->setStatusCode(409, 'Conflict');

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors,
                ]
            );
        }

        return $response;
    }
);

$app->handle();

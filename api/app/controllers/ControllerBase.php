<?php

// use Phalcon\Mvc\Controller;

// class ControllerBase extends Controller
class ControllerBase extends \Phalcon\Mvc\Controller
{
    /**
     * ルーティング設定の中の定義されていないルートにアクセスした場合，その旨をjsonで返す
     *
     * @param 
     * @return json {status-code:int, status:steing, data:array, message:array:string}
     */
    public function route404Action()
    {
        $this->response->setStatusCode(404);
        return json_encode(array(
            'status-code' => 404,
            'status'      => 'NOT-FOUND',
            'data'        => NULL,
            'message'     => array('Not found route')
        ));
    }


    /**
     * ヘッダに付加されたAuth情報のチェック(あるか否か，不正なアクセスか否か)を行う
     *
     * @param $auth:string ヘッダに付加されたAuth情報
     * @return json {status-code:int, status:steing, data:array, message:array:string}
     */
    public function checkAuth($auth)
    {
        // Auth情報があるか否かをチェック　ないならjsonで返す
        if (!isset($auth) || empty($auth)) {
            $this->response->setStatusCode(403, "Forbidden");
            return json_encode(array(
                'status-code' => 403,
                'status'      => 'Forbidden',
                'data'        => NULL,
                'message'     => array('Auth is not included in the header')
            ));
        }

        // 先頭の"Bearer"を外す
        $token = explode(" ", $auth);
        $jwt = trim($token[1], '"');

        // jwtをデコードしてチェック　ダメならjsonでその旨を返す
        try {
            Firebase\JWT\JWT::$leeway = 60; // 60 seconds
            $user_info = Firebase\JWT\JWT::decode($jwt, $this->config->key, array('HS256'));
        } catch (Firebase\JWT\ExpiredException $e) {
            $this->response->setStatusCode(405, $e->getMessage());
            return json_encode(array(
                'status-code' => 405,
                'status'      => 'error',
                'data'        => NULL,
                'message'     => array($e->getMessage())
            ));
        }

        // 必要であれば以下のコメントアウトの部分でデータベースとの照合を行う

        // // 変数$github_idであるユーザを取得
        // $user = Users::findFirst("oauth_id = '" . $user_info["github_id"] . "'"); // ここがうまく機能しない。。。
        // // $user = array();

        // // 指定したIDがデータベースに存在しなければ，その旨をjsonで返す
        // if ($user->name == null) {
        //     $this->response->setStatusCode(404);
        //     return json_encode(array(
        //         'status-code' => 404,
        //         'status'      => 'NOT-FOUND',
        //         'data'        => NULL,
        //         'message'     => 'not found user in db'
        //     ));
        // }
        
        return json_encode(array(
            'status-code' => NULL,
            'status'      => NULL,
            'data'        => NULL,
            'message'     => NULL
        ));
    }
    

}

<?php

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

class UsersController extends ControllerBase
{

    // public function indexAction()
    // {

    // }


    /**
     * JWTを生成
     *
     * @param 
     * @return json {status-code:int, status:string, token:string}
     */
    public function getJWTAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            // ユーザ情報を受け取る
            $request_user = $this->request->getJsonRawBody();
            $request_user_array = [
                'name'      => $request_user->name,
                'github_id' => $request_user->id
            ];

            // ユーザ検索
            $find_user_success = $this->searchUser($request_user_array['github_id']);
            if( ! $find_user_success )
            {
                // なければユーザ登録(とりあえず)
                $return_status = $this->registerUser($request_user_array);
                if ( $return_status['status-code'] != 200 )
                {
                    return json_encode(array(
                        'status-code' => $return_status['status-code'],
                        'status'      => 'error',
                        'token'       => NULL
                    ));
                }
                $a=1;
            }

            // jwtを作成
            $tokenId    = base64_encode(random_bytes(32));
            $issuedAt   = time();                   // 発行時刻：トークンが生成された時刻
            $notBefore  = $issuedAt + 1;             //Adding 1 seconds
            $expire     = $notBefore + 3600;            // Adding 3600 seconds トークンの有効期間
            $serverName = $this->config->serverName; // 発行者
            $data = [
                'iat'  => $issuedAt,         // 発行時刻：トークンが生成された時刻
                'jti'  => $tokenId,          // JsonトークンID：トークンの一意の識別子
                'iss'  => $serverName,       // 発行者
                'nbf'  => $notBefore,        // Not before
                'exp'  => $expire            // トークンの有効期間
            ];

            $jwt = Firebase\JWT\JWT::encode($data + $request_user_array, $this->config->key);

            return json_encode(array(
                'status-code' => 200,
                'status'      => 'success',
                'token'       => $jwt
            ));
        }

    }

    /**
     * データベース内から対象ユーザを検索し存在するか否かを返す
     *
     * @param githubのoauth認証で得られたid
     * @return boolean
     */
    private function searchUser($github_id)
    {
        // APIなので画面に表示させない
        $this->view->disable();

        // 引数の$github_idであるユーザを取得
        $user = Users::findFirst("oauth_id = '" . $github_id . "'");

        // 指定したIDがデータベースに存在しなければ，その旨をjsonで返す
        if ($user->name == null) {
            return false;
        }
        
        return true;
    }


    /**
     * データベースに対象ユーザを登録
     *
     * @param githubのoauth認証で得られたid
     * @return array(status-code:int, status:string data:array, message:array:string)
     */
    private function registerUser($request_user)
    {
        // APIなので画面に表示させない
        $this->view->disable();

        // ユーザ情報を Usersのインスタンスにセット
        $user               = new Users();
        $user->name         = $request_user['name'];
        $user->oauth_name   = "github";
        $user->oauth_id     = $request_user['github_id'];


        // データベースへ保存を行う
        if ($user->save() === false) {
            // 保存が失敗した時，バリデーションエラーの場合はその旨をで返す，それ以外の例外時もその旨を返す
            $messages = $user->getMessages();
            if(count($messages) > 0){
                $errors = [];
                foreach ($messages as $message) {
                    $errors[] = $message->getMessage();
                }
                return array('status-code'=>400, 'status'=>'error', 'data'=>NULL, 'messages'=>$errors);
            }
            return array('status-code'=>500, 'status'=>'error', 'data'=>NULL, 'messages'=>array('can not be created'));

        } else {
            // 保存が成功した場合は，保存したユーザ名をjsonに埋め込み返す
            return array('status-code'=>200, 'status'=>'create', 'data'=>array('name'=>$user->name), 'messages'=>NULL);
        }
    }

}
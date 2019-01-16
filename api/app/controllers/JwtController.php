<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;

class JwtController extends ControllerBase
{

    public function indexAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) {
            // ユーザ情報を受け取る
            $request_user = $this->request->getJsonRawBody();

            $config = include APP_PATH . "/app/config/config.php";

            // jwtを作成
            $tokenId    = base64_encode(random_bytes(32));
            $issuedAt   = time();                   // 発行時刻：トークンが生成された時刻
            $notBefore  = $issuedAt + 1;             //Adding 1 seconds
            $expire     = $notBefore + 10;            // Adding 10 seconds トークンの有効期間
            // $serverName = $_SERVER['SERVER_NAME'];
            $serverName = $config->serverName; // 発行者
            $data = [
                'iat'  => $issuedAt,         // 発行時刻：トークンが生成された時刻
                'jti'  => $tokenId,          // JsonトークンID：トークンの一意の識別子
                'iss'  => $serverName,       // 発行者
                'nbf'  => $notBefore,        // Not before
                'exp'  => $expire            // トークンの有効期間
            ];
            

            $this->response->setJsonContent(array(
                'status-code' => 200,
                'status'      => 'success',
                "token" => JWT::encode($data + $request_user, $config->key)
            ));
        }
    }

}


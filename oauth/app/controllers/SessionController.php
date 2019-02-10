<?php

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

class SessionController extends ControllerBase
{

    /**
     * GitHubのOAuth認証から情報を得るメソッド
     */
    private function getGithubProvider()
    {
        $provider = new League\OAuth2\Client\Provider\Github(
            [
                'clientId'      => $this->config->github->clientId,
                'clientSecret'  => $this->config->github->clientSecret,
            ]
        );
        return $provider;
    }


    /**
     * ログイン前の画面について
     */
    public function indexAction()
    {
        // セッションを利用してアクセストークン確認
        if($this->session->get('AccessToken') and $this->session->get('JWT')){
            // トークン使って認可したユーザ情報を取得する
            $provider = $this->getGithubProvider();
            $user     = $provider->getResourceOwner($this->session->get('AccessToken'));
            $user     = $user->toArray();
        }

        // JWTをリクエストのヘッダーに付加し、APIへリクエスト送信
        $base_url = 'http://localhost/EC_exercise_Phalcon/';
        $response = file_get_contents($base_url.'api/items/', false, $context);
        $response = json_decode($response, true);

        // ユーザ情報を画面へレンダリング
        $this->view->setVar("user", $user);
        $this->view->setVar("jwt",  $this->session->get('JWT'));
        $this->view->setVar("data", $response['data']);

    }


    /**
     * OAuth認証によるログイン
     * Oauth認証をする確認画面へ飛ばす処理
     */
    public function loginAction()
    {
        $provider = $this->getGithubProvider();

        // 認証画面へリダイレクトするURL取得
        $authUrl = $provider->getAuthorizationUrl();

        // CSRF対策のためにいまの状態を入れておく
        $this->session->set('oauth2state', $provider->getState());

        // 認証確認画面へ飛ばす
        header('Location: '.$authUrl);
    }


    /**
     * コールバック処理 (OAuth認証が完了後の処理)
     * セッションを確認し、アクセストークンを取得
     */
    public function callbackAction()
    {
        $provider = $this->getGithubProvider();

        // ちゃんと session/login からきたかどうかセッションを確認
        if (empty($this->request->get('state')) || $this->request->get('state') !== $this->session->get('oauth2state')) {
            // きてなければセッションを切りエラー画面へと遷移
            return $this->dispatcher->forward(array('controller' => 'error', 'action' => 'session_error'));
        }

        // 認証コードからアクセストークンを取得
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $this->request->get('code')
        ]);

        // アクセストークンをセッションに登録
        $this->session->set('AccessToken', $token);


        // 情報を取得するAPIからJWTを取得しセッションに登録
        $jwt_status = $this->getJWTofItemAPI();
        if ( ! $jwt_status ){
            return $this->dispatcher->forward(array('controller' => 'error', 'action' => 'get_jwt_error'));
        }

        // アクセストークンを使用して情報を取得し掲示する処理へリダイレクト
        // $this->response->redirect("session/show");
        $this->response->redirect("session/");
    }


    /**
     * 使用するAPIからJWTを取得する処理
     */
    private function getJWTofItemAPI()
    {
        $provider = $this->getGithubProvider();

        // トークン使って認可したユーザ情報を取得する
        $user = $provider->getResourceOwner($this->session->get('AccessToken'));
        $user = $user->toArray();

        $context = stream_context_create(
            array(
                'http' => array(
                    'method'=> 'POST',
                    'header'=> 'Content-type: application/json; charset=UTF-8',
                    'content' => json_encode(
                        [
                            'name'  => $user['login'],
                            'id'    => $user['id']
                        ]
                    )
                )
            )
        );
        
        $base_url = 'http://localhost/EC_exercise_Phalcon/';
        $jwt_response = file_get_contents($base_url.'api/users/jwt', false, $context);
        $jwt_response = json_decode($jwt_response, true);

        if ($jwt_response['status-code'] == 200){
            // 取得したjwtをセッションに登録
            $this->session->set('JWT', $jwt_response['token']);
            return true;
        } else {
            // ユーザ情報を登録できなかった場合やjwtを取得できなければログインをし直すよう促す
            $this->session->set('JWT', $jwt_response['status']);
            return false;
        }
        
    }


    /**
     * ユーザ情報を掲示する画面
     * アクセストークンを使用して情報を取得し掲示する処理
     */
    public function showAction()
    {
        $provider = $this->getGithubProvider();

        // セッションを利用してアクセストークン確認
        if(empty($this->session->get('AccessToken'))){
            // セッションにアクセストークンがなければエラー画面へ遷移
            return $this->dispatcher->forward(array('controller' => 'error', 'action' => 'session_error'));
        }
        // セッションを利用してJWT確認
        if(empty($this->session->get('JWT'))){
            // セッションにJWTがなければエラー画面へ遷移
            return $this->dispatcher->forward(array('controller' => 'error', 'action' => 'get_jwt_error'));
        }

        // トークン使って認可したユーザ情報を取得する
        $user = $provider->getResourceOwner($this->session->get('AccessToken'));
        $user = $user->toArray();

        // JWTをリクエストのヘッダーに付加し、APIへリクエスト送信
        $base_url = 'http://localhost/EC_exercise_Phalcon/';
        $response = file_get_contents($base_url.'api/items/', false, $context);
        $response = json_decode($response, true);

        // ユーザ情報を画面へレンダリング
        $this->view->setVar("user", $user);
        $this->view->setVar("jwt",  $this->session->get('JWT'));
        $this->view->setVar("data",  $response['data']);
    }



    /**
     * 
     * 商品情報を追加するためのform
     */
    public function addAction()
    {

        // セッションを利用してアクセストークン確認
        if(empty($this->session->get('AccessToken'))){
            // セッションにアクセストークンがなければエラー画面へ遷移
            return $this->dispatcher->forward(array('controller' => 'error', 'action' => 'session_error'));
        }
        // セッションを利用してJWT確認
        if(empty($this->session->get('JWT'))){
            // セッションにJWTがなければエラー画面へ遷移
            return $this->dispatcher->forward(array('controller' => 'error', 'action' => 'get_jwt_error'));
        }

        // 商品情報をformから取得
        // JWTをリクエストのヘッダーに付加し、APIへリクエスト送信
        $insert_data = array(
            'name'        => 'kkkkk',
            'description' => 'heiseijidai',
            'price'       => 2000,
            'img'         => null
        );

        $context = stream_context_create(
            array(
                'http' => array(
                    'method'=> 'POST',
                    'header'=>
                        [
                            'Authorization: Bearer '.$this->session->get('JWT'),
                            'Content-type: application/json; charset=UTF-8',
                        ],
                    'content' => json_encode(
                        $insert_data
                    )
                )
            )
        );
        
        $base_url = 'http://localhost/EC_exercise_Phalcon/';
        $response = file_get_contents($base_url.'api/items/', false, $context);
        $response = json_decode($response, true);


        if ($response["status-code"]!=200){
            // エラー画面へ推移
        }

        // 情報一覧を掲示する処理へリダイレクト
        $this->response->redirect("session/show");


    }
    

    /**
     * ログアウト処理
     * セッションを捨て、ログイン前の画面へ遷移させる
     */
    public function logoutAction()
    {
        // セッションを切る
        $this->session->destroy();

        // ログイン前の画面へ遷移
        $this->response->redirect("session");
    }

}
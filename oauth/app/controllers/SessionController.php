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
        require_once APP_PATH . '/library/oauth2/vendor/autoload.php';
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

        // アクセストークンを使用して情報を取得し掲示する処理へリダイレクト
        $this->response->redirect("session/show");
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

        // トークン使って認可したユーザ情報を取得する
        $user = $provider->getResourceOwner($this->session->get('AccessToken'));
        $user = $user->toArray();

        // ユーザ情報を画面へレンダリング
        $this->view->setVar("user", $user);
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
<?php

class ErrorController extends ControllerBase
{

    // public function indexAction()
    // {

    // }

    /**
     * ログインされていないのに、ログイン後の画面へアクセスした時のエラー画面について
     */
    public function session_errorAction()
    {

    }

    /**
     * jwtの取得に失敗した時、およびjwtがセッションにない時のエラー画面について
     */
    public function get_jwt_errorAction()
    {

    }

    /**
     * 定義されていないルートへアクセスされた時のエラー画面について
     */
    public function error404Action()
    {
        // セッションを利用してアクセストークン確認し、フラグを立てる
        // これはエラー画面にて、リンクをクリックした時に、遷移する画面を分岐させるため
        if(empty($this->session->get('AccessToken'))){
            $session_access_token = true;
        }else{
            $session_access_token = false;
        }

        // フラグをを画面へレンダリング
        $this->view->setVar("session_access_token", $session_access_token);
    }

}
<?php

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH')  || define('APP_PATH', BASE_PATH . '/app');

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
     * ユーザ情報と商品一覧を表示
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

        // JWTをリクエストのヘッダーに付加し、APIへリクエスト送信する。すると商品一覧情報を得る
        $response = file_get_contents($this->getBaseURL_ofAPI().'api/items/', false, $context);
        $response = json_decode($response, true);

        // ユーザ情報を画面へレンダリング
        $this->view->setVar("user", $user);
        $this->view->setVar("jwt",  $this->session->get('JWT'));
        // 商品一覧情報をレンダリング
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
        // GitHubのOAuth認証から情報を得る
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
        $this->response->redirect("session/");
    }


    /**
     * 使用するAPIからJWTを取得する処理
     */
    private function getJWTofItemAPI()
    {
        // GitHubのOAuth認証から情報を得る
        $provider = $this->getGithubProvider();

        // トークン使って認可したユーザ情報を取得する
        $user = $provider->getResourceOwner($this->session->get('AccessToken'));
        $user = $user->toArray();

        // JWTに含むユーザ情報を構成
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
        
        // APIのレスポンスからJWTを取得
        $jwt_response = file_get_contents($this->getBaseURL_ofAPI().'api/users/jwt', false, $context);
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
     * 商品の詳細情報を閲覧
     */
    public function showAction($id)
    {
        // セッションを利用してアクセストークン確認
        if($this->session->get('AccessToken') and $this->session->get('JWT')){
            // トークン使って認可したユーザ情報を取得する
            $provider = $this->getGithubProvider();
            $user     = $provider->getResourceOwner($this->session->get('AccessToken'));
            $user     = $user->toArray();
        }

        // APIへリクエスト送信し、商品の詳細情報をレスポンスとして受ける
        $response = file_get_contents($this->getBaseURL_ofAPI().'api/items/'.$id, false, $context);
        $response = json_decode($response, true);

        // ユーザ情報を画面へレンダリング
        $this->view->setVar("user", $user);
        $this->view->setVar("jwt",  $this->session->get('JWT'));
        // 商品の詳細情報をレンダリング
        $this->view->setVar("item", $response['data']);
    }




    /**
     * 商品の検索を実行して表示
     */
    public function searchAction()
    {
        // セッションを利用してアクセストークン確認
        if($this->session->get('AccessToken') and $this->session->get('JWT')){
            // トークン使って認可したユーザ情報を取得する
            $provider = $this->getGithubProvider();
            $user     = $provider->getResourceOwner($this->session->get('AccessToken'));
            $user     = $user->toArray();
        }

        // 検索ワードを取得
        $key = $this->request->getQuery("key");

        // APIへリクエスト送信し、検索した結果(商品情報)のレスポンスを受ける
        $response = file_get_contents($this->getBaseURL_ofAPI().'api/items/search/'.$key, false, $context);
        $response = json_decode($response, true);

        // ユーザ情報を画面へレンダリング
        $this->view->setVar("user", $user);
        $this->view->setVar("jwt",  $this->session->get('JWT'));
        // 検索ワードと、検索にヒットした商品情報をレンダリング
        $this->view->setVar("key", $key);
        $this->view->setVar("data", $response['data']);
    }




    /**
     * 
     * 商品情報を追加(ログインユーザのみ)
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

    
        // トークン使って認可したユーザ情報を取得する
        $provider = $this->getGithubProvider();
        $user     = $provider->getResourceOwner($this->session->get('AccessToken'));
        $user     = $user->toArray();


        if( ! $this->request->isPost())
        {
            // index画面から"商品を追加"ボタンを押した時

            // ユーザ情報を画面へレンダリング
            $this->view->setVar("user", $user);
            $this->view->setVar("jwt",  $this->session->get('JWT'));


        }else{
            // 追加ボタンを押した時

            // 商品情報をformから取得しJson(APIへリクエストするための)の要素とする
            $insert_data = array(
                'name'        => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'price'       => $this->request->getPost('price'),
            );

            // アップロード画像があれば，Jsonの要素へと追加する
            if($this->request->hasFiles(true)){
                // Phalconは複数のファイルアップロードを想定しているので配列になっている -> ループ回して取り出す
                foreach ($this->request->getUploadedFiles() as $file) {
                    $raw_data = file_get_contents($file->getTempName()); // バイナリ取得
                    $insert_data['img'] = base64_encode($raw_data);      // base64へエンコード
                }

            }

                
            // APIへPOSTするためのコンテンツ作り
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
            
            // API叩く
            $response = file_get_contents($this->getBaseURL_ofAPI().'api/items/', false, $context);
            $response = json_decode($response, true);


            // responseの情報を元にレンダリングやリダイレクト
            if ($response["status-code"]!=201){
                // バリデーションエラー他のときメッセージなどを含めてレンダリングし直す

                // ユーザ情報を画面へレンダリング
                $this->view->setVar("user", $user);
                $this->view->setVar("jwt",  $this->session->get('JWT'));
                // バリデーションやその他エラーを表示
                $this->view->setVar("error_message", $response['message']);

            } else{
                // 情報一覧を掲示する処理へリダイレクト
                $this->response->redirect("session/");
            }


        }

    }




    /**
     * 
     * 商品情報の編集(ログインユーザのみ)
     */
    public function editAction($id)
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

    
        // トークン使って認可したユーザ情報を取得する
        $provider = $this->getGithubProvider();
        $user     = $provider->getResourceOwner($this->session->get('AccessToken'));
        $user     = $user->toArray();


        if( ! $this->request->isPost())
        {
            // 商品詳細画面から"編集"ボタンを押した時

            // ユーザ情報を画面へレンダリング
            $this->view->setVar("user", $user);
            $this->view->setVar("jwt",  $this->session->get('JWT'));

            // 現在の商品情報をAPI経由で取得しレンダリング
            $response = file_get_contents($this->getBaseURL_ofAPI().'api/items/'.$id, false, $context);
            $response = json_decode($response, true);
            $this->view->setVar("item_info", $response['data']);



        }else{
            // 編集画面で"編集"ボタンを押した時

            // 商品情報をformから取得しJson(APIへリクエストするための)の要素とする
            $insert_data = array(
                'name'        => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'price'       => $this->request->getPost('price'),
            );

            // アップロード画像があれば，Jsonの要素へと追加する
            if($this->request->hasFiles(true)){
                // Phalconは複数のファイルアップロードを想定しているので配列になっている -> ループ回して取り出す
                foreach ($this->request->getUploadedFiles() as $file) {
                    $raw_data = file_get_contents($file->getTempName()); // バイナリ取得
                    $insert_data['img'] = base64_encode($raw_data);      // base64へエンコード
                }

            }

                
            // APIへPUTするためのコンテンツ作り
            $context = stream_context_create(
                array(
                    'http' => array(
                        'method'=> 'PUT',
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
            
            // API叩く
            $response = file_get_contents($this->getBaseURL_ofAPI().'api/items/'.$id, false, $context);
            $response = json_decode($response, true);


            // responseの情報を元にレンダリングやリダイレクト
            if ($response["status-code"]!=200){
                // バリデーションエラー他のときメッセージなどを含めてレンダリングし直す

                // ユーザ情報を画面へレンダリング
                $this->view->setVar("user", $user);
                $this->view->setVar("jwt",  $this->session->get('JWT'));
                // バリデーションやその他エラーを表示
                $this->view->setVar("error_message", $response['message']);

            } else{
                // 情報一覧を掲示する処理へリダイレクト
                $this->response->redirect("session/");
            }


        }

    }




    /**
     * 
     * 商品情報を削除(ログインユーザのみ)
     */
    public function deleteAction($id)
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


        // トークン使って認可したユーザ情報を取得する
        $provider = $this->getGithubProvider();
        $user     = $provider->getResourceOwner($this->session->get('AccessToken'));
        $user     = $user->toArray();

        // APIへDELETEするためのコンテンツ作り
        $context = stream_context_create(
            array(
                'http' => array(
                    'method'=> 'DELETE',
                    'header'=>
                            [
                                'Authorization: Bearer '.$this->session->get('JWT'),
                                'Content-type: application/json; charset=UTF-8',
                            ],
                )
            )
        );
        
        // API叩く
        $response = file_get_contents($this->getBaseURL_ofAPI().'/api/items/'.strval($id) , false, $context);
        $response = json_decode($response, true);

        // ユーザ情報を画面へレンダリング
        $this->view->setVar("user", $user);
        $this->view->setVar("jwt",  $this->session->get('JWT'));
        // response情報を元にレンダリング
        $this->view->setVar("response", $response);


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
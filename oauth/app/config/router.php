<?php

// デフォルトのルーティング設定を切るために引数で"false"を与えている
$router = $di->getRouter(false);

// ルートの定義

// ユーザー情報と商品一覧を表示する画面のルート
$router->add('/session', 'session::index');

// ログインするためのOAuth認証画面へ飛ばすためのルート
$router->add('/session/login', 'session::login');

// OAuth認証画面から返ってきた後の処理をするルート
$router->add('/session/callback', 'session::callback');

// ログイン後のユーザ情報を表示するルート
$router->add('/session/show/{id:[0-9]+}', 'session::show');

// ログアウトのためのルート
$router->add('/session/logout', 'session::logout');



// 商品情報を検索するルート;
$router->add('/session/search', 'session::search');

// 商品情報追加の処理のルート
$router->add('/session/add', 'session::add');

// 商品情報の編集処理のルート
$router->add('/session/edit/{id:[0-9]+}', 'session::edit');

// 商品情報削除の処理のルート
$router->add('/session/delete/{id:[0-9]+}', 'session::delete');



// 以上のルートのどこにも当てはまらない時
$router->notFound('error::error404');

// 末尾のスラッシュを自動的に取り除く
$router->removeExtraSlashes(true);

$router->handle();

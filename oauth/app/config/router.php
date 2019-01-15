<?php

// デフォルトのルーティング設定を切るために引数で"false"を与えている
$router = $di->getRouter(false);

// ルートの定義

// ログイン前の画面のルート
$router->add('/session', 'session::index');

// ログインするためのOAuth認証画面へ飛ばすためのルート
$router->add('/session/login', 'session::login');

// OAuth認証画面から返ってきた後の処理をするルート
$router->add('/session/callback', 'session::callback');

// ログイン後のユーザ情報を表示するルート
$router->add('/session/show', 'session::show');

// ログアウトのためのルート
$router->add('/session/logout', 'session::logout');

// 以上のルートのどこにも当てはまらない時
$router->notFound('error::error404');

// 末尾のスラッシュを自動的に取り除く
$router->removeExtraSlashes(true);

$router->handle();

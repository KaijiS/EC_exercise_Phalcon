<?php

// デフォルトのルーティング設定を切るために引数で"false"を与えている
$router = $di->getRouter(false);


// ルートの定義

// idで商品情報を取得するルート
$router->addGet('/items/{id:\d+}', 'items::show');

// 商品情報を検索するルート
$router->addGet('/items/search/{name}', 'items::search');

// 商品情報を追加するルート
$router->addPost('/items', 'items::add');

// 商品情報を編集するルート
$router->addPut('/items/{id:\d+}', 'items::edit');

// 商品情報を削除するルート
$router->addDelete('/items/{id:\d+}', 'items::delete');

// 以上のルートのどこにも当てはまらない時
$router->notFound('items::route404');

// 末尾のスラッシュを自動的に取り除く
$router->removeExtraSlashes(true);

$router->handle();

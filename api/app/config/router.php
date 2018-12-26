<?php

$router = $di->getRouter(false);


// ルートの定義

$router->addGet('/items/{id:\d+}', 'items::show');

$router->addGet('/items/search/{name}', 'items::search');

$router->addPost('/items', 'items::add');

$router->addPut('/items/{id:\d+}', 'items::edit');

$router->addDelete('/items/{id:\d+}', 'items::delete');

$router->notFound('items::route404');

// 末尾のスラッシュを自動的に取り除く
$router->removeExtraSlashes(true);

$router->handle();

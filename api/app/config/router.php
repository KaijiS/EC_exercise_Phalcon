<?php

$router = $di->getRouter();


// ルートの定義

$router->addGet('/items/{id:\d+}', 'items::show');

$router->addGet('/items/search/{name}', 'items::search');

$router->addPost('/items/add', 'items::add');

$router->handle();

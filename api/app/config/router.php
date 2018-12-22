<?php

$router = $di->getRouter();


// ルートの定義
// $router->add(
//     "/items/show",
//     [
//         "controller" => "items",
//         "action"     => "show",
//     ]
// );

$router->addGet('/items/{id}', 'items::show');

$router->addGet('/items/search/{name}', 'items::search');

$router->handle();

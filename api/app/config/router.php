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

$router->add('/items/{id}', 'items::show');

$router->handle();

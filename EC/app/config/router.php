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

$router->handle();

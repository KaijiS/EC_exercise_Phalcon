<?php

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

// リソースの特定に役立つ絶対パス定数を定義する
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// オートローダーの登録
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();

// DIの生成
$di = new FactoryDefault();

// ビューコンポーネントの設定
$di->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

// ベースURIの設定
$di->set(
    'url',
    function () {
        $url = new UrlProvider();
        $url->setBaseUri('/');
        return $url;
    }
);



// データベースの設定
$di->set(
    'db',
    function () {
        return new DbAdapter(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'dbname'   => 'EC',
            ]
        );
    }
);

$application = new Application($di);

try {
    // リクエストのハンドリング
    $response = $application->handle();

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
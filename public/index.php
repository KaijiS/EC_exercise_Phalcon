<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Response;

require_once __DIR__."/../ImageDataInfo/ImageData.php";

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');


// Loader() を使ってモデルをオートロード
$loader = new Loader();

$loader->registerNamespaces(
    [
        // 'EC\Products' => __DIR__ . '/../models/',
        'App\Controllers' => APP_PATH . '/controllers/',
        'App\Models'      => APP_PATH . '/models/',
    ]
);

$loader->register();

$di = new FactoryDefault();

// データベースサービスのセットアップ
$di->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'dbname'   => 'EC',
            ]
        );
    }
);

// DI を作成し、アプリケーションにバインド
$app = new Micro($di);

/**
 * Handle routes
 */
include APP_PATH . '/config/router.php';

$app->handle();

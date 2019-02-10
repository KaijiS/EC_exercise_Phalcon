<?php


$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->libraryDir
    ]
);

// Load composer vendor stuff
$loader->registerFiles(
    [
        APP_PATH . "/library/php_jwt/vendor/autoload.php"
    ]
);

$loader->register();

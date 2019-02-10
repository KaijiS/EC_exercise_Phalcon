<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
    ]
);

// Load composer vendor stuff
$loader->registerFiles(
    [
        APP_PATH . "/library/oauth2/vendor/autoload.php"
    ]
);

$loader->register();

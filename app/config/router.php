<?php

// ルーティングの定義
$itemsCollection = new \Phalcon\Mvc\Micro\Collection();
$itemsCollection->setHandler('\App\Controllers\ItemController', true);
$itemsCollection->setPrefix("/api/items");
$itemsCollection->get('/', 'index');
$itemsCollection->get('/{id}', 'show');
$itemsCollection->get('/', 'search');
$itemsCollection->post('/', 'add');
$itemsCollection->put('/{id}', 'edit');
$itemsCollection->edit('/{id}', 'edit');
$application->mount($itemsCollection);


// URLが見つからなかったら
$application->notFound(
    function () use ($application) {
        $exception =
            new \App\Controllers\HttpExceptions\Http404Exception(
                _('URI not found or error in request.'),
                \App\Controllers\AbstractController::ERROR_NOT_FOUND,
                new \Exception('URI not found: ' . $application->request->getMethod() . ' ' . $application->request->getURI())
            );
        throw $exception;
    }
);
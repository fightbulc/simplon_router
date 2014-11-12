<?php

require __DIR__ . '/../src/Router.php';
require __DIR__ . '/TestController.php';

$routes = [
    [
        'request'  => 'GET',
        'pattern'  => '^hello$',
        'callback' => function () { return 'How kind!'; },
    ],
    [
        'request'  => 'GET',
        'pattern'  => 'hello/(\w+)$',
        'callback' => function ($param) { return 'How kind, ' . $param; },
    ],
    [
        'request'    => 'POST',
        'pattern'    => 'foo/(\w+)$',
        'controller' => 'TestController::fooAction',
    ],
    [
        'request'  => 'POST',
        'pattern'  => '.',
        'callback' => function () { return '404'; },
    ],
];

$response = \Simplon\Router\Router::observe($routes, $_GET['route']);

echo $response;
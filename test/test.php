<?php

require __DIR__ . '/../src/Router.php';
require __DIR__ . '/TestController.php';

$routes = [
    [
        'pattern'  => '^hello$',
        'callback' => function () { return 'How kind!'; },
    ],
    [
        'pattern'  => 'hello/(\w+)$',
        'callback' => function ($param) { return 'How kind, ' . $param; },
    ],
    [
        'pattern'    => 'foo/(\w+)$',
        'controller' => 'TestController::fooAction',
    ],
    [
        'pattern'  => '.',
        'callback' => function () { return '404'; },
    ],
];

$response = \Simplon\Router\Router::observe($routes, $_GET['route']);

echo $response;
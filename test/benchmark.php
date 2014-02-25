<?php

  require __DIR__ . '/../vendor/autoload.php';

  (new Simplon\Router\Router())
    ->enableRoutingViaQueryString(TRUE)
    ->addRoute('GET', '/say/hello/:alpha', function ($name) { echo "Hello $name"; })
    ->run();

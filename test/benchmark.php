<?php

  use Simplon\Router\Router;

  require __DIR__ . '/../vendor/autoload.php';

  (new Router())
    ->enableRoutingViaQueryString(TRUE)
    ->addRoute('GET', '/say/hello/:alpha', function ($name){ echo "Hello $name"; })
    ->run();

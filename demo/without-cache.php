<?php

include_once __DIR__ . '/../vendor/autoload.php';

$data  = require __DIR__ . '/global.php';
$slice = new \Bavix\Slice\Slice($data);

$router = new \Bavix\Router\Router($slice);

$route = $router->getRoute('/en/cp/posts', 'example.com', 'http');

var_dump($route);

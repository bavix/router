<?php

include_once __DIR__ . '/../vendor/autoload.php';

$route = new \Deimos\Route\Route([
    ['/<controller>(/<action>(/<id:\d+>))'],
    [
        'action' => 'default'
    ]
]);

$router = new \Deimos\Router\Router();

$router->addRoute($route);

var_dump($router->getCurrentRoute('/hello-world'));die;
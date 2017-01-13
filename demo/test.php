<?php

include_once __DIR__ . '/../vendor/autoload.php';

$route = new \Deimos\Route\Route([
    ['(/<lang:[a-z]{2}>)/<controller>(/<action>(/<id:\d+>))'],
    [
        'lang'   => 'ru',
        'action' => 'default'
    ]
]);

$router = new \Deimos\Router\Router();

$router->setMethod('GET');
$router->addRoute($route);

var_dump($route = $router->getCurrentRoute('/hello-world'), $route->attributes());
var_dump($route = $router->getCurrentRoute('/en/hello-world'), $route->attributes());
die;
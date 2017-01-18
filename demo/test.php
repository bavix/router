<?php

include_once __DIR__ . '/../vendor/autoload.php';

$router = new \Deimos\Router\Router();

$router->setMethod('GET');


//$route = new \Deimos\Route\Route(
//    ['(/<lang:[a-z]{2}>)/<controller>(/<action>(/<id:\d+>))'],
//    [
//        'lang'   => 'ru',
//        'action' => 'default'
//    ]
//);
//$router->addRoute($route); // OR

$router->setRoutes([
    [
        'type'     => 'pattern',
        'path'     => '(/<lang:[a-z]{2}>)/<controller>(/<action>(/<id:\d+>))',

        'defaults' => [
            'lang'   => 'ru',
            'action' => 'default'
        ]
    ]
]);

var_dump($route = $router->getCurrentRoute('/hello-world'), $route->attributes());
var_dump($route = $router->getCurrentRoute('/en/hello-world'), $route->attributes());
die;
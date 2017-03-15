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
        'path'     => [
            '(/<lang:[a-z]{2}>)/<controller>(/<action>(/<id:\d+>))',
            [
                'controller' => '[\w-<>!12]+'
            ]
        ],

        'defaults' => [
//            'lang'   => 'ru',
            'action' => 'default'
        ]
    ],
    [
        'type' => 'prefix',
        'path' => '/get/',

        'resolver' => [
            [
                'type' => 'pattern',
                'path' => 'image/<hash:[a-z0-9]+/[a-z0-9]+>/<frame:\d+><ext:\.(png|jpe?g)>',

                'methods' => [
                    'GET'
                ],

                'defaults' => [
                    'action'     => 'image',
                ]
            ],
            [
                'type' => 'pattern',
                'path' => 'file/<size>/<path:.*>',

                'defaults' => [
                    'action'     => 'file',
                ]
            ],
        ],

        'defaults' => [
            'controller' => 'file',
            'runner'   => 'pub',
            'notFound' => 'default'
        ]
    ]
]);

var_dump($route = $router->getCurrentRoute('/get/image/ab/cd/0.png'), $route->attributes());
var_dump($route = $router->getCurrentRoute('/hello-world'), $route->attributes());
var_dump($route = $router->getCurrentRoute('/en/hello-world'), $route->attributes());
die;
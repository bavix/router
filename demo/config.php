<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$config = new \Bavix\Config\Config(__DIR__);
$driver = new \Stash\Driver\FileSystem();
$pool   = new \Stash\Pool($driver);

$slice = $config->get('global');

$router = new \Bavix\Router\Router($slice, $pool);

$route = $router->getRoute('/en/cp/posts', 'example.com', 'http');

var_dump($route);

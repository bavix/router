<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$driver = new \Stash\Driver\FileSystem();
$pool   = new \Stash\Pool($driver);

//$loader = new \Bavix\SDK\FileLoader\PHPLoader(__DIR__ . '/global.php');
//$loader = new \Bavix\SDK\FileLoader\JSONLoader(__DIR__ . '/global.json');
$loader = new \Bavix\SDK\FileLoader\YamlLoader(__DIR__ . '/global.yaml');
$slice = $loader->asSlice();

$router = new \Bavix\Router\Router($slice, $pool);

$route = $router->getRoute('/en/cp/posts', 'example.com', 'http');

var_dump($route);

<?php

include_once __DIR__ . '/../vendor/autoload.php';

$data = require __DIR__ . '/global.php';
$slice = new \Bavix\Slice\Slice($data);

$driver = new \Stash\Driver\FileSystem();
$pool   = new \Stash\Pool($driver);
$router = new \Bavix\Router\Router($slice, $pool);

$route = $router->getCurrentRoute();

var_dump(
    $route->getAttributes(),
    $route->getDefaults(),
    $route->getProtocol(),
    $route->getHost(),
    $route->getPath(),
    $route->getPattern(),
    $route->getPathPattern(),
    $route->getName()
);

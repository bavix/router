<?php

include_once __DIR__ . '/../vendor/autoload.php';

$data = require __DIR__ . '/global.php';
$slice = new \Bavix\Slice\Slice($data);

$driver = new \Stash\Driver\FileSystem();
$pool   = new \Stash\Pool($driver);
$router = new \Bavix\Router\Router($slice, $pool);

$route = $router->getCurrentRoute();

var_dump(
    \Bavix\Router\route($route),
    $route->getAttributes(),
    $route->getDefaults(),
    $route->getHttp(),
    $route->getPath(),
    $route->getRegex(),
    $route->getRegexPath()
);

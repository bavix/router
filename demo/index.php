<?php

include_once __DIR__ . '/../vendor/autoload.php';

$data = require __DIR__ . '/global.php';
$slice = new \Bavix\Slice\Slice($data);

$driver = new \Stash\Driver\FileSystem();
$pool   = new \Stash\Pool($driver);
$router = new \Bavix\Router\Router($slice, $pool);

$route = $router->getCurrentRoute();

var_dump(
    ['getExtends' => $route->getExtends()],
    ['getAttributes' => $route->getAttributes()],
    ['getGroups' => $route->getGroups()],
    ['getDefaults' => $route->getDefaults()],
    ['getMethods' => $route->getMethods()],
    ['getMethod' => $route->getMethod()],
    ['getProtocol' => $route->getProtocol()],
    ['getHost' => $route->getHost()],
    ['getPath' => $route->getPath()],
    ['getPattern' => $route->getPattern()],
    ['getPathPattern' => $route->getPathPattern()],
    ['getPathValue' => $route->getPathValue()],
    ['getName' => $route->getName()]
);

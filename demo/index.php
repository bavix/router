<?php

include_once __DIR__ . '/../vendor/autoload.php';

$builder = new Deimos\Builder\Builder();
$helper  = new \Deimos\Helper\Helper($builder);
$config  = new \Deimos\Config\Config($helper, __DIR__);

$slice = $config->get('global');

$cache  = new \Deimos\CacheHelper\SliceHelper(__DIR__ . '/cache');
$router = new \Deimos\Router\Router($slice, $cache);

$route = $router->getCurrentRoute();

var_dump(
    \Deimos\Router\route($route),
    $route->getAttributes(),
    $route->getDefaults(),
    $route->getHttp(),
    $route->getPath(),
    $route->getRegex(),
    $route->getRegexPath()
);

<?php

include_once __DIR__ . '/../vendor/autoload.php';

$builder = new Deimos\Builder\Builder();
$helper  = new \Deimos\Helper\Helper($builder);
$config  = new \Deimos\Config\Config($helper, __DIR__);

$slice = $config->get('global');

$cache  = new \Deimos\CacheHelper\SliceHelper(__DIR__ . '/cache');
$router = new \Deimos\Router\Router($slice, null);

$route = $router->getRoute('/en/cp/posts', 'example.com', 'http');

var_dump($route);

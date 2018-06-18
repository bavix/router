<?php

include_once \dirname(__DIR__) . '/vendor/autoload.php';

$group = new \Bavix\Router\Group('/api', function (\Bavix\Router\GroupResolution $route) {
    $route->get('/hello');
    $route->resource('/users')->only([
        'create', 'store', 'update', 'destroy'
    ]);
});

var_dump((new \Bavix\Router\Loader($group->toArray()))->simplify());

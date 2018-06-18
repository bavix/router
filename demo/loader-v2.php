<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$loader = new \Bavix\Router\Loader(require __DIR__ . '/global.php');

foreach ($loader->simplify() as $key => $route) {
    var_dump([$key => $route]);
}

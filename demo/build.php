<?php

include_once \dirname(__DIR__) . '/vendor/autoload.php';

var_dump(\Bavix\Router\Build::url('/hello')); // https://cli/hello
var_dump(\Bavix\Router\Build::url('/hello', 'router.local'));
var_dump(\Bavix\Router\Build::url('/hello', 'router.local', 'cli')); // https://cli/hello

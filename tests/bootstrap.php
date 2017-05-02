<?php

/**
 * @var $loader \Composer\Autoload\ClassLoader
 */
$loader = require dirname(__DIR__) . '/vendor/autoload.php';

$loader->addPsr4('BavixTest\\', 'tests/src/');

if (class_exists('\PHPUnit\Framework\TestCase'))
{
    class TestCase extends \PHPUnit\Framework\TestCase
    {
    }
}
else
{
    class TestCase extends \PHPUnit_Framework_TestCase
    {
    }
}
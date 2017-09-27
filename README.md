# router

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bavix/router/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bavix/router/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/bavix/router/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bavix/router/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/bavix/router/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bavix/router/build-status/master)

Here is a quick demo:
```php
$slice = new \Bavix\Slice\Slice([
  'default' => [
    'type' => 'pattern',
    'path' => '/demo'
  ]
]);

$router = new \Bavix\Router\Router($slice);
var_dump($router->getCurrentRoute());
```

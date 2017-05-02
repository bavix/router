# router

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

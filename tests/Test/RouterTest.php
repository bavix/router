<?php

namespace Test;

use Bavix\Router\Route;
use Bavix\Router\Router;
use Bavix\Slice\Slice;

class RouterTest extends \TestCase
{

    public function testRoute()
    {

        $slice = new Slice([
            'type' => 'pattern',
            'path' => '/<controller>(/<action>/<value:\w+>)',

            'defaults' => [
                'action' => 'default2'
            ]
        ]);

        $route1 = new Route($slice);

        $route2 = new Route($slice->make([
            'type' => 'pattern',
            'path' => '/<controller>(/<action>(/<id:\d+>))',

            'defaults' => [
                'action' => 'default'
            ],

            'methods' => ['GET', 'POST', 'AJAX']

        ]));

        $this->assertTrue($route1->test('https://example.com/hello-world', 'GET'));
        $this->assertTrue($route1->test('https://example.com/hello-world', 'AJAX'));
        $this->assertFalse($route2->test('https://example.com/hello-world2/test/string', 'GET'));

        $attributes  = $route1->getAttributes();
        $attributes2 = $route2->getAttributes();

        $this->assertEquals(
            'hello-world',
            $attributes['controller']
        );
        $this->assertEquals(
            'default2',
            $attributes['action']
        );
        $this->assertEquals(
            $route2->getDefaults(),
            $attributes2
        );
    }

    /**
     * @expectedException \Bavix\Exceptions\NotFound\Page
     */
    public function testErrorNotFound()
    {
        $slice = new Slice([
            'fake' => [
                'type' => 'pattern',
                'path' => '/<controller>(/<action>)',

                'defaults' => [
                    'action' => 'default'
                ],

                'methods' => ['FAKE']
            ]
        ]);

        $router = new \Bavix\Router\Router($slice);

        $class = (new \ReflectionClass(Router::class));

        $property = $class->getProperty('method');
        $property->setAccessible(true);
        $property->setValue($router, 'GET');

        $property = $class->getProperty('host');
        $property->setAccessible(true);
        $property->setValue($router, 'router.deimos');

        $property = $class->getProperty('protocol');
        $property->setAccessible(true);
        $property->setValue($router, 'https');

        $router->getRoute('/hello-world');
    }

    /**
     * @expectedException \Bavix\Exceptions\NotFound\Data
     */
    public function testErrorWithoutType()
    {
        $slice = new Slice([
            'fake' => [
                // 'type' => 'pattern',
                'path' => '/<controller>(/<action>)',

                'defaults' => [
                    'action' => 'default'
                ],

                'methods' => ['FAKE']
            ]
        ]);

        (new Router($slice))->routes();
    }

    /**
     * @expectedException \Bavix\Exceptions\NotFound\Path
     */
    public function testErrorWithoutPath()
    {
        $slice = new Slice([
            'fake' => [
                'type' => 'pattern',
                //'path' => '/<controller>(/<action>)',

                'defaults' => [
                    'action' => 'default'
                ],

                'methods' => ['FAKE']
            ]
        ]);

        (new Router($slice))->routes();
    }

    public function testPrefixSuccess()
    {
        $slice = new Slice([
            __METHOD__ => [
                'type'     => 'prefix',
                'path'     => '/demo',
                'resolver' => [
                    __FUNCTION__ => [
                        'type'     => 'pattern',
                        'path'     => '/many.php',
                        'defaults' => [
                            'p1'     => 'hello',
                            'p2'     => 'world',
                            'action' => 'default'
                        ],
                    ],
                    __METHOD__   => [
                        'type'     => 'pattern',
                        'path'     => '/<p1>',
                        'defaults' => [
                            'p1'     => 'hello',
                            'p2'     => 'world',
                            'action' => 'default'
                        ],
                    ]
                ]
            ]
        ]);

        $router = new Router($slice);

        $class = (new \ReflectionClass(Router::class));

        $property = $class->getProperty('method');
        $property->setAccessible(true);
        $property->setValue($router, 'GET');

        $property = $class->getProperty('host');
        $property->setAccessible(true);
        $property->setValue($router, 'router.deimos');

        $property = $class->getProperty('protocol');
        $property->setAccessible(true);
        $property->setValue($router, 'https');

        $attributes = $router->getRoute('/demo/many.php')->getAttributes();

        $this->assertEquals(
            'hello',
            $attributes['p1']
        );

        $attributes = $router->getRoute('/demo/deimos')->getAttributes();

        $this->assertEquals(
            'deimos',
            $attributes['p1']
        );

    }

    public function testLanguage()
    {
        $slice = new Slice([
            __METHOD__ => [
                'type'     => 'prefix',
                'path'     => '(/<lang:[a-z]{2}>)',
                'resolver' => [
                    __FUNCTION__ => [
                        'type'     => 'pattern',
                        'path'     => '/<controller>(/<action>(/<id:\d+>))',
                        'defaults' => [
                            'action' => 'default'
                        ],
                    ],
                ],

                'defaults' => [
                    'lang' => 'ru'
                ]
            ]
        ]);

        $router = new Router($slice);

        $class = (new \ReflectionClass(Router::class));

        $property = $class->getProperty('method');
        $property->setAccessible(true);
        $property->setValue($router, 'GET');

        $property = $class->getProperty('host');
        $property->setAccessible(true);
        $property->setValue($router, 'router.deimos');

        $property = $class->getProperty('protocol');
        $property->setAccessible(true);
        $property->setValue($router, 'https');

        // lang:default -> ru
        $route = $router->getRoute('/hello-world');
        $this->assertEquals($route->getAttributes(), [
            'controller' => 'hello-world',
            'action'     => 'default',
            'lang'       => 'ru',
        ]);

        // lang -> en
        $route = $router->getRoute('/en/hello-world');
        $this->assertEquals($route->getAttributes(), [
            'controller' => 'hello-world',
            'action'     => 'default',
            'lang'       => 'en',
        ]);
    }

}

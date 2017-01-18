<?php

namespace Test;

use Deimos\Router\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    public function testRouter()
    {

        $route2 = new \Deimos\Route\Route(
            ['/<controller>(/<action>/<value:\w+>)'],
            [
                'action' => 'default2'
            ]
        );

        $router = new \Deimos\Router\Router();

        $router->setMethod('GET');
        $router->setRoutes([
            [
                'type'     => 'pattern',
                'path'     => '/<controller>(/<action>(/<id:\d+>))',

                'defaults' => [
                    'action' => 'default'
                ],

                'methods'  => ['GET', 'POST']
            ]

        ]);
        $router->addRoute($route2);

        $attributes  = $router->getCurrentRoute('/hello-world')->attributes();
        $attributes2 = $router->getCurrentRoute('/hello-world2/test/string')->attributes();

        $this->assertEquals(
            'hello-world',
            $attributes['controller']
        );
        $this->assertEquals(
            'default',
            $attributes['action']
        );

        $this->assertEquals(
            'hello-world2',
            $attributes2['controller']
        );
        $this->assertEquals(
            'test',
            $attributes2['action']
        );

        $this->assertFalse(isset($attributes['id']));
        $this->assertEquals(
            'string',
            $attributes2['value']
        );

    }

    /**
     * @expectedException \Deimos\Router\Exceptions\NotFound
     */
    public function testErrorNotFound()
    {
        $route = new \Deimos\Route\Route(
            ['/<controller>(/<action>)'],
            [
                'action' => 'default'
            ],
            ['FLASH']
        );

        $router = new \Deimos\Router\Router();

        $router->setMethod('GET');
        $router->addRoute($route);

        $router->getCurrentRoute('/hello-world');
    }

    /**
     * @expectedException \Deimos\Router\Exceptions\NotFound
     */
    public function testErrorWithoutType()
    {
        $route = new Router();

        $route->setRoutes([
            [
                'type' => 'prefix',
                'path' => '/demo',
                'resolver' => [
                    __METHOD__ => [
//                        'type' => 'pattern',
                        'path' => '/many.php',
                        'defaults' => [
                            'p1'     => 'hello',
                            'p2'     => 'world',
                            'action' => 'default'
                        ],
                    ]
                ]
            ]
        ]);

    }

    /**
     * @expectedException \Deimos\Router\Exceptions\NotFound
     */
    public function testErrorWithoutPath()
    {
        $route = new Router();

        $route->setRoutes([
            [
                'type' => 'prefix',
                'path' => '/demo',
                'resolver' => [
                    __METHOD__ => [
                        'type' => 'pattern',
//                        'path' => '/many.php',
                        'defaults' => [
                            'p1'     => 'hello',
                            'p2'     => 'world',
                            'action' => 'default'
                        ],
                    ]
                ]
            ]
        ]);

    }

    public function testPrefixSuccess()
    {
        $route = new Router();

        $route->setRoutes([
            [
                'type' => 'prefix',
                'path' => '/demo',
                'resolver' => [
                    __FUNCTION__ => [
                        'type' => 'pattern',
                        'path' => '/many.php',
                        'defaults' => [
                            'p1'     => 'hello',
                            'p2'     => 'world',
                            'action' => 'default'
                        ],
                    ],
                    __METHOD__ => [
                        'type' => 'pattern',
                        'path' => '/<p1>',
                        'defaults' => [
                            'p1'     => 'hello',
                            'p2'     => 'world',
                            'action' => 'default'
                        ],
                    ]
                ]
            ]
        ]);

        $route->setMethod('GET');

        $attributes = $route->getCurrentRoute('/demo/many.php')->attributes();

        $this->assertEquals(
            'hello',
            $attributes['p1']
        );

        $attributes = $route->getCurrentRoute('/demo/deimos')->attributes();

        $this->assertEquals(
            'deimos',
            $attributes['p1']
        );

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMethod()
    {
        $route = new \Deimos\Route\Route(
            ['/<controller>(/<action>)'],
            [
                'action' => 'default'
            ]
        );

        $router = new \Deimos\Router\Router();

        $router->addRoute($route);

        $router->getCurrentRoute('/hello-world');
    }

    public function testLanguage()
    {
        $route = new \Deimos\Route\Route(
            [
                '(/<lang:[a-z]{2}>)/<controller>(/<action>(/<id:\d+>))'
            ],
            [
                'lang'   => 'ru',
                'action' => 'default'
            ]
        );

        $router = new \Deimos\Router\Router();

        $router->setMethod('GET');
        $router->addRoute($route);

        // lang:default -> ru
        $route = $router->getCurrentRoute('/hello-world');
        $this->assertEquals($route->attributes(), [
            'lang'       => 'ru',
            'action'     => 'default',
            'controller' => 'hello-world',
        ]);

        // lang -> en
        $route = $router->getCurrentRoute('/en/hello-world');
        $this->assertEquals($route->attributes(), [
            'lang'       => 'en',
            'action'     => 'default',
            'controller' => 'hello-world',
        ]);
    }

}

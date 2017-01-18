<?php

namespace Test;

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
     * @expectedException \InvalidArgumentException
     */
    public function testError()
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

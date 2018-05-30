<?php

namespace Tests;

use Bavix\Router\Match;
use Bavix\Router\Route;
use Bavix\Router\Router;
use Bavix\Router\Rules\PatternRule;
use Bavix\Slice\Slice;
use Bavix\Tests\Unit;
use Bavix\Tests\Bind;

class RouterTest extends Unit
{

    public function testRoute(): void
    {

        $rule1 = new PatternRule('rule1', [
            'type' => 'pattern',
            'path' => '/<controller>(/<action>/<value:\w+>)',

            'defaults' => [
                'action' => 'default2'
            ]
        ]);

        $rule2 = new PatternRule('rule2', [
            'type' => 'pattern',
            'path' => '/<controller>(/<action>(/<id:\d+>))',

            'defaults' => [
                'action' => 'default'
            ],

            'methods' => ['GET', 'POST']

        ]);

        $match1 = new Match($rule1, 'https://example.com/hello-world', 'GET');
        $match2 = new Match($rule1, 'https://example.com/hello-world', 'MY_METHOD');
        $match3 = new Match($rule2, 'https://example.com/hello-world2/test/string', 'GET');

        $this->assertTrue($match1->isTest());
        $this->assertTrue($match2->isTest());
        $this->assertFalse($match3->isTest());

        $attributes  = $match2->getAttributes();
        $attributes2 = $match3->getAttributes();

        $this->assertEquals(
            'hello-world',
            $attributes['controller']
        );
        $this->assertEquals(
            'default2',
            $attributes['action']
        );
        $this->assertEquals(
            $match3->getRule()->getDefaults(),
            $attributes2
        );
    }

    /**
     * @expectedException \Bavix\Exceptions\NotFound\Page
     */
    public function testErrorNotFound(): void
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

        $router = new Router($slice);
        $router->getRoute('/hello-world');
    }

    /**
     * @expectedException \Bavix\Exceptions\NotFound\Data
     */
    public function testErrorWithoutType(): void
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
    public function testErrorWithoutPath(): void
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

    public function testPrefixSuccess(): void
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

    public function testLanguage(): void
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

<?php

return [

    'http' => [

        'type'   => 'prefix',

        'protocol' => 'http', // optional null
        'host'     => 'router\.local', // optional null

        'resolver' => [

            'cp' => [
                'type' => 'prefix',
                'path' => '(/<language:[a-z]{2}>)/cp',

                'resolver' => [

                    'dashboard' => [
                        'type' => 'pattern',
                        'path' => '/dashboard',

                        'defaults' => [
                            'controller' => 'dashboard'
                        ],

                        'methods' => ['GET', 'HEAD']
                    ],

                    'post' => [
                        'type' => 'pattern',
                        'path' => '/post(/<id>)',
                    ],

                    'posts' => [
                        'type' => 'pattern',
                        'path' => '/posts(/<id>(/<type>))',

                        'defaults' => [
                            'type'       => 'list',
                            'controller' => 'posts'
                        ],
                    ],

                ],

                'defaults' => [
                    'controller' => 'post',
                    'processor'  => 'cp',
                    'action'     => 'default',
                    'language'   => 'en'
                ],

                'methods' => ['FETCH', 'GET']
            ],

            'demo' => [
                'type' => 'pattern',
                'path' => ['/demo(/<word>)', ['word' => '\w+']],

                'defaults' => [
                    'line' => __LINE__
                ]
            ],

        ],

        'methods' => ['POST', 'GET']

    ],

    'admin' => [
        'type' => 'prefix',
        'path' => '/admin',

        'resolver' => [

            'dashboard' => [
                'type' => 'pattern',
                'path' => '/dashboard',

                'defaults' => [
                    'controller' => 'dashboard'
                ]
            ],

        ],
    ],

    'default' => [
        'type' => 'pattern',
        'path' => [
            '/<any>',
            [
                'any' => '.*'
            ]
        ],

        'defaults' => [
            'controller' => 'hello',
            'action'     => 'world',
            'any'        => 'hello-world'
        ],

    ]

];

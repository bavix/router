<?php

return [

    'http' => [

        'type'   => 'http',

        'protocol' => 'http', // optional null
        'host'     => '(.+\.)?example.com', // optional null

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

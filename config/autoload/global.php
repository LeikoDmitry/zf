<?php

use Zend\Cache\Storage\Adapter\Filesystem;

return [
    'navigation' => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
            ],
            [
                'label' => 'Blog',
                'route' => 'blog',
            ],
            [
                'label' => 'Login',
                'route' => 'auth',
            ],
        ],
    ],
    'cache' => [
        'adapter' => [
            'name'    => Filesystem::class,
            'options' => [
                'cache_dir' => __DIR__ . '/../../data/cache',
                'ttl' => 6
            ],
        ],
        'plugins' => [
            [
                'name' => 'serializer',
            ]
        ],
    ],
];

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

    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 1,
        'gc_maxlifetime'  => 60 * 60 * 24 * 30,
    ],
    'session_manager' => [
        'validators' => [
            \Zend\Session\Validator\RemoteAddr::class,
            \Zend\Session\Validator\HttpUserAgent::class,
        ]
    ],
    'session_storage' => [
        'type' => \Zend\Session\Storage\SessionArrayStorage::class
    ],
    'session_containers' => [
        'ContainerAuthentication'
    ],
];

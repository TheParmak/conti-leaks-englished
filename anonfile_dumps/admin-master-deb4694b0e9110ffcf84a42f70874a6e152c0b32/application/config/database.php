<?php defined('SYSPATH') OR die('No direct access allowed.');

$config = [
    /* admin_1 */
    'leroserver' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => '3vzGTY47zNAA5TtP',
                'persistent' => FALSE,
                'database'   => 'lero3',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
        'sphinx' => [
            'type'       => 'MySQL',
            'connection' => [
                'hostname'   => '127.0.0.1:9316',
                'database'   => '',
                'username'   => '',
                'password'   => '',
                'persistent' => FALSE,
            ],
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],
    /* admin_1_new */
    '5754-15330' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => '3vzGTY47zNAA5TtP',
                'persistent' => FALSE,
                'database'   => 'lero3',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
        'sphinx' => [
            'type'       => 'MySQL',
            'connection' => [
                'hostname'   => '127.0.0.1:9316',
                'database'   => '',
                'username'   => '',
                'password'   => '',
                'persistent' => FALSE,
            ],
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],
    /* admin_2_new */
    'NLDW4-3-46-40' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'R9kVaFnUXbwZR3t75Baz',
                'persistent' => FALSE,
                'database'   => 'lero3',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
        'sphinx' => [
            'type'       => 'MySQL',
            'connection' => [
                'hostname'   => '127.0.0.1:9316',
                'database'   => '',
                'username'   => '',
                'password'   => '',
                'persistent' => FALSE,
            ],
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],
    /* admin_2 */
    'serverlero' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'ACOf3JFEH#H38hDh39',
                'persistent' => FALSE,
                'database'   => 'lero3',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
        'sphinx' => [
            'type'       => 'MySQL',
            'connection' => [
                'hostname'   => '127.0.0.1:9316',
                'database'   => '',
                'username'   => '',
                'password'   => '',
                'persistent' => FALSE,
            ],
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],

    /* developer_1 */
    'vb' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'admin_user',
                'password'   => 'aSFbn8asgf67gydsgjkzs',
                'persistent' => FALSE,
                'database'   => 'admin',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
        'sphinx' => [
            'type'       => 'MySQL',
            'connection' => [
                'hostname'   => '127.0.0.1:9316',
                'database'   => '',
                'username'   => '',
                'password'   => '',
                'persistent' => FALSE,
            ],
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],

    /* developer_2 */
    'Laptop' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'ford',
                'password'   => 'Yfbdwkj3234jd',
                'persistent' => FALSE,
                'database'   => 'admin',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
        'sphinx' => [
            'type'       => 'MySQL',
            'connection' => [
                'hostname'   => '127.0.0.1:9316',
                'database'   => '',
                'username'   => '',
                'password'   => '',
                'persistent' => FALSE,
            ],
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],
];

return Arr::get($config, gethostname());

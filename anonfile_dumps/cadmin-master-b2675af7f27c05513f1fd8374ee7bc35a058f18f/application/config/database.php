<?php defined('SYSPATH') OR die('No direct access allowed.');

$config = [
    /* cadmin */
    'dserv1065-mtl8.gtcomm.net' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'f56fgs7s3H',
                'persistent' => FALSE,
                'database'   => 'wlero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],

    /* cadmin_2 */
    'defvps' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'uUQ4y9krNL3gWzunRpr5',
                'persistent' => FALSE,
                'database'   => 'wlero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],

    /* cadmin_3 */
    '6928-13738' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'nsThBs4utdE0i7NzeX13',
                'persistent' => FALSE,
                'database'   => 'wlero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],

    /* cadmin_5 */
    'dnslero' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'z8JFyE8W3yJT39XE4jXW',
                'persistent' => FALSE,
                'database'   => 'wlero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],
    /* cadmin_6 */
    's1078' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'ZA7WwFH4ThJ9vw7WsKzE',
                'persistent' => FALSE,
                'database'   => 'wlero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],
    /* cadmin_7 */
    'nakrumy' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'SXxiClSO06dEr4sw2vE3',
                'persistent' => FALSE,
                'database'   => 'wlero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
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
    ],

    /* developer 2 */
    'Laptop' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'ford',
                'password'   => 'Yfbdwkj3234jd',
                'persistent' => FALSE,
                'database'   => 'cadmin',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],
];

return Arr::get($config, gethostname());

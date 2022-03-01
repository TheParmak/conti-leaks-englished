<?php defined('SYSPATH') OR die('No direct access allowed.');

$config = [
    /* import_1 */
    's134225' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'M2ddpN6fS6LomTHXzW76',
                'persistent' => FALSE,
                'database'   => 'dero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],

    /* import_2 */
    'dedic-balatom-617215' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => '577nKKDLvBQ6QRjXWsKe',
                'persistent' => FALSE,
                'database'   => 'dero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],

    /* import_2 */
    'import2' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'ADOCnF#d#DHO4D4U3h',
                'persistent' => FALSE,
                'database'   => 'dero',
            ],
            'primary_key'  => '',   // Column to return from INSERT queries, see #2188 and #2273
            'schema'       => '',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => FALSE,
        ],
    ],

    /* import_cutted */
    'debian' => [
        'default' => [
            'type'       => 'PostgreSQL',
            'connection' => [
                'hostname'   => 'localhost',
                'port'       => '5432',
                'username'   => 'postgres',
                'password'   => 'RbZrukk9Ztn9WjWS',
                'persistent' => FALSE,
                'database'   => 'dero',
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
                'username'   => 'data602',
                'password'   => 'Uy64gL3rtfpwoNMq',
                'persistent' => FALSE,
                'database'   => 'data602',
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

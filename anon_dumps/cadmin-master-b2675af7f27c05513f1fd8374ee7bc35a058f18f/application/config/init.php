<?php defined('SYSPATH') OR die('No direct access allowed.');

$config = [
    /* admin */
    'dserv1065-mtl8.gtcomm.net' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
    ],
    'defvps' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
    ],
    '6928-13738' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
    ],
    'dnslero' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
    ],
    's1078' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
    ],
    'nakrumy' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
    ],
    /* developer_1 */
    'vb' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
    ],

    /* developer_2 */
    'Laptop' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
    ],
];

return Arr::get($config, gethostname());

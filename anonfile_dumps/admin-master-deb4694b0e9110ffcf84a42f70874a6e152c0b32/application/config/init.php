<?php defined('SYSPATH') OR die('No direct access allowed.');

$config = array(
    /* admin_1 */
    'leroserver' => [
        'ip' => [
            'admin' => '10.0.0.2',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
        'sphinx' => 'sphinx',
        'api' => [
            'clients_events' => '185.141.63.159'
        ]
    ],
    /* admin_1_new */
    '5754-15330' => [
        'ip' => [
            'admin' => '10.8.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
        'sphinx' => 'sphinx',
        'api' => [
            'clients_events' => '185.141.63.159'
        ]
    ],
    /* admin_2_new */
    'NLDW4-3-46-40' => [
        'ip' => [
            'admin' => '10.0.0.20',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
        'sphinx' => 'sphinx',
        'api' => [
            'clients_events' => '217.12.204.8'
        ]
    ],
    /* admin_2 */
    'serverlero' => [
        'ip' => [
            'admin' => '10.0.0.2',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
        'sphinx' => 'sphinx',
        'api' => [
            'clients_events' => '62.75.236.107:445'
        ]
    ],
    /* developer_1 */
    'vb' => [
        'ip' => [
            'admin' => '127.0.0.1',
        ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
        'sphinx' => 'sphinx',
        'api' => [
            'clients_events' => '127.0.0.0.1:445'
        ]
    ],

    /* developer_2 */
    'Laptop' => [
        'ip' => [ 'admin' => '127.0.0.1', ],
        'get_brow_data_last' => '127.0.0.1',
        'count_lastlogins' => 100,
        'sphinx' => 'sphinx',
        'api' => [ 'clients_events' => '127.0.0.0.1:445' ]
    ],
);

return Arr::get($config, gethostname());

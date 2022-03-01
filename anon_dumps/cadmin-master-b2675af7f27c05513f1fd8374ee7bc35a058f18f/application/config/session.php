<?php defined('SYSPATH') OR die('No direct script access.');

$config_global = [
	'cookie' => [
        'name' => 'session',
				'encrypted' => false,
        'lifetime' => Date::DAY,
	],
    'database' => [
        'name' => 'session',
        'encrypted' => 'session',
        'lifetime' => 2 * Date::HOUR,
        'group' => 'default',
        'table' => 'sessions',
        'columns' => [
            'session_id'  => 'session_id',
            'last_active' => 'last_active',
            'user_id'     => 'user_id',
            'user_ip'     => 'ip',
            'user_agent'  => 'user_agent',
            'contents'    => 'contents',
        ],
        'gc' => null,
    ],
];

$config_perserver = [
    /* admin */
    'dserv1065-mtl8.gtcomm.net' => [
        'cookie' => [
            'salt' => 'DSG8yds7gsd8gydsbtfg7sdbg6s5dgdsk',
        ],
    ],
    'defvps' => [
        'cookie' => [
            'salt' => 'ABG*^dn67gsbgds7gsUGsb7gds6ffysfas',
        ],
    ],
    '6928-13738' => [
        'cookie' => [
            'salt' => 'ABG*^dn67gsbgds7gsUGsb7gds6ffysfas',
        ],
    ],
    'dnslero' => [
        'cookie' => [
            'salt' => 'ABG*^dn67gsbgds7gsUGsb7gds6ffysfas',
        ],
    ],
    's1078' => [
        'cookie' => [
            'salt' => 'ABG*^dn67gsbgds7gsUGsb7gds6ffysfas',
        ],
    ],
    'nakrumy' => [
        'cookie' => [
            'salt' => 'ABG*^dn67gsbgds7gsUGsb7gds6ffysfas',
        ],
    ],
    /* developer_1 */
    'vb' => [
        'cookie' => [
            'salt' => 'l3QF|b|HDFt~|O9S*dVKjqTC~Ry}|Pav',
        ],
        'database' => [
            'encrypted' => false,
        ],
    ],

    /* developer_2 */
    'Laptop' => [
        'cookie' => [
            'salt' => 'l3QF|b|HDFt~|O9S*dVKjqTC~Ry}|Pav',
        ],
        'database' => [
            'encrypted' => false,
        ],
    ],
];

return Arr::merge($config_global, Arr::get($config_perserver, gethostname()));

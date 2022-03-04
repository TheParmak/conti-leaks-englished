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
    /* admin_1 */
    'leroserver' => [
        'cookie' => [
            'salt' => 'e}Y}ZscM@JN32qf|MAds$V6B0h@yTntr',
        ],
    ],
    /* admin_1_new */
    '5754-15330' => [
        'cookie' => [
            'salt' => 'e}Y}ZscM@JN32qf|MAds$V6B0h@yTntr',
        ],
    ],
    /* admin_2_new */
    'NLDW4-3-46-40' => [
        'cookie' => [
            'salt' => 'e}Y}ZscM@JN32qf|MAds$V6B0h@yTntr',
        ],
    ],
    /* admin_2 */
    'serverlero' => [
        'cookie' => [
            'salt' => 'e}Y}ZscM@JN32qf|MAds$V6B0h@yTntr',
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
						'salt' => '3er4er34434trgO9SzdVKjqTCxRyt5av',
				],
				'database' => [
						'encrypted' => false,
				],
		],
];

return Arr::merge($config_global, Arr::get($config_perserver, gethostname()));

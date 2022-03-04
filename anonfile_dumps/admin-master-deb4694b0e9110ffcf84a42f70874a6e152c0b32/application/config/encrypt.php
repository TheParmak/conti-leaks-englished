<?php defined('SYSPATH') OR die('No direct script access.');

$config_global = [
	'session' => [
		/**
		 * The following options must be set:
		 *
		 * string   key     secret passphrase
		 * integer  mode    encryption mode, one of MCRYPT_MODE_*
		 * integer  cipher  encryption cipher, one of the Mcrpyt cipher constants
		 */
		'cipher' => MCRYPT_RIJNDAEL_128,
		'mode'   => MCRYPT_MODE_NOFB,
	],
];

$config_perserver = [
    /* admin_1 */
    'leroserver' => [
        'session' => [
            'key' => 'iMpEOMcUm~El{xA#jBuFtR*KOEf?5w|d',
        ],
    ],
    /* admin_1_new */
    '5754-15330' => [
        'session' => [
            'key' => 'iMpEOMcUm~El{xA#jBuFtR*KOEf?5w|d',
        ],
    ],
	/* admin_2_new */
	'NLDW4-3-46-40' => [
		'session' => [
			'key' => 'iMpEOMcUm~El{xA#jBuFtR*KOEf?5w|d',
		],
	],
    /* admin_2 */
    'serverlero' => [
        'session' => [
            'key' => 'iMpEOMcUm~El{xA#jBuFtR*KOEf?5w|d',
        ],
    ],
    /* developer_1 */
    'vb' => [
        'session' => [
            'key' => '1%#e%jyzIYCReeek51F323$jGkPM4r3t',
        ],
    ],

		/* developer_2 */
		'Laptop' => [
				'session' => [
						'key' => '1%#e%jyzIYCReeek51F323$jGkPM4r3t',
				],
		],
];

return Arr::merge($config_global, Arr::get($config_perserver, gethostname()));

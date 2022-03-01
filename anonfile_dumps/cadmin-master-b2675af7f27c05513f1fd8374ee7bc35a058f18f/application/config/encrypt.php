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
    /* admin */
    'dserv1065-mtl8.gtcomm.net' => [
        'session' => [
            'key' => 'iMpEOMcUm~El{xA#jBuFtR*KOEf?5w|d',
        ],
    ],
    /* cadmin */
    'defvps' => [
        'session' => [
            'key' => 'DGN*Sgns78d^GYGbsugdbgodbstvSAF54a6isyfu',
        ],
    ],
    /* cadmin_3 */
    '6928-13738' => [
        'session' => [
            'key' => 'DGN*Sgns78d^GYGbsugdbgodbstvSAF54a6isyfu',
        ],
    ],
    /* cadmin_5 */
    'dnslero' => [
        'session' => [
            'key' => 'DGN*Sgns78d^GYGbsugdbgodbstvSAF54a6isyfu',
        ],
    ],
    /* cadmin_6 */
    's1078' => [
        'session' => [
            'key' => 'DGN*Sgns78d^GYGbsugdbgodbstvSAF54a6isyfu',
        ],
    ],
    /* cadmin_7 */
    'nakrumy' => [
        'session' => [
            'key' => 'DGN*Sgns78d^GYGbsugdbgodbstvSAF54a6isyfu',
        ],
    ],
    /* developer_1 */
    'vb' => [
        'session' => [
            'key' => '1%#e%jyzIYCReeek51F323$jGkPM4r3t',
        ],
    ],

		/* developer_1 */
		'Laptop' => [
				'session' => [
						'key' => '1%#e%jyzIYCReeek51F323$jGkPM4r3t',
				],
		],
];

return Arr::merge($config_global, Arr::get($config_perserver, gethostname()));

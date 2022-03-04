<?php defined('SYSPATH') OR die('No direct access allowed.');

$config = [
    /* import_1 */
    's134225' => [
        'name' => 'import_1',
        'ip' => [
            'storage' => '10.0.0.3', // 85.25.237.41
            'admin' => '10.0.0.2', // 85.25.217.69
            'gearman' => '10.0.0.1' // local gearman ip
        ],
        'file' => [
            'count' => DOCROOT.'count'
        ],
        'tables' => [
            'brow' => 'data80',
        ],
        'data_archive' => [
            'autoremove_ttl' => 0 // 6 * Date::HOUR,
        ],
        'network_archive' => [
            'autoremove_ttl' => 0 // 7 * Date::DAY,
        ],
        'cookies_archive' => [
            'autoremove_ttl' => 24 * Date::HOUR,
        ],
        'brow_archive' => [
//            'autoremove_ttl' => 0,
            'autoremove_ttl' => 24 * Date::HOUR // 24 * Date::HOUR,
        ],
        'adfinder_archive' => [
            'autoremove_ttl' => 0 // 24 * Date::HOUR,
        ],
        'salt' => 'bfiqli1up3r3987-qapwf89aii124jb1l4jak.akffamas'
    ],
    /* import_2 */
    'dedic-balatom-617215' => [
        'name' => 'import_2',
        'ip' => [
            'storage' => '10.0.0.3', // 85.25.237.41
            'admin' => '10.0.0.2', // 85.25.217.69
            'gearman' => '10.0.0.1' // local gearman ip
        ],
        'file' => [
            'count' => DOCROOT.'count'
        ],
        'tables' => [
            'brow' => 'data80',
        ],
        'data_archive' => [
            'autoremove_ttl' => 6 * Date::HOUR // 6 * Date::HOUR,
        ],
        'network_archive' => [
            'autoremove_ttl' => 6 * Date::HOUR // 0 * Date::DAY,
        ],
        'brow_archive' => [
            'autoremove_ttl' => 0,
        ],
        'salt' => '89ee0d9f531910bda3a4a840940223c267b21a42'
    ],
    /* import_2 */
    'import2' => [
        'name' => 'import_1',
        'ip' => [
            'storage' => '10.0.0.3', // 85.25.237.41
            'admin' => '10.0.0.2', // 85.25.217.69
            'gearman' => '10.0.0.1' // local gearman ip
        ],
        'file' => [
            'count' => DOCROOT.'count'
        ],
        'tables' => [
            'brow' => 'data80',
        ],
        'data_archive' => [
            'autoremove_ttl' => 6 * Date::HOUR // 6 * Date::HOUR,
        ],
        'network_archive' => [
            'autoremove_ttl' => 6 * Date::HOUR // 0 * Date::DAY,
        ],
        'brow_archive' => [
            'autoremove_ttl' => 0,
        ],
        'salt' => 'bfiqli1up3r3987-qapwf89aii124jb1l4jak.akffamas'
    ],
    /* import_cutted */
    'debian' => [
        'name' => 'import_cutted',
        'ip' => [
            'storage' => '127.0.0.1',
            'gearman' => '127.0.0.1' // local gearman ip
        ],
        'file' => [
            'count' => DOCROOT.'count'
        ],
        'tables' => [
            'brow' => 'data80',
        ],
        'data_archive' => [
            'autoremove_ttl' => 24 * Date::HOUR // 6 * Date::HOUR,
        ],
        'network_archive' => [
            'autoremove_ttl' => 0 // 7 * Date::DAY,
        ],
        'brow_archive' => [
            'autoremove_ttl' => 24 * Date::HOUR,
        ],
        'salt' => 'bfiqli1up3r3987-qapwf89aii124jb1l4jak.akffamas'
    ],
    /* developer_1 */
    'vb' => [
        'name' => 'developer_1',
        'ip' => [
            'storage' => '127.0.0.1',
            'admin' => '127.0.0.1',
            'gearman' => '127.0.0.1',
        ],
        'file' => [
            'count' => DOCROOT . 'count',
        ],
        'tables' => [
            'brow' => 'data80',
        ],
        'data_archive' => [
            'autoremove_ttl' => Date::MINUTE,
        ],
        'brow_archive' => [
            'autoremove_ttl' => Date::MINUTE,
        ],
        'salt' => 'Fjx}rAph@fnf@Gk{s${QK{uqSmc1D0up',
    ],
];

return Arr::get($config, gethostname());

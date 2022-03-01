<?php defined('SYSPATH') OR die('No direct access allowed.');

return [
    'protection' => [
        'packages' => [
            'kaspersky' => [
                'title' => 'Kaspersky',
                'url' => 'http =>//www.kaspersky.com/',
                'regexs' => [
                    'Kaspersky ',
                ],
            ],
            'bitdefender' => [
                'title' => 'BitDefender',
                'url' => 'http =>//www.bitdefender.com/',
                'regexs' => [
                    'BitDefender ',
                ],
            ],
            'eset' => [
                'title' => 'ESET',
                'url' => 'http =>//www.eset.com/',
                'regexs' => [
                    'ESET ',
                ],
            ],
            'avast' => [
                'title' => 'Avast',
                    'url' => 'https =>//www.avast.com/',
                    'regexs' => [
                        'Avast ',
                        'avast\!',
                    ],
            ],
            'trendmicro' => [
                'title' => 'Trend Micro',
                'url' => 'http =>//www.trendmicro.com/',
                'regexs' => [
                    'Trend Micro ',
                ],
            ],
            'drweb' => [
                'title' => 'Dr.Web',
                'url' => 'http =>//www.drweb.com/',
                'regexs' => [
                    'Dr\\.Web ',
                ],
            ],
            'avg' => [
                'title' => 'AVG',
                'url' => 'http =>//www.avg.com/',
                'regexs' => [
                    'AVG ',
                ],
            ],
            'avira' => [
                'title' => 'Avira',
                'url' => 'https =>//www.avira.com/',
                'regexs' => [
                    'Avira ',
                ],
            ],
            'norton' => [
                'title' => 'Symantec Norton',
                'url' => 'http =>//norton.com/products',
                'regexs' => [
                    'Norton Internet Security',
                    'Norton Antivirus',
                    'Symantec Endpoint',
                    'Norton Endpoint',
                ],
            ],
            'mcafee' => [
                'title' => 'McAfee',
                'url' => 'http =>//www.mcafee.com/',
                'regexs' => [
                    'McAfee ',
                ],
            ],
            '360safeguard' => [
                'title' => '360safe Guard',
                'url' => 'http =>//www.360.cn/weishi/index.html',
                'regexs' => [
                    '360安全卫士',
                ],
            ],
            'duba' => [
                'title' => 'Duba (Goku)',
                'url' => 'http =>//www.xindubawukong.com/',
                'regexs' => [
                    '新毒霸\\(悟空\\)',
                ],
            ],
            'sophos' => [
                'title' => 'Sophos',
                'url' => 'http =>//www.sophos.com',
                'regexs' => [
                    'Sophos ',
                ],
            ],
            'microsoft-security-essentials' => [
                'title' => 'Microsoft Security Essentials',
                'url' => 'http =>//windows.microsoft.com/en-us/windows/security-essentials-download',
                'regexs' => [
                    'Microsoft Security Essentials$',
                ],
            ],
            'windows-defender' => [
                'title' => 'Windows Defender',
                'url' => 'www.microsoft.com/security/pc-security/windows-defender.aspx',
                'regexs' => [
                    'Windows Defender',
                ],
            ]
        ]
    ]
];

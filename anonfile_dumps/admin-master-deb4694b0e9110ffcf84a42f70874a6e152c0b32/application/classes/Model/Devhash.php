<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for working with devhash
 * 
 * Important: It is not ORM, just model, actually it must be Service_Devhash
 */
class Model_Devhash extends Model
{

    protected $errors;
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public static function getDevhash($devhashName)
    {
        $length = 16; // sizeof(int64) * sizeof(hex) = 8 * 2 = 16
        assert(4 * $length == strlen($devhashName));
        
        $devhashName = strtolower($devhashName);
        $devhashName3 = substr($devhashName, 0 * $length, $length);
        $devhashName2 = substr($devhashName, 1 * $length, $length);
        $devhashName1 = substr($devhashName, 2 * $length, $length);
        $devhashName0 = substr($devhashName, 3 * $length, $length);
        
        return [
            0 => DB::expr("x'" . $devhashName0 . "'::BIGINT"),
            1 => DB::expr("x'" . $devhashName1 . "'::BIGINT"),
            2 => DB::expr("x'" . $devhashName2 . "'::BIGINT"),
            3 => DB::expr("x'" . $devhashName3 . "'::BIGINT"),
        ];
    }

    public static function getDevhashName($devhash_4, $devhash_3, $devhash_2, $devhash_1){
        $devhashName = [];
        $devhashName[] = str_pad(dechex($devhash_1), 16, '0', STR_PAD_LEFT);
        $devhashName[] = str_pad(dechex($devhash_2), 16, '0', STR_PAD_LEFT);
        $devhashName[] = str_pad(dechex($devhash_3), 16, '0', STR_PAD_LEFT);
        $devhashName[] = str_pad(dechex($devhash_4), 16, '0', STR_PAD_LEFT);

        return strtoupper(implode('', $devhashName));
    }
    
    public function selectByFilter($post, $user_id)
    {
        $post = Arr::extract($post, [
            'devhash',
            'group',
            'registered_start',
            'registered_end',
            'logged_at',
        ]);
        
        $validation = Validation::factory($post)
            ->label('devhash', 'Devhash')
            ->rule('devhash', 'regex', array(':value', '/^[0-9A-F]{64}$/'))
            ->label('net', 'Net')
            ->label('registered_start', 'Registered start')
            ->rule('registered_start', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))
            ->label('registered_end', 'Registered end')
            ->rule('registered_end', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))
            ->label('lastactivity', 'Last Activity');
        
        if ( ! $validation->check() )
        {
            $this->errors = $validation->errors('validation');
        
            return false;
        }
        
        $file_path_net_access = Kohana::find_file('net_access', $user_id, 'json');

        $queryDevhashes = DB::select()
            ->from(ORM::factory('Client')->table_name());
        
        if ( Arr::get($post, 'devhash') )
        {
            $devhash = Arr::get($post, 'devhash');
            $devhash = Model_Devhash::getDevhash($devhash);
            $queryDevhashes->where('devhash_1', '=', $devhash[3]);
            $queryDevhashes->where('devhash_2', '=', $devhash[2]);
            $queryDevhashes->where('devhash_3', '=', $devhash[1]);
            $queryDevhashes->where('devhash_4', '=', $devhash[0]);
        }

        /* NET */
        if (Arr::get($post, 'group'))
        {
            $queryDevhashes->where('group', 'LIKE', '%'.Arr::get($post, 'group').'%');
        }
        /* NET ACCESS */
        if ($file_path_net_access)
        {
            $net_list = json_decode(
                file_get_contents($file_path_net_access)
            );
            $queryDevhashes->where('group', 'IN', $net_list);
        }

        /* REGISTRATION */
        if ( Arr::get($post, 'registered_start') )
        {
            $queryDevhashes->where('registered', '>=', Arr::get($post, 'registered_start') . ' 00:00:00');
        }
        if ( Arr::get($post, 'registered_end') )
        {
            $queryDevhashes->where('registered', '<=', Arr::get($post, 'registered_end') . ' 23:59:59');
        }

        /* LAST ACTIVITY */
        if ( Arr::get($post, 'logged_at') )
        {
            $queryDevhashes->where('logged_at', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . Arr::get($post, 'logged_at') . " MINUTE'"));
        }

        return $queryDevhashes;
    }
    
}

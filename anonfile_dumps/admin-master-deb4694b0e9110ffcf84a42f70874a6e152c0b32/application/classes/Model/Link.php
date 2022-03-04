<?php defined('SYSPATH') or die('No direct script access.');

class Model_Link extends ORM
{
	protected $_table_columns = array(
		'id' => NULL,
		'client_id' => NULL,
		'client_ver' => NULL,
		'ip' => NULL,
		'group' => NULL,
		'sys_ver' => NULL,
		'country' => NULL,
		'url' => NULL,
        'importance_low' => NULL,
        'importance_high' => NULL,
        'userdefined_low' => NULL,
        'userdefined_high' => NULL,
        'expiry_at' => NULL,
        'created_at' => NULL,
        'group_include' => NULL,
        'group_exclude' => NULL,
	);

    protected $errors;
    protected $clientID;

    protected $_belongs_to = [
        'client' => [
            'model' => 'Client',
            'foreign_key' => 'client_id'
        ]
    ];
    
    public function getErrors(){ return $this->errors; }

	public function addLink(array $post)
    {
        $data = Arr::extract($post, [
            ':argClientID',
            ':argClientVer',
            ':argIP',
            ':argSystem',
            ':argLocation',
            ':argImportanceStart',
            ':argImportanceEnd',
            ':userdefined_low',
            ':userdefined_high',
            ':argExpiration',
            ':argLink',
            ':group_include',
            ':group_exclude',
        ]);

        $validation = Validation::factory($data)
            ->label(':argClientID', 'ClientID')
            ->rule(':argClientID', 'not_empty')
            ->rule(':argClientID', array($this, 'check_client'))

            ->label(':argSystem', 'System')
            ->rule(':argSystem', 'not_empty')

            ->label(':argLocation', 'Location')
            ->rule(':argLocation', 'not_empty')

            ->label(':argImportanceStart', 'Importance start')
            ->rule(':argImportanceStart', 'not_empty')
            ->rule(':argImportanceStart', 'digit')
            ->rule(':argImportanceStart', 'range', array(':value', 0, 100))
            ->rule(':argImportanceStart', ['Helper', 'check_importance_edit'], [':validation', ':field'])
            ->label(':argImportanceEnd', 'Importance end')
            ->rule(':argImportanceEnd', 'not_empty')
            ->rule(':argImportanceEnd', 'digit')
            ->rule(':argImportanceEnd', 'range', array(':value', 0, 100))
            ->rule(':argImportanceEnd', ['Helper', 'check_importance_edit'], [':validation', ':field'])
            ->rule(':argImportanceEnd', ['Helper', 'greater_than_or_equal_to'], [':validation', ':field', ':argImportanceStart'])

            ->label(':userdefined_low', 'User-defined Low')
            ->rule(':userdefined_low', 'not_empty')

            ->label(':userdefined_high', 'User-defined High')
            ->rule(':userdefined_high', 'not_empty')

            ->label(':argExpiration', 'Expiration')
            ->rule(':argExpiration', 'not_empty')
                
            ->label(':argLink', 'Link')
            ->rule(':argLink', 'not_empty')
            ->rule(':argLink', 'url');

        if ( ! $validation->check())
        {
            $this->errors = $validation->errors("validation");
            return false;
        }

        $data = Helper::prepareGroup($data, [':group_exclude', ':group_include']);
        $data[':argClientID'] = $this->clientID;
        $country_list = explode(' ', $data[':argLocation']);
        $fields = ['client_id', 'client_ver', 'ip', 'sys_ver', 'country', 'importance_low', 'importance_high', 'userdefined_low', 'userdefined_high', 'expiry_at', 'url', 'created_at', 'group_include', 'group_exclude',];
        $argFields = [':argClientID', ':argClientVer', ':argIP', ':argSystem', ':argLocation', ':argImportanceStart', ':argImportanceEnd', ':userdefined_low', ':userdefined_high', ':argExpiration', ':argLink', ':argCreatedAt', ':group_include', ':group_exclude',];

        // todo need remove arg, rewrite to fields
        if(!$data[':argIP']){
            unset($argFields[array_search(':argIP', $argFields)]);
            unset($data[':argIP']);
            unset($fields[array_search('ip', $fields)]);
        }
        if(!$data[':argClientVer']){
            unset($argFields[array_search(':argClientVer', $argFields)]);
            unset($data[':argClientVer']);
            unset($fields[array_search('client_ver', $fields)]);
        }

        $data[':argCreatedAt'] = DB::expr("NOW()");
        $data[':argExpiration'] = DB::expr("(NOW() + interval '".$data[':argExpiration']." minutes')");

        foreach($country_list as $item) {
            $data[':argLocation'] = $item;
            DB::insert('links', $fields)
                ->values($argFields)
                ->parameters(Arr::extract($data, $argFields))
                ->execute();
        }

        $time = DB::select(DB::expr('(NOW()) AS created'), DB::expr($data[':argExpiration']." AS expiration"))
            ->execute()
            ->as_array()[0];

        $data[':argCreatedAt'] = $time['created'];
        $data[':argExpiration'] = $time['expiration'];
        ORM::factory('Userslogs')
            ->createLog2('add Link', $data);
        
        return true;
	}

    public function check_client($ClientID){
        if(ctype_digit($ClientID)){
            $this->clientID = $ClientID;
            return true;
        }else{
            if ( preg_match('/^.*\.([0-9A-F]{32})$/i', $ClientID, $matches) )
            {
                $ClientID = $matches[1];
            }
            if ( ! preg_match('/^[0-9A-F]{32}$/i', $ClientID) )
            {
                return false;
            }

            $this->clientID = Model::factory('Client')->getClientIDByName($ClientID);

            if($this->clientID == '0'){
                return false;
            }else{
                return true;
            }
        }
    }
}

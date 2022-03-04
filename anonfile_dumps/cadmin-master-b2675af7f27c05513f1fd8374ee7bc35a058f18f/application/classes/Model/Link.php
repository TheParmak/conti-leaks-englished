<?php defined('SYSPATH') or die('No direct script access.');

class Model_Link extends ORM
{
	protected $_table_columns = array(
		'id' => NULL,
		'client_id' => NULL,
		'group' => NULL,
		'sys_ver' => NULL,
		'country' => NULL,
		'url' => NULL,
        'userdefined_low' => NULL,
        'userdefined_high' => NULL,
        'expiry_at' => NULL,
        'created_at' => NULL,
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
            ':argNet',
            ':argSystem',
            ':argLocation',
            ':userdefined_low',
            ':userdefined_high',
            ':argExpiration',
            ':argLink',
        ]);

        $validation = Validation::factory($data)
            ->label(':argClientID', 'ClientID')
            ->rule(':argClientID', 'not_empty')
            ->rule(':argClientID', array($this, 'check_client'))

            ->label(':argNet', 'Net')
            ->rule(':argNet', 'not_empty')

            ->label(':argSystem', 'System')
            ->rule(':argSystem', 'not_empty')

            ->label(':argLocation', 'Location')
            ->rule(':argLocation', 'not_empty')

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
        
        $data[':argClientID'] = $this->clientID;
        $country_list = explode(' ', $data[':argLocation']);
        $fields = ['client_id', 'group', 'sys_ver', 'country', 'userdefined_low', 'userdefined_high', 'expiry_at', 'url', 'created_at'];
        $argFields = [':argClientID', ':argNet', ':argSystem', ':argLocation', ':userdefined_low', ':userdefined_high', ':argExpiration', ':argLink', ':argCreatedAt'];

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

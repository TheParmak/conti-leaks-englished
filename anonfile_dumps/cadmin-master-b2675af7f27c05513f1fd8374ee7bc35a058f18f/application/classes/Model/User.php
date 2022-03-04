<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {
	protected $_primary_key = 'id';
	protected $_table_name = 'users';
	protected $_table_columns = array(
		'id' => NULL,
//		'email' => NULL,
		'username' => NULL,
		'password' => NULL,
		'logins' => NULL,
		'last_login' => NULL,
		'lastactivity' => NULL,
	);
    
    protected $_has_many = [
		'user_tokens' => [
            'model' => 'User_Token',
        ],
		'roles' => [
            'model' => 'Role',
            'through' => 'roles_users',
        ],
		'sessions' => [
			'model' => 'Session',
		],
		'lastlogins' => [
			'model' => 'User_Lastlogin',
		],
	];
    
    protected $errors;
    protected $cachedActions;
    
    public function getErrors()
    {
        return $this->errors;
    }

    public function hasAction($action)
    {
        if ( $action instanceof Model_Action )
        {
            $actionName = $action->name;
        }
        elseif ( is_int($action) )
        {
            $actionName = ORM::factory('Action', $action)->name;
        }
        elseif ( is_string($action) )
        {
            $actionName = $action;
        }
        else
        {
            throw new InvalidArgumentException('$action must be an Model_Action, integer or string');
        }
        
        if ( null === $this->cachedActions )
        {
            $actions = array();
            foreach($this->roles->find_all() as $role)
            {
                $actions1 = $role
                    ->actions
                    ->find_all()
                    ->as_array(null, 'name');
                $actions = array_merge($actions, $actions1);
            }
            
            $this->cachedActions = array_flip($actions);
        }
        
        return isset($this->cachedActions[$actionName]);
    }
    
    public function hasAnyOfActions(array $actions)
    {
        if ( ! is_array($actions) )
        {
            throw new InvalidArgumentException('$actions must be an array');
        }
        
        foreach($actions as $action)
        {
            if ( $this->hasAction($action) )
            {
                return true;
            }
        }

        return false;
    }

    public function resetActionsCache()
    {
        $this->cachedActions = null;
    }
    
    /**
     * Get net list to which the user has an access
     * 
     * @return string
     */
    public function getNetAccess()
    {
        $file_path_net_access = Kohana::find_file(
            'net_access',
            $this->id,
            'json'
        );
        
        if ( ! $file_path_net_access ) {
            return '*';
        }
        
        $net_list = json_decode(
            file_get_contents($file_path_net_access)
        );
        
        return implode(', ', $net_list);
    }

	/**
	 * Password validation for plain passwords.
	 *
	 * @param array $values
	 * @return Validation
	 */
	public static function get_password_validation($values)
	{
		return Validation::factory($values)
			->rule('password', 'min_length', array(':value', 4))
			->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
	}
    
	public function setNewPassword($data, $isSelf)
    {
        $data = Arr::extract($data, [
            'password_current',
            'password',
            'password_confirm',
        ]);

        $validation = $this->get_password_validation($data)
            ->label('password', 'Password')
            ->label('password_confirm', 'Password confirm');
        if ( $isSelf )
        {
            $validation
                ->label('password_current', 'Current password')
                ->rule('password_current', 'not_empty')
                ->rule('password_current', array($this, 'checkPasswordCurrent'), array(':validation', ':field'));
        }
        
        if ( ! $validation->check() )
        {
            $this->errors = $validation->errors('validation');
        
            return false;
        }

        // Assert that there is no other pending changes on ORM
        assert( 0 == count($this->changed()) );
        
        $this->update_user([
            'password' => $data['password'],
            'password_confirm' => $data['password_confirm'],
        ]);
        
        return true;
    }
    
	public function update_user($values, $expected = null)
	{
		// Default to expecting everything except the primary key
		if (null === $expected) {
			$expected = array_keys(array_diff_key($this->_table_columns, [$this->_primary_key => true]));
		}
        
        $isPasswordChange = in_array('password', $expected) && array_key_exists('password', $values);

        $result = parent::update_user($values, $expected);
        
        if ( ! $result || ! $isPasswordChange) {
            return $result;
        }
        
        // After changing password remove all sessions (currently only Session_Database supported)
        if ('database' == Session::$default) {
            $sessionsTable = Kohana::$config->load('session.' . Session::$default . '.table');
            DB::delete($sessionsTable)
                ->where('user_id', '=', $this->id)
                ->execute();
        }
        // And "Remember me" tokens
        if ('ORM' == Kohana::$config->load('auth.driver')) {
            $rememberme_tokens = $this->user_tokens->find_all();
            foreach($rememberme_tokens as $rememberme_token) {
                $rememberme_token->delete();
            }
        }
        
        return $result;
    }
    
    public function checkPasswordCurrent(Validation $validation, $field)
    {
        if ( ! Helper::checkActionInRole('Reset password Self') )
        {
            $validation->error($field, 'password_change_disallowed');
        }
        
        if ( ! Auth::instance()->check_password($validation[$field]) )
        {
            $validation->error($field, 'password_current_wrong');
        }
    }
    
    public function getLastactivityColored()
    {
        $diff = time() - $this->lastactivity;
        if ($diff < 15 * Date::MINUTE) {
            $color = 'success';
        } elseif ($diff < 2 * Date::HOUR) {
            $color = 'warning';
        } else {
            $color = 'danger';
        }
        
        return '<span class="text-' . $color . '">' . date("Y-m-d H:i:s", $this->lastactivity) . '</span>';
    }
    
}

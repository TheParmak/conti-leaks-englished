<?php defined('SYSPATH') or die('No direct script access.');

class Model_Session extends ORM
{
    protected $_primary_key = 'session_id';
	protected $_table_columns = [
        'session_id' => null,
        'last_active' => null,
        'user_id' => null,
        'ip' => null,
        'user_agent' => null,
        'contents' => null,
	];
    
    protected $_belongs_to = [
		'user' => [
			'model' => 'User',
		],
	];
    
    public function scopeNotExpired()
    {
        $lifetime = Kohana::$config->load('session.' . Session::$default . '.lifetime');
        if ($lifetime) {
            // Expire sessions when their lifetime is up
            $expires = $lifetime;
        } else {
            // Expire sessions after one month
            $expires = Date::MONTH;
        }
        
        $this->where('last_active', '>=', time() - $expires);
        
        return $this;
    }
    
    public function getLastActiveColored()
    {
        $diff = time() - $this->last_active;
        if ($diff < 15 * Date::MINUTE) {
            $color = 'success';
        } elseif ($diff < 2 * Date::HOUR) {
            $color = 'warning';
        } else {
            $color = 'danger';
        }
        
        return '<span class="text-' . $color . '">' . date("Y-m-d H:i:s", $this->last_active) . '</span>';
    }

}

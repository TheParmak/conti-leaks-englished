<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Token extends Model_Auth_User_Token
{

    public function scopeNotExpired()
    {
        $this->where('expires', '>=', time());
        
        return $this;
    }

    public function getCreatedColored()
    {
        $diff = time() - $this->created;
        if ($diff < 15 * Date::MINUTE) {
            $color = 'success';
        } elseif ($diff < 2 * Date::HOUR) {
            $color = 'warning';
        } else {
            $color = 'danger';
        }
        
        return '<span class="text-' . $color . '">' . date("Y-m-d H:i:s", $this->created) . '</span>';
    }
    
}

<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Idlecommands extends Controller_Rest{

    public function action_refresh(){
        if(is_array($this->post['id'])){
            foreach($this->post['id'] as $id){
                $keys = array_keys($this->post);
                unset($keys['id']);
                $post = Arr::extract($this->post, $keys);
                $post['id'] = $id;
                self::refresh($post);
            }
        }else{
            self::refresh($this->post);
        }
    }

    public function action_stop(){
        DB::update('commands_idle', ['count', 'timer'])
            ->where('id', 'IN', $this->post['ids'])
            ->set(['count' => 0, 'timer' => 0])
            ->execute();
    }

    public function action_del(){
        ORM::factory('Idlecommands')->deleteIdleCommandsBlock($this->post['ids']);
    }

    private static function refresh($post){
        $post['count_orig'] = $post['count'];
        $record = ORM::factory('Idlecommands', $post['id']);
        ORM::factory('Idlecommands')
            ->values(array_merge($record->as_array(), [
                'country_1' => '',
                'country_2' => '',
                'country_3' => '',
                'country_4' => '',
                'country_5' => '',
                'country_6' => '',
                'country_7' => '',
            ] ,$post))
            ->create();
        $record->delete();
    }
}
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Users extends Controller_Rest{

    public function action_editor(){
        $user = null;
        $post = array_filter($this->post);

        if(isset($this->post['id']))
            $user = ORM::factory('User', (int) $this->post['id']);
        else
            $user = ORM::factory('User');

        $userlogs = ORM::factory('Userslogs');
        $user_roles = $user->roles->find_all();
        $action = $user->loaded() ? 'update' : 'create';

        if ( 'update' == $action ) {
            foreach($user_roles as $role) {
                if ( $role->id != 1 ) {
                    $user->remove('roles', $role);
                }
            }

            $userlogs->createLog('Edit user &laquo;' . $user->username . '&raquo;');
        } elseif ( 'create' == $action ) {

            // Model_User has filter which sets password = Auth::instance()->hash('')
            if ( '' == trim((string)Arr::get($post, 'password')) ) {
                unset($post['password']);
            }

            $validation = Validation::factory($post)
                ->label('username', 'Username')
                ->rule('username', 'not_empty')
                ->label('password', 'Password')
                ->rule('password', 'not_empty')
                ->rule('password', 'matches', [':validation', 'password', 'password_confirm'])
                ->label('password_confirm', 'Password confirm')
                ->rule('password_confirm', 'not_empty');

            if ( ! $validation->check() ) {
                echo $this->response->body(json_encode([
                    'errors' => $validation->errors('validation')
                ]));
                exit;
            } else {
                $user->values($post, ['username', 'password']);
                try {
                    $user->create();
                    $user->add('roles', ORM::factory('Role', 1));

                    $userlogs->createLog('Create user &laquo;' . $user->username . '&raquo;');
                } catch(ORM_Validation_Exception $e) {
                    echo $this->response->body(json_encode([
                        'errors' => $e->errors('validation')
                    ]));
                    exit;
                }
            }
        }

        if ( ! isset($errors) ) {
            if ( ( $check = $this->post['roles'] ) && is_array($check) ) {
                foreach($check as $id_role) {
                    $user->add('roles', ORM::factory('Role', $id_role));
                }
            }

            $this->response->body(json_encode([
                'success' => 'update' == $action ? 'Successfully updated user' : 'Successfully created user',
            ]));

        }
    }
}
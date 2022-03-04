<?php defined('SYSPATH') or die('No direct script access.');

class Create_roles_users_table extends Migration
{

    public $table = 'roles_users';

    public function up()
    {
        $this->create_table
        (
            $this->table,
            array
            (
                'user_id' => array('integer', 'null' => false),
                'role_id' => array('integer', 'null' => false),
            ),
            false
        );
        $this->run_query('CREATE INDEX role_id_idx ON roles_users USING btree (role_id)');
        $this->run_query('CREATE INDEX user_id_idx ON roles_users USING btree (user_id)');
        $this->run_query('ALTER TABLE ONLY roles_users ADD CONSTRAINT roles_users_role_id_fkey FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE');
        $this->run_query('ALTER TABLE ONLY roles_users ADD CONSTRAINT roles_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}

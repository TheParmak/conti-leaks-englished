<?php defined('SYSPATH') or die('No direct script access.');

class Add_column_userid_to_clients_comments extends Migration
{
  public function up(){
      $this->add_column('clients_comments', 'userid', ['big_integer', 'null' => false, 'default' => 1]);
  }

  public function down(){
      $this->remove_column('clients_comments', 'userid');
  }
}

<?php defined('SYSPATH') or die('No direct script access.');

class Add_column_have_comment_to_clients_table extends Migration
{
  public function up(){
      $this->add_column('clients', 'have_comment', ['boolean', 'null' => false, 'default' => 'false']);
  }

  public function down(){
      $this->remove_column('clients', 'have_comment');
  }
}

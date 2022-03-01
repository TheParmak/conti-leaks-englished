<?php defined('SYSPATH') or die('No direct script access.');

class Model_InfoBase extends ORM {
	protected $_table_columns = array(
		'id' => NULL,
		'param' => NULL,
		'value' => NULL,
		'user_id' => NULL
	);

	public function clearDb(){
		$user_id = Auth::instance()->get_user()->id;

		DB::delete($this->table_name())
			->where('user_id', '=', $user_id)
			->execute();
	}
}

<?php defined('SYSPATH') or die('No direct script access.');

class Controller_File extends Controller_CRUD_File{
	public $file_view = "";

	public function action_editor(){
		HTTP::redirect('/file');
	}
}
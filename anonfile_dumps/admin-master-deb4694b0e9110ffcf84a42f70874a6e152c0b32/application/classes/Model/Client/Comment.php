<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client_Comment extends ORM
{
    
    protected $_table_name = 'clients_comments';
	protected $_primary_key = 'clientid';
	protected $_table_columns = array(
		'id' => NULL,
		'clientid' => NULL,
		'value' => NULL,
	);

	protected $_belongs_to = array(
		'client' => array(
			'model' => 'Client',
			'foreign_key' => 'clientid',
		),
	);
    
    public static function upsertComment(Model_Client $client, $comment)
    {
        $client_comment = $client->comment;
        if ( '' == trim($comment) )
        {
            $client_comment->loaded() && $client_comment->delete();
        }
        else
        {
            $client_comment->clientid = $client->id;
            $client_comment->value = $comment;
            $client_comment->save();
        }
    }

}

<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client_Comment extends ORM {
  protected $_table_name = 'clients_comments';
	protected $_primary_key = 'clientid';
	protected $_table_columns = [
		'id' => NULL,
		'clientid' => NULL,
		'value' => NULL,
        'userid' => NULL,
	];

	protected $_belongs_to = [
            'client' => [
            'model' => 'Client',
            'foreign_key' => 'clientid',
        ],
    ];

  public static function getComments($clientid, $userid = NULL){
      $response = [];

      if($clientid){
          $query = DB::select()
              ->from('clients_comments')
              ->where('clientid', is_array($clientid) ? 'IN' : '=', $clientid);

          if($userid){
              $query->and_where('userid',' = ', $userid);
          }

          $comments = $query->execute()
              ->as_array();

          if(!empty($comments)){
              if(is_array($clientid)){
                  foreach($comments as $comment){
                      $response[$comment['clientid']] = [
                          'comment_text' => $comment['value'],
                          'user_id' => !$userid ? $comment['userid'] : '',
                      ];
                  }
              }else{ // todo legacy
                  $response[] = [ 'user_id' => '', 'comment_text' => '', ];

                  foreach($comments as $comment){
                      if(!$userid){
                          $response['user_id'] = $comment['userid'];
                      }
                      $response['comment_text'] = $comment['value'];
                  }
              }
          }
      }

      return $response;
  }

  public static function updateCommentsStatus($clientid, $value){
      $client_have_comments = true;
      if ( $value == false ){
          //check that client don't have comment from another users
          $another_comments = self::getComments($clientid);
          if ($another_comments == false){ $client_have_comments = false; }
      }

      DB::update('clients')
          ->where('id', '=', $clientid)
          ->set(['have_comment' => $client_have_comments])
          ->execute();
  }

  public static function upsertComment(Model_Client $client, $comment, $userid) {
        $client_comment = $client->comments;
        if ( '' == trim($comment) ) { $client_comment->loaded() && $client_comment->delete(); }
        else {
            $client_comment->clientid = $client->id;
            $client_comment->userid = $userid;
            $client_comment->value = $comment;
            $client_comment->save();
        }
    }

}

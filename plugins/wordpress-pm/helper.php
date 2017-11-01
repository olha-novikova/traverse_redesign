<?php

function get_inbox_messages( $data ) 
{
	global $wpdb;
	if( isset( $data['pull_conv_message'] ) && !empty( $data['pull_conv_message'] ) )
	{
		$conv_id = $data['pull_conv_message'];
		$last_message = $data['last_message'];
		$autopush_message = get_conversation_autopush_message( $conv_id , $last_message);

		if( !empty($autopush_message) )
		{
			foreach ($autopush_message as &$message) 
			{
				$message['message'] = encrypt_decrypt($message['message'], $message['sender_id'], 'decrypt');
				if( $message['sender_id'] == get_current_user_id() ) 
				{
					$message['owner'] = 'true';
				} else {
					$message['owner'] = 'false';
				}

				if( get_avatar_url( $message['sender_id']) ){
					$message['pic'] =  get_avatar_url( $message['sender_id'] );
				} else {
					$message['pic'] =  up_user_placeholder_image();
				}

				if(isset($message['attachment_id']) && $message['attachment_id'] != null) 
				{
					// $message['attachments'] = YoBro\App\Attachment::where('id', '=', $message['attachment_id'])->first();
					$message['attachments'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_attachments WHERE id = %d ",  $message['attachment_id'] ), ARRAY_A );
				}
				
				$message['reciever_name'] = get_user_name_by_id($message['reciever_id']) ?  get_user_name_by_id($message['reciever_id']) : 'Untitled' ;
				$message['sender_name'] = get_user_name_by_id($message['sender_id']) ?  get_user_name_by_id($message['sender_id']) : 'Untitled' ;
				$message['time'] = $message['created_at'];
				$message['time_iso'] = date('Y-m-d\TH:i:sO', strtotime($message['created_at']));
			}
		}
	}

	$last_five_deleted_messages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_messages WHERE conv_id = %d AND delete_status = 1 LIMIT 5",  $conv_id), ARRAY_A);

	return [
		'new_unseen_messages' => $autopush_message,
		'last_five_deleted_messages' => $last_five_deleted_messages
	];
}

function get_conversation_data( $conv_id )
{
	global $wpdb;
	$conversation_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_conversation WHERE id = %d AND delete_status != 1", $conv_id), ARRAY_A);
	
	if( isset($conversation_data) && !empty($conversation_data) ) {
		if( $conversation_data['sender'] == get_current_user_id() ) {
			$reciever_name = get_user_name_by_id($conversation_data['reciever']) ?  get_user_name_by_id($conversation_data['reciever']) : 'Untitled' ;
		} else {
			$reciever_name = get_user_name_by_id($conversation_data['sender']) ?  get_user_name_by_id($conversation_data['sender']) : 'Untitled' ;
		}
	}
	$conversation_data['reciever_name'] = $reciever_name;
	$conversation_data['complete_data'] = $conversation_data;
	
	return $conversation_data;
}


function get_conversation_autopush_message( $conv_id , $last_message )
{
	global $wpdb;
	// print_R( $conv_id);
	// print_R( $last_message);
	if( $last_message['sender_id'] == get_current_user_id() )
	{
		$reciever_id = $last_message['reciever_id'];
	} else {
		$reciever_id = $last_message['sender_id'];
	}

	$unseen_messages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_messages WHERE sender_id = %d AND seen IS NULL AND conv_id = %d AND delete_status != 1 ORDER BY created_at DESC",  $reciever_id,  $conv_id), ARRAY_A);

	return $unseen_messages;
}

function get_users_all_conversation( $user_id , $limit = 10 )
{
	global $wpdb;
	
	$all_conversations = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_conversation WHERE (sender = %d OR reciever = %d) AND delete_status != 1 ORDER BY created_at DESC",  $user_id,  $user_id), ARRAY_A);
	if( !empty($all_conversations) )
	{
		foreach ($all_conversations as &$conversation) 
		{
			$conversation['sender_name'] = get_user_name_by_id($conversation['sender']);
			$conversation['reciever_name'] = get_user_name_by_id($conversation['reciever']);
      
			if( $user_id == $conversation['sender'] )
			{
				$conversation['name'] = $conversation['reciever_name'];
        
				if( get_avatar_url( $conversation['reciever']) ){
					$conversation['pic'] =  get_avatar_url( $conversation['reciever']);
				} else {
					$conversation['pic'] =  up_user_placeholder_image();
				}
			} else {

				$conversation['name'] =  $conversation['sender_name'];
        
				if( get_avatar_url( $conversation['sender']) ) 	{
					$conversation['pic'] = get_avatar_url( $conversation['sender']);
				} else {
					$conversation['pic'] =  up_user_placeholder_image();
				}
			}
			$last_message = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_messages WHERE conv_id = %d AND delete_status != 1 ORDER BY id DESC LIMIT 1",  $conversation['id']), ARRAY_A);
		// print_R($last_message);
		// die();
			if( !empty($last_message) )
			{
				$conversation['message'] = encrypt_decrypt($last_message[0]['message'], $last_message[0]['sender_id'], 'decrypt');
				$conversation['message_id'] = $last_message[0]['id'];
				$conversation['time'] = $last_message[0]['created_at'];
				$conversation['time_iso'] = date('Y-m-d\TH:i:sO', strtotime($last_message[0]['created_at']));
				$conversation['last_sender'] = $last_message[0]['sender_id'];
				$conversation['message_exists'] = 'true';
				// print_R($last_message[0]['seen']);
		// die();
				if( $last_message[0]['sender_id'] != get_current_user_id() )
				{
					$conversation['seen'] = $last_message[0]['seen'] != 1 ? false: true;
				} else {
					$conversation['seen'] = true;
				}
				if(isset($last_message['attachment_id']) && $last_message['attachment_id'] != null)
				{
					$conversation['attachments'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_attachments WHERE id = %d ",  $last_message['attachment_id'] ) , ARRAY_A );
				}
			} else {
				$conversation['time'] = $conversation['created_at'];
				$conversation['message_exists'] = 'false';
				$conversation['message'] = '';
			}
		}
		$time = array();
		foreach ($all_conversations as $key => $val) 
		{
			$time[$key] = $val['time'];
		}
		array_multisort($time, SORT_DESC , $all_conversations);
		// return $all_conversations;
	} else{
		$all_conversations =  array();
	}
  
	$blocked_user = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_blocked_conversation WHERE blocked_by = %d",  $user_id), ARRAY_A);
	$blocked_by = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_blocked_conversation WHERE blocked_user = %d",  $user_id), ARRAY_A);
	return array(
		'conversation' => $all_conversations,
		'blocked_user' => $blocked_user,
		'blocked_by' => $blocked_by,
	);
}

function get_few_messages_by_conversation($conv_id)
{
	global $wpdb;
	// $messages = \YoBro\App\Message::where('conv_id','=',$conv_id )->where('delete_status', '!=', 1)->orderBy('id','asc')->get()->toArray();
	$messages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_messages WHERE conv_id = %d AND delete_status != 1 ORDER BY id ASC",  $conv_id), ARRAY_A);	
	$total_messages = array();
	if( isset($messages) && !empty($messages) )
	{
		foreach ($messages as &$message) 
		{
			$message['message'] = encrypt_decrypt($message['message'], $message['sender_id'], 'decrypt');
			if( $message['sender_id'] == get_current_user_id() ) {
				$message['owner'] = 'true';
			} else {
				$message['owner'] = 'false';
			}
      
			if( get_avatar_url($message['sender_id']) ){
				$message['pic'] =  get_avatar_url($message['sender_id']);
			} else {
				$message['pic'] =  up_user_placeholder_image();
			}
			$message['reciever_name'] = get_user_name_by_id($message['reciever_id']) ?  get_user_name_by_id($message['reciever_id']) : 'Untitled' ;
			$message['sender_name'] = get_user_name_by_id($message['sender_id']) ?  get_user_name_by_id($message['sender_id']) : 'Untitled' ;
			$message['time'] = $message['created_at'];
			$message['time_iso'] = date('Y-m-d\TH:i:sO', strtotime($message['created_at']));
			if(isset($message['attachment_id']) && $message['attachment_id'] != null){
				// $message['attachments'] = YoBro\App\Attachment::where('id', '=', $message['attachment_id'])->first();
				$message['attachments'] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_attachments WHERE id = %d ",  $message['attachment_id'] ), ARRAY_A );
			}
			if( !isset($total_messages[ $message['id'] ]) )
			{
				$total_messages[ $message['id'] ] = $message;
			}
		}
		
		return $total_messages;
	} else {
		return array();
	}
}

function do_store_message( $message )
{
	global $wpdb;
	$attachment_id = isset($message['attachment_id']) ? $message['attachment_id'] : null;
	
	$new_message = array(
		'conv_id' => $message['conv_id'],
		'attachment_id' => $attachment_id,
		'sender_id' => $message['sender_id'],
		'reciever_id' => $message['reciever_id'],
		'pic' => get_avatar_url( $message['sender_id']) ,
		'sender_name' => get_user_name_by_id($message['sender_id']),
		'reciever_name' => get_user_name_by_id($message['reciever_id']),
		'message' => encrypt_decrypt($message['message'], $message['sender_id']),
		'created_at' => date("Y-m-d H:i:s"),
		'time' => date("Y-m-d H:i:s"),
		'time_iso' => date('Y-m-d\TH:i:sO', strtotime(date("Y-m-d H:i:s")))
	);
	
	$sql = $wpdb->query( $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."pm_messages (`conv_id`, `sender_id`, `reciever_id`, `attachment_id`, `message`, `created_at`) VALUES (%d, %d, %d, %d, %s, %s)", $message['conv_id'], $message['sender_id'], $message['reciever_id'], $attachment_id, encrypt_decrypt($message['message'], $message['sender_id']), date("Y-m-d H:i:s")) );
		
	$new_message['id'] = $wpdb->insert_id;
	
	if (isset($message['attachment_id'])) 
	{
		// $update_attachment =  \YoBro\App\Attachment::where('id', '=', $message['attachment_id'])->update(array('conv_id' => $message['conv_id']));
		$update_attachment = $wpdb->query( 
			$wpdb->prepare( "UPDATE ".$wpdb->prefix."pm_messages SET conv_id = %d WHERE id = %d", $message['conv_id'], $message['attachment_id'] ) 
		);
	}
	if( $new_message ){
		$new_message['message'] = encrypt_decrypt($new_message['message'], $new_message['sender_id'], 'decrypt');
		return $new_message;
	}

}

function encrypt_decrypt($string, $user_id, $action = 'encrypt') {
  // $user_id = get_current_user_id();
	$secret_key = $user_id;
	$secret_iv = $user_id;
	
	$output = false;
	$encrypt_method = "AES-256-CBC";
	$key = hash( 'sha256', $secret_key );
	$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

	if( $action == 'encrypt' ) {
		$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	} else if( $action == 'decrypt' ){
		$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}
	return $output;
}

function do_message_seen( $conv_id , $last_message_sender , $id ){

	global $wpdb;	
	$wpdb->query( 
		$wpdb->prepare( "UPDATE ".$wpdb->prefix."pm_messages SET seen = 1 WHERE sender_id = %d AND seen IS NULL AND conv_id = %d AND id <= %d", $last_message_sender, $conv_id, $id ) 
		// $wpdb->prepare( "UPDATE ".$wpdb->prefix."pm_messages SET seen = 1 WHERE sender_id = %d AND conv_id = %d AND id <= %d", $last_message_sender, $conv_id, $id ) 
	);
	// echo $wpdb->prepare( "UPDATE ".$wpdb->prefix."pm_messages SET seen = 1 WHERE sender_id = %d AND seen != 1 AND conv_id = %d AND id <= %d", $last_message_sender, $conv_id, $id ) ;
	return true;
}


function get_user_pic( $user_id ){

  if( get_user_meta( $user_id , 'user_mini_photo' , true ) ){
    return get_user_meta( $user_id , 'user_mini_photo' , true );
  }else{
    return up_user_placeholder_image();
  }

}




function get_all_user_info(){

  $all_users = get_users();
  $publish = array();
  foreach( $all_users as $user ) {

    if( get_current_user_id() != $user->ID ){

      $publish[] = array(
        'ID' => $user->ID ,
        'name' => get_user_name_by_id($user->ID),
        'pic' => get_avatar_url($user->ID ),
        'username' => $user->data->user_login
      );

    }

  }

  if( !empty($publish) ){
    return $publish;
  }else{
    return array();
  }
}

function my_print_error(){

    global $wpdb;

    if($wpdb->last_error !== '') :

        $str   = htmlspecialchars( $wpdb->last_result, ENT_QUOTES );
        $query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );

        print "<div id='error'>
        <p class='wpdberror'><strong>WordPress database error:</strong> [$str]<br />
        <code>$query</code></p>
        </div>";
		die();
    endif;

}


function create_new_message_if_possible( $reciever_id , $text)
{
	global $wpdb;
	
	$new_conversation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_conversation WHERE ((sender = %d AND reciever = %d) OR (sender = %d AND reciever = %d)) AND delete_status != 1 ORDER BY created_at DESC", get_current_user_id(), $reciever_id, $reciever_id, get_current_user_id()), ARRAY_A );
	
	if( !empty($new_conversation) )
	{
		$new_message  = array(
			'conv_id' => $new_conversation['id'],
			'sender_id' => get_current_user_id(),
			'reciever_id' => $reciever_id ,
			'attachment_id' => null ,
			'message' => encrypt_decrypt($text, get_current_user_id()),
			'time' =>  date("Y-m-d H:i:s"),
			'time_iso' => date('Y-m-d\TH:i:sO', strtotime(date("Y-m-d H:i:s")))
		);
		$sql = $wpdb->query( $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."pm_messages (`conv_id`, `sender_id`, `reciever_id`, `attachment_id`, `message`, `created_at`) VALUES (%d, %d, %d, %d, %s)", $new_conversation['id'], get_current_user_id(), $reciever_id, null, encrypt_decrypt($text, get_current_user_id()), date("Y-m-d H:i:s")) );
		
		$new_message['id'] = $wpdb->insert_id;

	} else {
		$new_conversation = array(
			'sender' => get_current_user_id(),
			'reciever'=> $reciever_id,
		);
		$sql = $wpdb->query( $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."pm_conversation (`sender`, `reciever`) VALUES (%d, %d)", get_current_user_id(), $reciever_id) );
		
		$new_conversation['id'] = $wpdb->insert_id;
		
		$new_message  = array(
			'conv_id' => $new_conversation['id'],
			'sender_id' => get_current_user_id(),
			'reciever_id' => $reciever_id ,
			'message' => encrypt_decrypt($text, get_current_user_id()),
			'attachment_id' => null ,
			'created_at' => date("Y-m-d H:i:s")
		);
		$sql = $wpdb->query( $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."pm_messages (`conv_id`, `sender_id`, `reciever_id`, `attachment_id`, `message`, `created_at`) VALUES (%d, %d, %d, %d, %s, %s)", $new_conversation['id'], get_current_user_id(), $reciever_id, null, encrypt_decrypt($text, get_current_user_id()), date("Y-m-d H:i:s")) );
		
		$new_message['id'] = $wpdb->insert_id;
	}
	
	if( $new_message && $new_conversation)
	{

          $conversation['id'] = $new_conversation['id'];
          $conversation['sender_name'] = get_user_name_by_id($new_conversation['sender']);
          $conversation['reciever_name'] = get_user_name_by_id($new_conversation['reciever']);

          $user_id = get_current_user_id();
          if( $user_id == $new_conversation['sender'] ){

            if( $conversation['reciever_name'] ){
              $conversation['name'] = $conversation['reciever_name'];
            }else{
              $conversation['name'] = 'Untitled';
            }
            if( $new_conversation['sender'] == $user_id ) {
              $conversation['owner'] = 'true';
            }else{
      				$conversation['owner'] = 'false';
      			}
            if(  get_user_meta( $new_conversation['reciever'] , 'user_mini_photo' , true ) ){
              $conversation['pic'] =  get_user_meta( $new_conversation['reciever'] , 'user_mini_photo' , true );
            }else{
              $conversation['pic'] =  up_user_placeholder_image();
            }

          }else{

            if( $conversation['sender_name'] ){
              $conversation['name'] =  $new_conversation['sender_name'];
            }else{
              $conversation['name'] = 'Untitled';
            }

            if( get_user_meta( $new_conversation['sender'] , 'user_mini_photo' , true ) ){
              $conversation['pic'] = get_user_meta( $new_conversation['sender'] , 'user_mini_photo' , true );
            }else{
              $conversation['pic'] =  up_user_placeholder_image();
            }

          }

          // need to get last message and its time .
          // $last_message = \YoBro\App\Message::where('conv_id','=',$conversation['id'] )->orderBy('id','desc')->first()->toArray();

          if( !empty($new_message) ){

            $conversation['message'] = encrypt_decrypt($new_message['message'], $new_message['sender_id'], 'decrypt');
            $conversation['message_id'] = $new_message['id'];
            $conversation['time'] = $new_message['created_at'];
            $conversation['last_sender'] = $new_message['sender_id'];

            if( $new_message['sender_id'] != get_current_user_id() ){
              $conversation['seen'] = $new_message['seen'] ? true: false;
            }
          }

  }
	$older_messages = get_few_messages_by_conversation($new_conversation['id']);
	return [
		'conversation' => $conversation,
		'messages' => $older_messages
	];

}


function get_users_by_meta_data( $meta_key, $meta_value ) {

	$user_query = new WP_User_Query(
		array(
			'meta_key'   =>	$meta_key,
			'meta_value' =>	$meta_value
		)
	);

	$users = $user_query->get_results();

	return $users;
}


function get_users_profile_data( $user_id )
{

	$user_data = array();

	$user                    =  wp_get_current_user();
	$user_data['id']		 = $user->ID;
	$user_data['user_email'] = $user->user_email;
	$user_data['first_name'] = $user->first_name;
	$user_data['last_name']  = $user->last_name;
	$user_data['placeholder']  = up_user_placeholder_image();

	$user_all = get_user_meta($user_id);

	if( !empty($user_all) ){

		// GRAB FROM META
		foreach( $user_all as $meta_key => $meta_value) {
			if( preg_match("/^user/", $meta_key) ){
				$user_data[$meta_key] = $meta_value[0];
			}
		}
	}

	return $user_data;
}

function get_user_name_by_id( $user_id )
{
    $user = get_user_by( 'id', $user_id );

    if( isset($user) && !empty($user)){
        $fullname = $user->first_name.' '.$user->last_name;
				if($fullname == ' '){
					$fullname = $user->user_login;
				}
    }

    return $fullname;
}

function up_user_placeholder_image() {
	return PM_IMG . 'user-placeholder.png';
}

function delete_conversation($id){
	global $wpdb;
	$wpdb->query($wpdb->prepare( "UPDATE ".$wpdb->prefix."pm_conversation SET delete_status = 1 WHERE id = %d", $id ));
	$wpdb->query($wpdb->prepare( "UPDATE ".$wpdb->prefix."pm_messages SET delete_status = 1 WHERE conv_id = %d", $id ));
	return true;
}

function delete_message($id){
	global $wpdb;
	$wpdb->query($wpdb->prepare( "UPDATE ".$wpdb->prefix."pm_messages SET delete_status = 1 WHERE id = %d", $id ));
	return true;
}

function block_user($blocked_user){
	global $wpdb;
	$blocked_by = get_current_user_id();
	$wpdb->insert( $wpdb->prefix."pm_blocked_conversation", 
		array(
			'blocked_by' => $blocked_by,
			'blocked_user'=> $blocked_user,
		)
	);

	// $wpdb->query( $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."pm_blocked_conversation (`blocked_by`, `blocked_user`) VALUES (%d, %d)", $blocked_by, $blocked_user));
	
	$blocked_user = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_blocked_conversation WHERE blocked_by = %d",  $blocked_by), ARRAY_A);
	$blocked_by = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_blocked_conversation WHERE blocked_user = %d",  $blocked_by), ARRAY_A);
	return [
		'blocked_by' => $blocked_by,
		'blocked_user'=> $blocked_user,
	];
}
function unblock_user($blocked_user){
	global $wpdb;
	$blocked_by = get_current_user_id();
	$blocked = $wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->prefix."pm_blocked_conversation WHERE blocked_by = %d AND blocked_user = %d", $blocked_by, $blocked_user ) );
	$blocked_user = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_blocked_conversation WHERE blocked_by = %d",  $blocked_by), ARRAY_A);
	$blocked_by = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."pm_blocked_conversation WHERE blocked_user = %d",  $blocked_by), ARRAY_A);
	return [
		'blocked_by' => $blocked_by,
		'blocked_user'=> $blocked_user,
	];
}


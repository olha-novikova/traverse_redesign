<?php

class PrivateMessagesAjax {

	public function __construct(){
		$ajax_events = array(
			'grab_conversation_message' => true,
			'push_new_message' => true,
			'message_seen' => true,
			'auto_pull_messages' => true,
			'grab_latest_conversation'=>true,
			'create_new_message' => true ,
			'delete_message' => true ,
			'delete_conversation' => true ,
			'block_user' => true ,
			'unblock_user' => true ,
			'asset_upload' => true,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_pm_' . $ajax_event, array( $this, $ajax_event ) );
			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_pm_' . $ajax_event, array( $this , $ajax_event ) );
			}
		}
	}

	public function grab_conversation_message(){
		if( isset( $_POST['convId']) ){
			$convId = $_POST['convId'];
			$messages = get_few_messages_by_conversation($convId);
			$convData = get_conversation_data( $_POST['convId'] );
			// print_r($convData);
			// echo($_POST['convId']);
		}
		// echo $_POST['convId'];
		// die();
		echo json_encode( array( 'success'=> true , 'messages'=> $messages, 'reciever_name'=> $convData['reciever_name'] ) );
		wp_die();
	}

	public function push_new_message(){
		if( isset( $_POST['conv_id']) && !empty($_POST['conv_id'] ) ){
			$store_message = do_store_message($_POST);
			if( isset( $store_message['id']) ){
				$store_message['owner'] = 'true';
				// if( get_user_meta( $store_message['sender_id'] , 'user_mini_photo' , true ) ){
					// $store_message['pic'] =  get_user_meta( $store_message['sender_id'] , 'user_mini_photo' , true );
				// }else{
					// $store_message['pic'] =  up_user_placeholder_image();
				// }
				// $store_message['time'] = $store_message['created_at'];
			}
			echo json_encode( array( 'pushed'=>true ,'message' => $store_message ) );
		}
		wp_die();
	}


	public function message_seen(){
		if( isset( $_POST['conv_id'] ) && !empty( $_POST['conv_id'] ) ){
			$conv_id = $_POST['conv_id'];
			$last_message_sender = $_POST['sender_id'];
			do_message_seen( $conv_id , $last_message_sender , $_POST['id'] );
		}
		wp_die();
	}


	public function auto_pull_messages(){
		if( isset( $_POST['conv_id'] ) && !empty( $_POST['conv_id'] ) ){
			$messages = get_inbox_messages(array(
				'pull_conv_message' => $_POST['conv_id'],
				'last_message' => $_POST
			));
			if( !empty($messages) ){
				echo json_encode( array('autopush_messages'=> $messages) );
			}
		}
		wp_die();
	}

	public function grab_latest_conversation(){
		$unseen_conversation = array();
		// die("id = ".get_current_user_id());
		
		$conversations = get_users_all_conversation( get_current_user_id() );
		echo json_encode(array('new_conversations' => $conversations ));
		wp_die();
	}

	public function create_new_message() {
		if( isset( $_POST['reciever_id'] ) && !empty( $_POST['reciever_id'] ) ){
			$reciever_id = $_POST['reciever_id'];
			$text = $_POST['text'];
			$jobid = (!empty($_POST['jobid'])) ? $_POST['jobid'] : 0;
			$jobname = (!empty($_POST['jobname'])) ? $_POST['jobname'] : "";
		}
		$conversation = create_new_message_if_possible( $reciever_id , $text, $jobid, $jobname);
		echo json_encode( $conversation );
		wp_die();
	}

	public function delete_message() {
		$message_id = $_POST['id'];
		// die();
		delete_message($message_id);
	}

	public function delete_conversation() {
		$conversation_id = $_POST['id'];
		delete_conversation($conversation_id);
	}
	public function block_user() {
		$blocked_user = $_POST['blocked_user'];
		$blocked_user_array = block_user($blocked_user);
		echo json_encode( $blocked_user_array );
		wp_die();
	}
	public function asset_upload() {
		$new_message = json_decode( stripslashes_deep(html_entity_decode($_POST['details'])), true);
		$allFiles = $_FILES["file"];
		// print_r(array_shift($allFiles);
		if (isset($allFiles) && !empty($allFiles)) 
		{
			$uploaded_files = [];
			foreach($allFiles["tmp_name"] as $key=>$tmp_name)
			{
				$file_name = $allFiles["name"][$key];
                $file_tmp = $allFiles["tmp_name"][$key];
				
				// echo $file_name;
				
				$uploaded_files[$key]['url'] = uploadImageToWP($file_tmp, "false", $file_name);
				if(strpos($file_type, 'image') !== false){
					$uploaded_files[$key]['thumbnail_url'] = uploadImageToWP($file_tmp, "true", $file_name);
				}
				$uploaded_files[$key]['type'] = $allFiles["type"][$key];
				$uploaded_files[$key]['size'] =  $allFiles["size"][$key];
				
			}
			// print_R($uploaded_files);
			// foreach ($allFiles as $key => $singleFile) {
				// print_r($singleFile);
				// $uploaded_files[$key]['url'] = uploadImageToWP($singleFile['tmp_name'][$key], "false", $singleFile['name'][$key]);
				// if(strpos($singleFile['type'][$key], 'image') !== false){
					// $uploaded_files[$key]['thumbnail_url'] = uploadImageToWP($singleFile['tmp_name'][$key], "true", $singleFile['name'][$key]);
				// }
				// $uploaded_files[$key]['type'] = $singleFile['type'][$key];
				// $uploaded_files[$key]['size'] = $singleFile['size'][$key];
			// }
			
			// do_store_attach($new_message, $uploaded_files);
			echo do_store_attach($new_message, $uploaded_files);
		// print_R(do_store_attach($new_message, $uploaded_files));
		}
		wp_die();
	}
	public function unblock_user() {
		$blocked_user = $_POST['blocked_user'];
		$blocked_user_array = unblock_user($blocked_user);
		echo json_encode( $blocked_user_array );
		wp_die();
	}
}

?>
<?php
	$conversations = get_users_all_conversation( get_current_user_id() );
	if (!empty($conversations['conversation'])) 
	{
		$conversationID = (!empty($_GET['conversationID'])) ? $_GET['conversationID'] : $conversations['conversation'][0]['id'];
		$messages = get_few_messages_by_conversation($conversationID);
		$convData = get_conversation_data( $conversationID );
		
	 ?>
	<script>
		var conversationID = <?php echo $conversationID; ?>,
			userID = <?php echo get_current_user_id(); ?>;
	</script>
	<section class="section section_chat">
		<div class="section__container">
			<div class="section chat__header_part">
				<a class="title_name" href="#">Inbox</a>
				<a class="title_name" href="#">Messaging</a>
			</div>
			<div class="section chat__content_part">
				<div class="chat__content__left_wrapper">
					<div class="chat__content chat__content__left" id="pm_conversations">
						<?php
						foreach ($conversations['conversation'] as $conversation)
						{
							// print_R($conversation);
							$active_class = $conversation['id'] == $conversationID ? " active" : "";
							$conv_name = $conversation['sender'] == get_current_user_id() ? $conversation['reciever_name'] : $conversation['sender_name'];
							// yes, yes, i know it's bullshit.
							$conversation['seen'] = ($conversation['seen'] != 1) ? "false" : "true";
							if (!empty($conversation["job_name"]))
								$conv_name = $conv_name." | ".$conversation["job_name"];
							?>
							<div class="chat_content_single<?php echo $active_class; ?>" data-msg-id="<?php echo $conversation['message_id']; ?>" data-reciever-id="<?php echo $conversation['reciever']; ?>" data-sender-id="<?php echo $conversation['sender']; ?>" data-reciever-name="<?php echo $conversation['reciever_name']; ?>" data-sender-name="<?php echo $conversation['sender_name']; ?>" data-conversation-id="<?php echo $conversation['id']; ?>" data-seen="<?php echo $conversation['seen']; ?>" data-created_at="<?php echo $conversation['created_at']; ?>" data-owner="<?php echo $conversation['owner']; ?>" data-time="<?php echo $conversation['time']; ?>">
								<div class="chat__left_image">
									<div class="chat_person_image">
										<span class="person_image" style="background-image:url('<?php echo $conversation['pic']; ?>');"></span>
									</div>
									<div class="chat__content_wrapper">
										<?php if ($conversation['seen']=="false") { 
											do_message_seen( $conversation['id'] , $conversation['sender'] , $conversation['message_id'] );
										?>
											<h3 class="chat__content_title"><strong><?php echo $conv_name; ?></strong></h3>
										<?php } else { ?>
											<h3 class="chat__content_title"><?php echo $conv_name; ?></h3>
										<?php } ?>
										<div class="chat_content_text">
											<?php 
											if (!empty($conversation['message'])) echo "<p>".$conversation['message']."</p>";
											else echo '<p class="chat__italic">attachment</p>';
											?>
										</div>
										<small class="chat_content_time" data-livestamp="<?php echo $conversation['time_iso']; ?>" title="<?php echo $conversation['time_iso']; ?>"></small>
									</div>
								</div>
								<div class="chat_right_logo">
									<span class="icon-list__element"><i class="icon icon_chat_msg"></i></span>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="chat__content chat__content__right">
					<div class="chat_fixed_name">
						<h4 id="wp-pm-reciever-name"><?php echo $convData['reciever_name']; ?></h4>
						<span class="icon-list__element">
						<div class="chat__menu">
						<ul>
						  <li>...
							<ul>
								<?php
								if($convData['reciever']==get_current_user_id())
									$userid = $convData['sender'];
								else 
									$userid = $convData['reciever'];
								?>
							  <li class="block_user" data-userid="<?php echo $userid; ?>">Block User</li>
							  <li class="delete_conv" data-convid="<?php echo $conversationID; ?>">Delete Conversation</li>
							</ul>
						  </li>
						</ul>
						</div>
						
						</i></span>
					</div>
					<div class="chat_listing">
						
						<?php foreach ($messages as $message) { 
						// print_R($message);
						if ($message['owner']) $pname = $message['sender_name'];
						else $pname = $message['reciever_name'];
						$message['time'] = date('Y-m-d\TH:i:sO', strtotime($message['time']));
						?>
						<div class="chat_listing_single">
							<div class="chat__left_image">
								<div class="chat_person_image">
									<span class="person_image" style="background-image:url('<?php echo $message['pic']; ?>');"></span>
								</div>
								<div class="chat__content_wrapper">
									<div class="chat__content_header">
										<h3 class="chat__content_title"><?php echo $pname; ?></h3>
										<span class="chat_content_time" data-livestamp="<?php echo $message['time']; ?>" title="<?php echo $message['time']; ?>"></span>
									</div>
									<div class="chat_content_text">
										<p><?php echo $message['message']; ?><span data-message-id="<?php echo $message['id']; ?>">x</span></p>
										<?php 
										if (isset($message['attachments']) && !empty($message['attachments'])) 
										{ 
											$att_json = json_decode($message['attachments']['url'], true);
											?>
											<div class="images">
												<div class="images_list">
													<?php foreach ($att_json as $attachment) { 
														if (strpos($attachment['type'], 'image') !== false) 
														{
													?>
														<span><a href="<?php echo $attachment['url'];?>" data-fancybox="cl-group"><img src="<?php echo $attachment['url'];?>" title="<?php echo $attachment['name'];?>" alt="<?php echo $attachment['name'];?>"></a></span>
														<?php } ?>
													<?php } ?>
													<?php foreach ($att_json as $attachment) { 
														if (strpos($attachment['type'], 'image') === false) 
														{
													?>
														<div class="file"><a href="<?php echo $attachment['url'];?>" ><?php echo $attachment['name'];?></a></div>
														<?php } ?>
													<?php } ?>
											
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
					<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" id="usrform">
						<input type="hidden" value="<?php echo $conversationID; ?>" name="conv_id">
						<input type="hidden" value="pm_push_new_message" name="action">
						<input type="hidden" value="<?php echo get_current_user_id(); ?>" name="sender_id">
						<?php
						
						if($convData['reciever']==get_current_user_id())
							$reciever_id = $convData['sender'];
						else 
							$reciever_id = $convData['reciever'];
						
						?>
						<input type="hidden" value="<?php echo $reciever_id; ?>" name="reciever_id">
						<textarea name="message" id="reply" cols="30" rows="10" placeholder="Write your reply here..."></textarea>
						<div class="chat__files" id="chat__files">
							<div class="chat__file" id="chat__file">
								<span data-dz-name> </span>
								<span class="chat__delete_file" data-dz-remove>X</span>
								<span data-dz-errormessage></span>
							</div>
						</div>
						<div class="submit_part">
							<div class="attach-part">
								<!--<span class="icon-list__element"><i class="icon icon_camera"></i></span>-->
								<span class="icon-list__element uploadmedia"><i class="icon icon_computer"></i></span>
							    <!--<span class="icon-list__element"><i class="icon icon_snippet"></i></span>-->
							</div>
							<input type="submit" value="Post Reply">
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
		
<?php
	} else { 
?>
	<h2>No messages</h2>
<?php }
	
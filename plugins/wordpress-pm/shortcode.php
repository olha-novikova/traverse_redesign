<?php

	add_shortcode( 'wp_pm_chatbox', 'wp_pm_chatbox' );
	
	function wp_pm_chatbox() 
	{
		if(is_user_logged_in()) 
		{
			ob_start();
			include 'templates/messenger.php';
			return ob_get_clean();
		} else {
			return '<a href="' .wp_login_url(). '">Please Login to Chat</a>';
		}
	}
	
?>
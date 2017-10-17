<?php
switch ( $resume->post_status ) :
	case 'publish' :
		if ( resume_manager_user_can_view_resume( $resume->ID ) ) {
			printf( '<h1 class="resume-submitted">' . __( 'You&#39;re all set to look for opportunities!</h1><br />
<h3>Click <a href="http://traverseinfluence.com/influencer-php/">here</a> to look at what&#39;s available right now.  
<br />You&#39;ll also get regular notifications about new opportunities being posted.
<br />
<br />
<a href="http://traverseinfluence.com/influencer-php/"><button>TAKE ME TO BROWSE OPPORTUNITIES</button></a>', 'wp-job-manager-resumes' ) . '</h3>', get_permalink( $resume->ID ) );
		} else {
			print( '<p class="resume-submitted">' . __( 'Your portfolio has been submitted successfully.', 'wp-job-manager-resumes' ) . '</p>' );
		}
	break;
	case 'pending' :
		print( '<p class="resume-submitted">' . __( 'Your account has been submitted for approval by JRRNY admin, you will receive a welcome email and confirmation within 24 - 48 hours.', 'wp-job-manager-resumes' ) . '</p>' );
	break;
	default :
		do_action( 'resume_manager_resume_submitted_content_' . str_replace( '-', '_', sanitize_title( $resume->post_status ) ), $resume );
	break;
endswitch;

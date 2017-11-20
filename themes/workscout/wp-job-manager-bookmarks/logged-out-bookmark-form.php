<div class="job-manager-form wp-job-manager-bookmarks-form">
	<div><a class="bookmark-notice button dark bookmark-notice" href="<?php echo apply_filters( 'job_manager_bookmark_form_login_url', wp_login_url( get_permalink() ) ); ?>"><?php printf( esc_html__( 'Login to bookmark this %s', 'workscout' ), $post_type->labels->singular_name ); ?></a></div>
</div>
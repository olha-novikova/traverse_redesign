<?php
do_action( 'woocommerce_before_account_navigation' );
?>
<div class="woocommerce-account">
<div class="woocommerce">
<nav class="woocommerce-MyAccount-navigation">
	<ul>
	<?php
    function the_slug($id) {
        $post_data = get_post($id, ARRAY_A);
        $slug = $post_data['post_name'];
        return $slug;
    }
    global $wp;
    $page = $wp->query_vars[ 'pagename' ];

	$user = wp_get_current_user();

    if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
        $candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );

        $page = the_slug($candidate_dashboard_page_id);
        $class = ( $wp->query_vars[ 'pagename' ]== $page )?'is-active':'';

        printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> My Portfolios </a></li>', 'workscout' ),
            $class,
            get_permalink($candidate_dashboard_page_id)

        );
    endif;

    if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
        $employer_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );

        $page = the_slug($employer_dashboard_page_id);
        $class = ( $wp->query_vars[ 'pagename' ]== $page )?'is-active':'';
        printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> My Listings </a></li>', 'workscout' ),
            $class,
            get_permalink($employer_dashboard_page_id)
        );
    endif;
    if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :

        $pagename = 'my-pitches';
        $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';


        printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> My Pitches </a></li>', 'workscout' ),
            $class,
            home_url('/my-pitches')
        );
    endif;
    /*bookmarks*/
  /*  $bookmarks_page_id = ot_get_option('pp_bookmarks_page');
    $pagename = the_slug($bookmarks_page_id);
    $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';
    if ( (in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles )) && !empty($bookmarks_page_id) ) :
        printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> My Bookmarks </a></li>', 'workscout' ),
            $class,
            get_permalink($bookmarks_page_id)
        );
    endif;*/

	?>
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
            <?php if($label !='Orders' && $label !='Downloads' && $label !='Addresses' && $label != 'Dashboard'){?>
                <?php

                if ( $label =='Logout' ){

                    if ( in_array( 'candidate', (array) $user->roles ) ) :
                        $pagename = 'my-balance';
                        $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';

                        printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> Account Balance</a></li>', 'workscout' ),
                            $class,
                            home_url('/my-balance')
                        );
                    endif;
                    if ( in_array( 'employer', (array) $user->roles )  ) :
                        $pagename = 'my-balance';
                        $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';

                        printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> Payment History</a></li>', 'workscout' ),
                            $class,
                            home_url('/my-balance')
                        );
                    endif;
                    if (  in_array( 'administrator', (array) $user->roles ) ) :
                        $pagename = 'my-balance';
                        $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';

                        printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> Balance/Payment</a></li>', 'workscout' ),
                            $class,
                            home_url('/my-balance')
                        );
                    endif;
                }
                ?>
                <li class="woocommerce-MyAccount-navigation-link">
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( ucwords($label) ); ?></a>
                </li>
                <?php
                if ( $endpoint == 'edit-account') { ?>
                    <li class="woocommerce-MyAccount-navigation-link">
                        <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ).'?password=change'; ?>"><?php echo esc_html( ucwords('Change Password') ); ?></a>
                    </li>
                <?php } ?>
            <?php } ?>
        <?php endforeach;?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
<div class="woocommerce-MyAccount-content">
<h2 class="my-acc-h2"> <strong>My Listings</strong></h2>
<div id="job-manager-bookmarks"><div id="job-manager-job-dashboard">
        <?php
        $submit_job_page = get_option('job_manager_submit_job_form_page_id');
        if (!empty($submit_job_page)) {  ?>
            <a href="<?php echo get_permalink($submit_job_page) ?>" class="button"><?php esc_html_e('Add Job','workscout'); ?></a>
        <?php } ?>
	<p class="margin-bottom-25"><?php esc_html_e( 'Your listings are shown in the table below.', 'workscout' ); ?></p>
	<table class="job-manager-jobs manage-table responsive-table">
		<thead>
			<tr>
				<?php 
				foreach ( $job_dashboard_columns as $key => $column ) : ?>
					<th class="<?php echo esc_attr( $key ); ?>"><?php echo workscout_manage_table_icons($key); echo esc_html( $column ); ?></th>
				<?php endforeach; ?>
					<th></th> 
			</tr>
		</thead>

		<tbody>
			<?php if ( ! $jobs ) : ?>
				<tr>
					<td colspan="8"><?php esc_html_e( 'You do not have any active listings.', 'workscout' ); ?></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $jobs as $job ) : ?>
					<tr>
						<?php foreach ( $job_dashboard_columns as $key => $column ) : ?>
							<td class="<?php echo esc_attr( $key ); ?>">
								<?php if ('job_title' === $key ) : ?>
									<?php if ( $job->post_status == 'publish' ) : ?>
										<a href="<?php echo get_permalink( $job->ID ); ?>"><?php echo esc_html($job->post_title); ?></a>
									<?php else : ?>
										<?php echo esc_html($job->post_title); ?> <small>(<?php the_job_status( $job ); ?>)</small>
									<?php endif; ?>
								<?php elseif ('date' === $key ) : ?>
									<?php echo date_i18n( get_option( 'date_format' ), strtotime( $job->post_date ) ); ?>	
								<?php elseif ('expires' === $key ) : ?>
									<?php echo  $job->_job_expires ? date_i18n( get_option( 'date_format' ), strtotime( $job->_job_expires ) )."dfdfdf" : '&ndash;'; ?>
								<?php elseif ('filled' === $key ) : ?>
									<?php echo is_position_filled( $job ) ? '&#10004;' : '&ndash;'; ?>
								<?php elseif ('applications' === $key ) : ?>
									<?php 
										global $post;
										echo ( $count = get_job_application_count( $job->ID ) ) ? '<a class="button" href="' . add_query_arg( array( 'action' => 'show_applications', 'job_id' => $job->ID ), get_permalink( $post->ID ) ) . '">'.__('Show','workscout').' (' . $count . ')</a>' : '&ndash;';
 									?>
								<?php else : ?>
									<?php do_action( 'job_manager_job_dashboard_column_' . $key, $job ); ?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
							<td class="action">
									<?php
										$actions = array();
										switch ( $job->post_status ) {
											case 'publish' :
												$actions['edit'] = array( 'label' => esc_html__( 'Edit', 'workscout' ), 'nonce' => false );

												if ( is_position_filled( $job ) ) {
													$actions['mark_not_filled'] = array( 'label' => esc_html__( 'Mark not filled', 'workscout' ), 'nonce' => true );
												} else {
													$actions['mark_filled'] = array( 'label' => esc_html__( 'Mark filled', 'workscout' ), 'nonce' => true );
												}
												break;
											case 'expired' :
												/*if ( job_manager_get_permalink( 'submit_job_form' ) ) {
													$actions['relist'] = array( 'label' => esc_html__( 'Relist', 'workscout' ), 'nonce' => true );
												}*/
                                                if ( $count = get_job_application_count( $job->ID ) == 0 )
                                                    $actions['reexpiries'] = array( 'label' => esc_html__( 'Prolong', 'workscout' ), 'nonce' => true );
												break;
											case 'pending_payment' :
											case 'pending' :
												if ( job_manager_user_can_edit_pending_submissions() ) {
													$actions['edit'] = array( 'label' => esc_html__( 'Edit', 'workscout' ), 'nonce' => false );
												}
											break;
										}

										$actions['delete'] = array( 'label' => esc_html__( 'Delete', 'workscout' ), 'nonce' => true );
										$actions           = apply_filters( 'job_manager_my_job_actions', $actions, $job );

										foreach ( $actions as $action => $value ) {
											$action_url = add_query_arg( array( 'action' => $action, 'job_id' => $job->ID ) );
											if ( $value['nonce'] ) {
												$action_url = wp_nonce_url( $action_url, 'job_manager_my_job_actions' );
											}
											echo '<a href="' . esc_url( $action_url ) . '" class="job-dashboard-action-' . esc_attr( $action ) . '">' .workscout_manage_action_icons_custom($action) . esc_html( $value['label'] ) . '</a>';
										}
									?>

							</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<?php get_job_manager_template( 'pagination.php', array( 'max_num_pages' => $max_num_pages ) ); ?>

	<br>
	<?php 
	$submit_job_page = get_option('job_manager_submit_job_form_page_id');
	if (!empty($submit_job_page)) {  ?>
		<a href="<?php echo get_permalink($submit_job_page) ?>" class="button"><?php esc_html_e('Add Job','workscout'); ?></a>
	<?php } ?>
</div>
</div></div></div>
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
            if ($post_data){
                $slug = $post_data['post_name'];
                return $slug;
            }
            return false;
        }
        global $wp;
        $pagename = $wp->query_vars[ 'pagename' ];

        $user = wp_get_current_user();

        if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
            $candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );

            $pagename = the_slug($candidate_dashboard_page_id);
            $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';

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
        /*alerts*/
        $alerts_page_id = get_option( 'job_manager_alerts_page_id' );

        $pagename= the_slug($alerts_page_id);
        $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';
        if ( (in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles )) && !empty($alerts_page_id) ) :
            printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> Job Alerts </a></li>', 'workscout' ),
                $class,
                get_permalink($alerts_page_id)
            );
        endif;

        ?>

        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
            <?php if($label !='Orders' && $label !='Downloads' && $label !='Addresses' && $label != 'Dashboard'){?>
                <?php

                if ( $endpoint =='customer-logout' ){

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
<h2 class="my-acc-h2">My Portfolio(s)</h2><?php
$submission_limit           = get_option( 'resume_manager_submission_limit' );
$submit_resume_form_page_id = get_option( 'resume_manager_submit_resume_form_page_id' );
?>
<div id="resume-manager-candidate-dashboard">
	<p class="margin-bottom-25"><?php echo _n( 'Your Portfolio can be viewed, edited or removed below.', 'Your Portfolios can be viewed, edited or removed below.', resume_manager_count_user_resumes(), 'workscout' ); ?></p>
	<table class="resume-manager-resumes manage-table resumes responsive-table">
		<thead>
			<tr>
				<?php foreach ( $candidate_dashboard_columns as $key => $column ) : ?>
					<th class="<?php echo esc_attr( $key ); ?>"><?php echo workscout_manage_table_icons($key); echo esc_html( $column ); ?></th>
				<?php endforeach; ?>
					<th></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! $resumes ) : ?>
				<tr>
					<td colspan="<?php echo sizeof( $candidate_dashboard_columns ); ?>"><?php esc_html_e( 'You do not have any active resume listings.', 'workscout' ); ?></td>
					<td></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $resumes as $resume ) : ?>
					<tr>
						<?php foreach ( $candidate_dashboard_columns as $key => $column ) : ?>
							<td class="<?php echo esc_attr( $key ); ?>">
								<?php if ( 'resume-title' === $key ) : ?>
									<?php if ( $resume->post_status == 'publish' ) : ?>
										<a href="<?php echo get_permalink( $resume->ID ); ?>"><?php echo esc_html( $resume->post_title ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $resume->post_title ); ?> <small>(<?php the_resume_status( $resume ); ?>)</small>
									<?php endif; ?>
									
								<?php elseif ( 'candidate-title' === $key ) : ?>
									<?php the_candidate_title( '', '', true, $resume ); ?>
								<?php elseif ( 'candidate-location' === $key ) : ?>
									<?php the_candidate_location( false, $resume ); ?></td>
								<?php elseif ( 'resume-category' === $key ) : ?>
									<?php the_resume_category( $resume ); ?>
								<?php elseif ( 'status' === $key ) : ?>
									<?php the_resume_status( $resume ); ?>
								<?php elseif ( 'date' === $key ) : ?>
									<?php
									if ( ! empty( $resume->_resume_expires ) && strtotime( $resume->_resume_expires ) > current_time( 'timestamp' ) ) {
										printf( esc_html__( 'Expires %s', 'workscout' ), date_i18n( get_option( 'date_format' ), strtotime( $resume->_resume_expires ) ) );
									} else {
										echo date_i18n( get_option( 'date_format' ), strtotime( $resume->post_date ) );
									}
									?>
								<?php else : ?>
									<?php do_action( 'resume_manager_candidate_dashboard_column_' . $key, $resume ); ?>
								<?php endif; ?>
							</td>
							
						<?php endforeach; ?>
						<td class="action">
								
										<?php
											$actions = array();

											switch ( $resume->post_status ) {
												case 'publish' :
													$actions['edit'] = array( 'label' => esc_html__( 'Edit', 'workscout' ), 'nonce' => false );
													$actions['hide'] = array( 'label' => esc_html__( 'Hide', 'workscout' ), 'nonce' => true );
												break;
												case 'hidden' :
													$actions['edit'] = array( 'label' => esc_html__( 'Edit', 'workscout' ), 'nonce' => false );
													$actions['publish'] = array( 'label' => esc_html__( 'Publish', 'workscout' ), 'nonce' => true );
												break;
												case 'expired' :
													if ( get_option( 'resume_manager_submit_resume_form_page_id' ) ) {
														$actions['relist'] = array( 'label' => esc_html__( 'Relist', 'workscout' ), 'nonce' => true );
													}
												break;
											}

											$actions['delete'] = array( 'label' => esc_html__( 'Delete', 'workscout' ), 'nonce' => true );

											$actions = apply_filters( 'resume_manager_my_resume_actions', $actions, $resume );

											foreach ( $actions as $action => $value ) {
												$action_url = add_query_arg( array( 'action' => $action, 'resume_id' => $resume->ID ) );
												if ( $value['nonce'] )
													$action_url = wp_nonce_url( $action_url, 'resume_manager_my_resume_actions' );
												echo '<a href="' . $action_url . '" class="candidate-dashboard-action-' . $action . '">'.workscout_manage_action_icons($action) . $value['label'] . '</a>';
											}
										?>
									
							</td>

					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<br>
	<?php if ( $submit_resume_form_page_id && ( resume_manager_count_user_resumes() < $submission_limit || ! $submission_limit ) ) : ?>
		
			<a class="button" href="<?php echo esc_url( get_permalink( $submit_resume_form_page_id ) ); ?>"><?php esc_html_e( 'Add Portfolio', 'workscout' ); ?></a>
				
	<?php endif; ?>
	<?php get_job_manager_template( 'pagination.php', array( 'max_num_pages' => $max_num_pages ) ); ?>
</div>
</div></div></div>
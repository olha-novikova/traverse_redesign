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
        /*bookmarks*/
        $bookmarks_page_id = ot_get_option('pp_bookmarks_page');
        $pagename = the_slug($bookmarks_page_id);
        $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';
        if ( (in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles )) && !empty($bookmarks_page_id) ) :
            printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> My Bookmarks </a></li>', 'workscout' ),
                $class,
                get_permalink($bookmarks_page_id)
            );
        endif;

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
            <?php } ?>
        <?php endforeach;?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
<div class="woocommerce-MyAccount-content">
<h2 class="my-acc-h2"> <strong>My Bookmarks</strong></h2>
<div id="job-manager-bookmarks">
	<table class="manage-table job-manager-bookmarks">
		<thead>
			<tr>
				<th><i class="fa fa-heart"></i> <?php esc_html_e( 'Bookmark', 'workscout' ); ?></th>
				<th><i class="fa fa-file-text"></i> <?php esc_html_e( 'Notes', 'workscout' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach ( $bookmarks as $bookmark ) : 
				if ( get_post_status( $bookmark->post_id ) !== 'publish' ) {
					continue;
				}
				$has_bookmark = true;
				?>
				<tr>
					<td width="50%">
						<?php echo '<a href="' . get_permalink( $bookmark->post_id ) . '">' . get_the_title( $bookmark->post_id ) . '</a>'; ?>
						
					</td>
					<td width="50%">
						<?php echo wpautop( wp_kses_post( $bookmark->bookmark_note ) ); ?>
					</td>
					<td class="action">
						
							<?php
								$actions = apply_filters( 'job_manager_bookmark_actions', array(
									'delete' => array(
										'label' => esc_html__( 'Delete', 'workscout' ),
										'url'   =>  wp_nonce_url( add_query_arg( 'remove_bookmark', $bookmark->post_id ), 'remove_bookmark' )
									)
								), $bookmark );

								foreach ( $actions as $action => $value ) {
									echo '<a href="' . esc_url( $value['url'] ) . '" class="delete job-manager-bookmark-action-' . $action . '"><i class="fa fa-remove"></i> ' . $value['label'] . '</a>';
								}
							?>
						
					</td>
				</tr>
			<?php endforeach; ?> 

			<?php if ( empty( $has_bookmark ) ) : ?>
				<tr>
					<td colspan="3"><?php esc_html_e( 'You currently have no bookmarks', 'workscout' ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
</div></div></div>
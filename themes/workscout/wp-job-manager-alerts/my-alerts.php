<div id="job-manager-alerts">
	<p class="margin-bottom-25"><?php printf( esc_html__( 'Your job alerts are shown in the table below. Your alerts will be sent to %s.', 'workscout' ), $user->user_email ); ?></p>
	<table class="manage-table resumes responsive-table job-manager-alerts">
		<thead>
			<tr>
				<th><i class="fa fa-file-text"></i> <?php esc_html_e( 'Alert Name', 'workscout' ); ?></th>
				<th><i class="fa fa-calendar"></i> <?php esc_html_e( 'Date Created', 'workscout' ); ?></th>
				<th><i class="fa fa-tags"></i> <?php esc_html_e( 'Keywords', 'workscout' ); ?></th>
				<th><i class="fa fa-map-marker"></i> <?php esc_html_e( 'Location', 'workscout' ); ?></th>
				<th><i class="fa fa-clock-o"></i> <?php esc_html_e( 'Frequency', 'workscout' ); ?></th>
				<th><i class="fa fa-check-square-o"></i> <?php esc_html_e( 'Status', 'workscout' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! $alerts ) : ?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'You do not have any job alerts.', 'workscout' ); ?></td>
					
				</tr>
			<?php endif;  ?>
			<?php foreach ( $alerts as $alert ) : ?>
				<tr>
					<td>
						<?php echo esc_html( $alert->post_title ); ?>
					</td>
					<td class="date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $alert->post_date ) ); ?></td>
					<td class="alert_keyword"><?php
						if ( $value = get_post_meta( $alert->ID, 'alert_keyword', true ) )
							echo esc_html( '&ldquo;' . $value . '&rdquo;' );
						else
							echo '&ndash;';
					?></td>
					<td class="alert_location"><?php
						if ( taxonomy_exists( 'job_listing_region' ) && wp_count_terms( 'job_listing_region' ) > 0 ) {
							$terms = wp_get_post_terms( $alert->ID, 'job_listing_region', array( 'fields' => 'names' ) );
							echo esc_html( implode( ', ', $terms ) );
						} else {
							$value = get_post_meta( $alert->ID, 'alert_location', true );
							echo $value ? esc_html( '&ldquo;' . $value . '&rdquo;' ) : '&ndash;';
						}
					?></td>
					<td class="alert_frequency"><?php
						switch ( $freq = get_post_meta( $alert->ID, 'alert_frequency', true ) ) {
							case "daily" :
								esc_html_e( 'Daily', 'workscout' );
							break;
							case "weekly" :
								esc_html_e( 'Weekly', 'workscout' );
							break;
							case "fortnightly" :
								esc_html_e( 'Fortnightly', 'workscout' );
							break;
						}
					?></td>
					<td class="status"><?php echo $alert->post_status == 'draft' ? esc_html__( 'Disabled', 'workscout' ) : esc_html__( 'Enabled', 'workscout' ); ?></td>
					<td class="action">
						
							<?php
								$actions = apply_filters( 'job_manager_alert_actions', array(
									'view' => array(
										'label' => esc_html__( 'Show Results', 'workscout' ),
										'nonce' => false
									),
									'email' => array(
										'label' => esc_html__( 'Email', 'workscout' ),
										'nonce' => true
									),
									'edit' => array(
										'label' => esc_html__( 'Edit', 'workscout' ),
										'nonce' => false
									),
									'toggle_status' => array(
										'label' => $alert->post_status == 'draft' ? esc_html__( 'Enable', 'workscout' ) : esc_html__( 'Disable', 'workscout' ),
										'nonce' => true
									),
									'delete' => array(
										'label' => esc_html__( 'Delete', 'workscout' ),
										'nonce' => true
									)
								), $alert );

								foreach ( $actions as $action => $value ) {
									$action_url = remove_query_arg( 'updated', add_query_arg( array( 'action' => $action, 'alert_id' => $alert->ID ) ) );

									if ( $value['nonce'] )
										$action_url = wp_nonce_url( $action_url, 'job_manager_alert_actions' );

									echo '<a href="' . $action_url . '" class="job-alerts-action-' . $action . '">' .workscout_manage_action_icons($action)  . $value['label'] . '</a>';
								}
							?>
						
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<br>
	<a class="button" href="<?php echo remove_query_arg( 'updated', add_query_arg( 'action', 'add_alert' ) ); ?>"><?php esc_html_e( 'Add alert', 'workscout' ); ?></a>
			
</div>



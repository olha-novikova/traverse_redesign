<div class="section__container section__container_pitches">
	<div class="table table_pitches">
		<div class="table__head">
			<div class="table__row table__row_header">
				<div class="table__header">
					<p class="section__header">Pitched Campaigns</p>
				</div>
			</div>
		</div>
		<div class="table__body">
			<?php
			$pitches = get_pitched_campaigns($user->ID, 'new');

			if ($pitches) : ?>
				<?php foreach ($pitches as $pitch) : ?>
					<?php $pitches_data = get_post_meta($pitch[0]->ID, '', true);
					$date = $pitch['date'];
          var_dump($pitch);
					?>
      <div class="table__row table__row_body">
						<div class="table__data"><i class="icon icon_calendar"></i>
							<p class="table__data__date"><?php echo date("d", strtotime($date)) ?></p>
							<p class="table__data__month"><?php echo date("F", strtotime($date)) ?></p>
						</div>
						<div class="table__data">
							<p class="table__text">
								<?php
								echo esc_html( $pitches_data['_job_title'][0]); echo ($pitches_data['_company_name'][0] != '' ? 'for <span>' . esc_html($pitches_data['_company_name'][0]) : '');
								?>
								</span>
							</p>
						</div>
						<div class="table__data">
							<p class="table__text"><?php echo ($pitches_data['_job_location'][0] ? esc_html($pitches_data['_job_location'][0]) : 'Anywhere') ?></p>
						</div>
						<div class="table__data">
							<p class="table__text"><?php echo esc_html($pitch['message'])?></p>
						</div>
						<div class="table__data">
							<div class="table__buttons">
								<div class="table__buttons">
									<a href="<?= get_post_permalink($pitch[0]->ID) ?>" class="button button_green">View Campaign Details</a>
									<a href="<?= get_post_permalink($pitch['id']) ?>" class="button button_green">View Full Pitch</a>
								</div>
							</div>
						</div>
					</div>
      <div class="section__pitches">
        <?php

          $total_count = count ( $pitch );

          $last_application = get_last_application( $pitch[0]->ID );
        ?>
          <div class="section__pitches_line">
            <div class="table__influencers">
          <?php
          for ( $i=1; $i<=$total_count; $i++){
            echo '<div class="table__influencer"></div>';
          }
          ?>

            </div>
            <div class="section__persons_items">
              <a class="person__single" href="#"><?php echo $last_application->post_title;?></a>
              <span><?php echo ( ($total_count -1) > 0 ? ('and '.($total_count -1).' more pitched this'):'' ); ?> </span>
            </div>
          </div>
          <div class="section__persons" id="job-<?php echo esc_attr( $job->ID ); ?>">
        <?php global $wp_post_statuses; ?>

            <div class="section__list_person">
                  <div class="section__list_header">
                    <div class="section_left">
                      <div class="person_info">
                        <div class="person_image">
                          <div class="person_photo"></div>
                        </div>
                        <div class="person_and_time">
                <?php if ( ( $resume_id = get_job_application_resume_id( $application->ID ) ) && 'publish' === get_post_status( $resume_id ) && function_exists( 'get_resume_share_link' ) && (
                  $share_link = get_resume_share_link( $resume_id ) ) ) {?>
                              <a class="person_name" href="<?php echo $share_link;?>"><?php echo  $application->post_title; ?></a>
                <?php }else{ ?>
                              <a class="person_name" href="#"><?php echo  $application->post_title; ?></a>
                <?php }; ?>

                          <span class="person_time_ago">
                                                              <?php printf( esc_html__( '%s ago', 'workscout' ), human_time_diff( get_post_time( 'U', true, $application->ID ), current_time( 'timestamp' ) ) ); ?>
                                                          </span>

                          <span class="person_status status_<?php echo $application->post_status;?>">
                                                              <?php echo $wp_post_statuses[ $application->post_status ]->label; ?>
                                                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="section_right">
                      ...
                    </div>
                  </div>
                  <div class="section__list_content">
                    <p><?php esc_html_e($pitch['message']); ?></p>
                  </div>
                  <div class="section__list_footer">
            <?php $user_id = get_post_meta( $application->ID , "_candidate_user_id", true); ?>
            <?php if($application->post_status=="new"){ ?>
                        <a href="#hire-dialod-<?php echo $job->ID;?>" class="button button_green open-popup-hire">Hire</a>
            <?php } ?>

                    <div class="openchat button button_orange" data-reciever-id="<?php echo $user_id;?>" data-job-id="<?php echo esc_attr( $job->ID )?>" data-job-name="<?php echo esc_html($job->post_title);?>">Message</div>

            <?php if( $application->post_status=="new"){ ?>
                        <div id = "hire-dialod-<?php echo $job->ID;?>" class="small-dialog zoom-anim-dialog mfp-hide apply-popup ">
                          <div class="small-dialog-headline">
                            <h2>Pitch Status Change</h2>
                          </div>
                          <div class="small-dialog-content">
                            <p>You are about to hire <strong><?php echo  $application->post_title; ?></strong> for  <strong><?php echo  $job->post_title; ?></strong></p>
                            <p>Would you like to proceed?</p>

                            <form class="inline job-manager-application-edit-form job-manager-form" method="post">
                              <input type="hidden" name="application_rating"/>
                              <input type="hidden" name="application_status" value="hired" />
                              <input type="hidden" name="application_id" value="<?php echo absint( $application->ID ); ?>" />
                  <?php wp_nonce_field( 'edit_job_application' ); ?>
                              <input class="button button_blue" type="submit" name="wp_job_manager_edit_application" value="<?php esc_html_e( 'Yes, accept', 'workscout' ); ?>" />
                            </form>
                            <div class="button button_orange mfp-close">Cancel</div>

                          </div>
                        </div>
            <?php }elseif( $application->post_status=="in_review" ){
              ?>
                        <a href="#review-<?php echo esc_attr($application->ID );?>" title="<?php esc_html_e( 'Review and Approve', 'workscout' ); ?>" class="button gray app-link job-application-toggle-content"><i class="fa fa-plus-circle"></i> <?php esc_html_e( 'Review and Approve', 'workscout' ); ?></a>
            <?php }elseif($application->post_status=="in_progress" ){
              esc_html_e( 'In Progress', 'workscout' );
            }
            ?>
                  </div>
                </div>

          </div>
        </div>
      <?php endforeach; else: ?>
				<div class="table__body">
					<div class="table__row table__row_body table__row_empty">
						<div class="empty"><i class="icon icon_calendar"></i>
							<p class="empty-text">There are no completed campaigns to show</p>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="table__head">
			<div class="table__row table__row_header">
				<div class="table__header">
					<p class="section__header">Completed Campaigns</p>
				</div>
			</div>
		</div>
		<div class="table__body">
			<?php
			$pitches = get_pitched_campaigns($user->ID, 'completed');

			if ($pitches) : ?>
				<?php foreach ($pitches as $pitch) : ?>
					<?php $pitches_data = get_post_meta($pitch[0]->ID, '', true);
					$date = $pitch['date'];

					?>
					<div class="table__row table__row_body">
						<div class="table__data"><i class="icon icon_calendar"></i>
							<p class="table__data__date"><?php echo date("d", strtotime($date)) ?></p>
							<p class="table__data__month"><?php echo date("F", strtotime($date)) ?></p>
						</div>
						<div class="table__data">
							<p class="table__text">
								<?php
								echo esc_html( $pitches_data['_job_title'][0]); echo ($pitches_data['_company_name'][0] != '' ? 'for <span>' . esc_html($pitches_data['_company_name'][0]) : '');
								?>
							</p>
						</div>
						<div class="table__data">
							<p class="table__text"><?php echo ($pitches_data['_job_location'][0] ? esc_html($pitches_data['_job_location'][0]) : 'Anywhere') ?></p>
						</div>
						<div class="table__data">
							<p class="table__text"><?php echo esc_html($pitch['message'])?></p>
						</div>
						<div class="table__data">
							<div class="table__buttons">
								<a href="<?= get_post_permalink($pitch[0]->ID) ?>" class="button button_green">View Campaign Details</a>
								<a href="<?= get_post_permalink($pitch['id']) ?>" class="button button_green">View Full Pitch</a>
							</div>
						</div>
					</div>
				<?php endforeach; else: ?>
				<div class="table__body">
					<div class="table__row table__row_body table__row_empty">
						<div class="empty"><i class="icon icon_calendar"></i>
							<p class="empty-text">There are no completed campaigns to show</p>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="table__head">
			<div class="table__row table__row_header">
				<div class="table__header">
					<p class="section__header">Active Campaigns</p>
				</div>
			</div>
		</div>
		<div class="table__body">
			<?php
			$pitches = get_pitched_campaigns($user->ID, 'hired');

			if ($pitches) : ?>
				<?php foreach ($pitches as $pitch) : ?>
					<?php $pitches_data = get_post_meta($pitch[0]->ID, '', true);
					$date = $pitch['date'];
					?>
					<div class="table__row table__row_body">
						<div class="table__data"><i class="icon icon_calendar"></i>
							<p class="table__data__date"><?php echo date("d", strtotime($date)) ?></p>
							<p class="table__data__month"><?php echo date("F", strtotime($date)) ?></p>
						</div>
						<div class="table__data">
							<p class="table__text">
								<?php
								echo esc_html( $pitches_data['_job_title'][0]); echo ($pitches_data['_company_name'][0] != '' ? ' for <span>' . esc_html($pitches_data['_company_name'][0]) : '');
								?>
								</span>
							</p>
						</div>
						<div class="table__data">
							<p class="table__text"><?php echo ($pitches_data['_job_location'][0] ? esc_html($pitches_data['_job_location'][0]) : 'Anywhere') ?></p>
						</div>
						<div class="table__data">
							<p class="table__text"><?php echo esc_html($pitch['message'])?></p>
						</div>
						<div class="table__data">
							<div class="table__buttons">
								<a href="<?= get_post_permalink($pitch[0]->ID) ?>" class="button button_green">View Campaign Details</a>
								<a href="<?= get_post_permalink($pitch['id']) ?>" class="button button_green">View Full Pitch</a>
							</div>
						</div>
					</div>
				<?php endforeach; else: ?>
				<div class="table__body">
					<div class="table__row table__row_body table__row_empty">
						<div class="empty"><i class="icon icon_calendar"></i>
							<p class="empty-text">There are no active campaigns to show</p>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
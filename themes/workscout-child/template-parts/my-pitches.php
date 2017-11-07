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
                                    <?php if ( $pitch[0]->post_status == 'publish') {?>
                                        <a href="<?echo get_post_permalink($pitch[0]->ID) ?>" class="button button_green">View Campaign Details</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                <div class="table__body">
                    <div class="table__row table__row_body table__row_empty">
                        <div class="empty"><i class="icon icon_calendar"></i>
                            <p class="empty-text">There are no new pitches to show</p>
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
                                <?php if ( $pitch[0]->post_status == 'publish') {?>
                                    <a href="<?echo get_post_permalink($pitch[0]->ID) ?>" class="button button_green">View Campaign Details</a>
                                <?php } ?>
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
			$pitches = get_pitched_campaigns($user->ID, 'hired,in_review');
            global $wp_post_statuses;
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
                                <?php if ( $pitch[0]->post_status == 'publish') {?>
                                    <a href="<?echo get_post_permalink($pitch[0]->ID) ?>" class="button button_green">View Campaign Details</a>
                                <?php } ?>
                                <?php if( $pitch['status']=="hired" ){ ?>
                                    <a href="#hire-dialod-<?php echo $pitch['id']?>" class="button button_green open-popup-hire">Submit for review</a>
                                <?php }?>
							</div>
						</div>
                        <?php if( $pitch['status']=="in_review"){ ?>
                            <div class="table__data">
                                <p class="table__text"><span class="person_status status_new">Pitch on Review</span><br>
                                Review Message: <br>
                                <?php echo get_post_meta($pitch['id'], '_review_msg', true); ?></p>
                            </div>
                        <?php } ?>

                        <?php if($pitch['status']=="hired"){?>
                            <div id = "hire-dialod-<?php echo $pitch['id'];?>" class="small-dialog zoom-anim-dialog mfp-hide apply-popup ">
                                <div class="small-dialog-headline">
                                    <h2>Send on Review</h2>
                                </div>
                                <div class="small-dialog-content">
                                    <p>Would you like to send your pitch on Review?</p>
                                    <form class="inline job-manager-application-review-form job-manager-form" method="post">
                                        <p><?php _e( 'Review Message', 'wp-job-manager-applications' ); ?></p>
                                        <textarea class="application-review-msg" name="application-review-msg"></textarea>
                                        <input type="hidden" name="application_rating"/>
                                        <input type="hidden" name="wp_job_manager_review_application"  value="1" />
                                        <input type="hidden" name="application_status" value="in_review" />
                                        <input type="hidden" name="application_id" value="<?php echo absint(  $pitch['id'] ); ?>" />
                                        <?php wp_nonce_field( 'edit_job_application' ); ?>
                                        <input class="button wp_job_manager_review_application" type="button" value="<?php esc_html_e( 'On Review', 'workscout' ); ?>" />
                                    </form>
                                </div>
                            </div>
                        <?php }?>
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
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
									<a href="<?= get_post_permalink($pitch[0]->ID) ?>" class="button button_green">View Campaign Details</a>
									<a href="<?= get_post_permalink($pitch['id']) ?>" class="button button_green">View Full Pitch</a>
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
<p>
	<?php printf( __( 'Dear %s', 'wp-job-manager-apply-with-facebook' ), $company_name ); ?>,<br/><br/>
	<?php printf( __( '%s has applied for the job of %s. See below for %s\'s profile snapshot.', 'wp-job-manager-apply-with-facebook' ), $profile_data->name, $job_title, $profile_data->first_name ); ?>
</p>

<table style="border:1px solid #ccc; background: #eee; padding: 14px; text-align:left;" cellpadding="6" cellspacing="0">
	<tr>
		<td rowspan="3"><img src="<?php echo esc_attr( $profile_picture ); ?>" width="100" /></td>
		<td><strong style="font-size: 16px;"><?php echo esc_html( $profile_data->name ); ?></strong></td>
	</tr>
	<tr>
		<td><strong><?php echo esc_html( $profile_data->bio ); ?></strong></td>
	</tr>
	<tr>
		<td><em><?php echo esc_html( $profile_data->location->name ); ?></em></td>
	</tr>
	<tr>
		<th valign="top"><?php _e( 'Current', 'wp-job-manager-apply-with-facebook' ); ?></th>
		<td valign="top"><?php 
			if ( isset( $profile_data->work[0] ) ) {
				foreach ( $profile_data->work as $position ) {
					if ( ! isset( $position->end_date ) ) {
						echo $position->position->name;
						if ( ! empty( $position->employer ) ) {
							echo ' - ' . $position->employer->name;
						}
						echo '<br />';
					}
				}
			}
		?></td>
	</tr>
	<tr>
		<th valign="top"><?php _e( 'Past', 'wp-job-manager-apply-with-facebook' ); ?></th>
		<td valign="top"><?php
			if ( isset( $profile_data->work[0] ) ) {
				foreach ( $profile_data->work as $position ) {
					if ( isset( $position->end_date ) ) {
						echo $position->position->name;
						if ( ! empty( $position->employer ) ) {
							echo ' - ' . $position->employer->name;
						}
						echo '<br />';
					}
				}
			}
		?></td>
	</tr>
	<tr>
		<th valign="top"><?php _e( 'Education', 'wp-job-manager-apply-with-facebook' ); ?></th>
		<td valign="top"><?php
			if ( isset( $profile_data->education[0] ) ) {
				foreach ( $profile_data->education as $education ) {
					echo $education->type . ' - ' . $education->school->name;
					if ( ! empty( $education->year ) ) {
						echo ' (' . $education->year->name . ')';
					}
					echo '<br />';
				}
			}
		?></td>
	</tr>
	<tr>
		<th valign="top"><?php _e( 'Email address', 'wp-job-manager-apply-with-facebook' ); ?></th>
		<td valign="top"><?php echo make_clickable( sanitize_text_field( $profile_data->email ) ); ?></td>
	</tr>
	<?php if ( $cover_letter ) : ?>
		<tr>
			<th valign="top"><?php _e( 'Cover letter', 'wp-job-manager-apply-with-facebook' ); ?></th>
			<td valign="top"><?php echo wpautop( wptexturize( $cover_letter ) ); ?></td>
		</tr>
	<?php endif; ?>
</table>

<p><a href="<?php echo esc_url( $profile_data->link ); ?>"><?php _e( 'View complete profile', 'wp-job-manager-apply-with-facebook' ); ?></a></p>
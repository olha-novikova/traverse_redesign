<tr id="<?php echo esc_attr( sanitize_title( $this->plugin_slug . '_licence_key_row' ) ); ?>" class="active plugin-update-tr smylesv2-updater-licence-key-tr">
	<td class="plugin-update" colspan="3">
		<?php $this->error_notices(); ?>
		<div class="smylesv2-updater-licence-key">
			<label for="<?php echo sanitize_title( $this->plugin_slug ); ?>_licence_key"><?php _e( 'License', 'wp-job-manager-field-editor' ); ?>:</label>
			<input type="text" id="<?php echo sanitize_title( $this->plugin_slug ); ?>_licence_key" name="<?php echo esc_attr( $this->plugin_slug ); ?>_licence_key" placeholder="XXXX-XXXX-XXXX-XXXX" />
			<input type="email" id="<?php echo sanitize_title( $this->plugin_slug ); ?>_email" name="<?php echo esc_attr( $this->plugin_slug ); ?>_email" placeholder="API/License Email Address" value="" />
			<span class="description"><?php _e( 'Enter your license key and email and hit return. A valid key is required for updates.', 'wp-job-manager-field-editor' ); ?> <?php printf( 'Lost your key? <a href="%s">Retrieve it here</a>.', esc_url( 'https://plugins.smyl.es/lost-api-key/' ) ); ?></span>
		</div>
	</td>
	<script>
		jQuery(function(){
			jQuery('tr#<?php echo esc_attr( $this->plugin_slug ); ?>_licence_key_row').prev().addClass('smylesv2-updater-licenced');
		});
	</script>
</tr>

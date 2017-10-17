<?php

function base_stripe_settings_setup() {
	add_options_page('Stripe Settings', 'Stripe Settings', 'manage_options', 'stripe-settings', 'base_stripe_render_options_page');
}
add_action('admin_menu', 'base_stripe_settings_setup');

function base_stripe_render_options_page() {
	global $stripe_options;
	?>
	<div class="wrap">
		<h2><?php _e('Stripe Settings', 'stripe_domain'); ?></h2>
		<form method="post" action="options.php">
		
			<?php settings_fields('stripe_settings_group'); ?>
			
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Test Mode', 'stripe_domain'); ?>
						</th>
						<td>
							<input id="stripe_settings[test_mode]" name="stripe_settings[test_mode]" type="checkbox" value="1" <?php checked(1, $stripe_options['test_mode']); ?> />
							<label class="description" for="stripe_settings[test_mode]"><?php _e('Check this to use the plugin in test mode.', 'stripe_domain'); ?></label>
						</td>
					</tr>
				</tbody>
			</table>	
			
			<h3 class="title"><?php _e('API Keys', 'stripe_domain'); ?></h3>
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Live Secret', 'stripe_domain'); ?>
						</th>
						<td>
							<input id="stripe_settings[live_secret_key]" name="stripe_settings[live_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_secret_key']; ?>"/>
							<label class="description" for="stripe_settings[live_secret_key]"><?php _e('Paste your live secret key.', 'stripe_domain'); ?></label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Live Publishable', 'stripe_domain'); ?>
						</th>
						<td>
							<input id="stripe_settings[live_publishable_key]" name="stripe_settings[live_publishable_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_publishable_key']; ?>"/>
							<label class="description" for="stripe_settings[live_publishable_key]"><?php _e('Paste your live publishable key.', 'stripe_domain'); ?></label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Test Secret', 'stripe_domain'); ?>
						</th>
						<td>
							<input id="stripe_settings[test_secret_key]" name="stripe_settings[test_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['test_secret_key']; ?>"/>
							<label class="description" for="stripe_settings[test_secret_key]"><?php _e('Paste your test secret key.', 'stripe_domain'); ?></label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Test Publishable', 'stripe_domain'); ?>
						</th>
						<td>
							<input id="stripe_settings[test_publishable_key]" name="stripe_settings[test_publishable_key]" class="regular-text" type="text" value="<?php echo $stripe_options['test_publishable_key']; ?>"/>
							<label class="description" for="stripe_settings[test_publishable_key]"><?php _e('Paste your test publishable key.', 'stripe_domain'); ?></label>
						</td>
					</tr>
				</tbody>
			</table>	
				
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Options', 'mfwp_domain'); ?>" />
			</p>
		
		</form>
	<?php
}

function base_stripe_register_settings() {
	// creates our settings in the options table
	register_setting('stripe_settings_group', 'stripe_settings');
}
add_action('admin_init', 'base_stripe_register_settings');
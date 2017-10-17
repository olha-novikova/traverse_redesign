<?php
wp_enqueue_script( 'wp-job-manager-multiselect' );
$key_class = "multiselect-" . esc_attr( $key );
$classes = array( 'jmfe-multiselect-field' );
$classes[] = $key_class;
$placeholder = array_key_exists( 'placeholder', $field ) && ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : __( 'Select Some Options', 'wp-job-manager-field-editor' );

// Check for custom configurations (that require separate jQuery initalization)
if ( array_key_exists( 'max_selected', $field ) && ! empty( $field[ 'max_selected' ] ) ) {

	$max_selected    = $field[ 'max_selected' ];
	$esc_key         = esc_attr( $key );

	// Generate custom jQuery code to initialize chosen element
	$multi_script    = "jQuery(function($){ jQuery('#{$esc_key}').chosen({ max_selected_options: {$max_selected}, search_contains: true }); });";
	wp_add_inline_script( 'wp-job-manager-multiselect', $multi_script );

	if ( empty( $field[ 'description' ] ) ) {
		$field[ 'description' ] = sprintf( __( 'Maximum selections: %s', 'wp-job-manager-field-editor' ), $max_selected );
	}

} else {
	// Add default chosen init class if this isn't a custom init field
	$classes[] = 'job-manager-multiselect';
}

?>
<select multiple="multiple" name="<?php echo esc_attr( isset($field['name']) ? $field['name'] : $key ); ?>[]" id="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php if( ! empty($field['required']) ) echo 'required'; ?> data-no_results_text="<?php _e( 'No results match', 'wp-job-manager-field-editor' ); ?>" data-placeholder="<?php echo $placeholder; ?>">
	<?php
	$no_values = isset( $field['value'] ) ? false : true;
	foreach ( $field['options'] as $key => $value ) :
		$key = str_replace( '*', '', $key, $replace_default );
		$key = str_replace( '~', '', $key, $replace_disabled );
		$field_value = isset( $field['value'] ) ? $field['value'] : array();

		if( $no_values && $replace_default > 0) $field[ 'value' ][ ] = $key;

		$disabled_option = $replace_disabled > 0 ? 'disabled="disabled"' : '';
	?>
		<option value="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['value'] ) && is_array( $field['value'] ) ) selected( in_array( $key, $field['value'] ), true ); ?> <?php echo $disabled_option; ?>><?php echo esc_html( $value ); ?></option>
	<?php endforeach; ?>
</select>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description <?php echo $key_class; ?>-description"><?php echo $field['description']; ?></small><?php endif; ?>

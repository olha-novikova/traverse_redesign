<?php
wp_enqueue_script( 'jmfe-fptime-field' );
wp_enqueue_style( 'jmfe-flatpickr-style' );

// Flatpickr Custom Theme
if ( wp_style_is( 'jmfe-flatpickr-theme', 'registered' ) && ! wp_style_is( 'jmfe-flatpickr-theme', 'enqueued' ) ) {
	wp_enqueue_style( 'jmfe-flatpickr-theme' );
}

// Build data attributes from field configuration, this allows for customization of the field type
// as values as pulled in JS from data attributes and override filter or default configs.
$data_atts = '';
$data_vals = apply_filters( 'job_manager_field_editor_fptime_field_data_fields', array( 'picker_increment' ) );
foreach ( $data_vals as $data_val ) {

	if ( ! array_key_exists( $data_val, $field ) ) {
		continue;
	}
	$data_key  = str_replace( 'picker_', '', $data_val );
	$data_atts .= " data-{$data_key}=\"{$field[ $data_val ]}\"";
}

$key_class = 'fptime-' . esc_attr( $key );
$classes   = array( 'jmfe-fptime-field', 'jmfe-input-fptime', 'input-fptime', 'jmfe-fptime-picker' );
$classes[] = $key_class;
$maybe_required = ! empty( $field['required'] ) && get_option( 'jmfe_fields_html5_required', TRUE ) ? 'required' : '';

if ( array_key_exists( 'value', $field ) ){
	$value = esc_attr( $field['value'] );
} else {
	$value = array_key_exists( 'default', $field ) ? esc_attr( $field[ 'default' ] ) : '';
}
?>
<input type="text" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" title="<?php echo isset($field['title']) ? esc_attr( $field['title'] ) : ''; ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo $value; ?>" <?php echo $data_atts; ?> />
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description <?php echo $key_class; ?>-description"><?php echo $field['description']; ?></small><?php endif; ?>

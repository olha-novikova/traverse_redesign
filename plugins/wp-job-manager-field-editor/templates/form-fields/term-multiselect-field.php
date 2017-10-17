<?php
// Get selected value
if ( isset( $field['value'] ) ) {
	$selected = $field['value'];
} elseif (  ! empty( $field['default'] ) && is_int( $field['default'] ) ) {
	$selected = $field['default'];
} elseif ( ! empty( $field['default'] ) && ( $term = get_term_by( 'slug', $field['default'], $field['taxonomy'] ) ) ) {
	$selected = $term->term_id;
} else {
	$selected = '';
}

wp_enqueue_script( 'wp-job-manager-term-multiselect' );

$args = array(
	'taxonomy'     => $field['taxonomy'],
	'hierarchical' => 1,
	'name'         => isset( $field['name'] ) ? $field['name'] : $key,
	'orderby'      => 'name',
	'selected'     => $selected,
	'hide_empty'   => false
);

// Check for custom configurations (that require separate jQuery initalization)
if ( array_key_exists( 'max_selected', $field ) && ! empty( $field[ 'max_selected' ] ) ) {
	// Set class to value that would be set by job_manager_dropdown_categories()
	// This prevents job-manager-category-dropdown class from being added
	$args[ 'class' ] = is_rtl() ? 'chosen-rtl' : '';
	$max_selected    = $field[ 'max_selected' ];
	$esc_key         = esc_attr( $key );

	// Generate custom jQuery code to initialize chosen element
	$multi_script    = "jQuery(function($){ jQuery('#{$esc_key}').chosen({ max_selected_options: {$max_selected}, search_contains: true }); });";
	wp_add_inline_script( 'wp-job-manager-term-multiselect', $multi_script );

	if( empty( $field[ 'description' ] ) ){
		$field['description'] = sprintf( __( 'Maximum selections: %s', 'wp-job-manager-field-editor' ), $max_selected );
	}
}

if ( isset( $field['placeholder'] ) && ! empty( $field['placeholder'] ) ) $args['placeholder'] = $field['placeholder'];

job_manager_dropdown_categories( apply_filters( 'job_manager_term_multiselect_field_args', $args ) );

if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo $field['description']; ?></small><?php endif; ?>

<?php
$key_class = "select-" . esc_attr( $key );
$classes   = array( 'jmfe-select-field', 'jmfe-input-select', 'input-select' );
$classes[] = $key_class;
// Chosen.JS has issues with HTML5 required attributes, so only use if filter used to return true value
// @see https://github.com/harvesthq/chosen/issues/515
$maybe_required = ! empty( $field['required'] ) && apply_filters( 'job_manager_field_editor_select_use_html5_required', false ) ? 'required' : '';
?>

<select class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php echo $maybe_required; ?>>
	<?php
	foreach ( $field['options'] as $key => $value ) :
		$key = str_replace( '*', '', $key, $replace_default );
		$key = str_replace( '~', '', $key, $replace_disabled );

		if( $replace_default > 0 ) $field[ 'default' ] = $key;
		$disabled_option = $replace_disabled > 0 ? 'disabled="disabled"' : '';
	?>

		<option value="<?php echo esc_attr( $key ); ?>" <?php if ( isset( $field['value'] ) || isset( $field['default'] ) ) selected( isset( $field['value'] ) ? $field['value'] : $field['default'], $key ); ?> <?php echo $disabled_option; ?>><?php echo esc_html( $value ); ?></option>
	<?php endforeach; ?>
</select>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description <?php echo $key_class; ?>-description"><?php echo $field['description']; ?></small><?php endif; ?>

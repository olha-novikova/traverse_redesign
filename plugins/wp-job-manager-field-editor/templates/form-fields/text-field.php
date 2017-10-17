<?php
$key_class = "text-" . esc_attr( $key );
$classes   = array( 'jmfe-text-field', 'jmfe-input-text', 'input-text' );
$classes[] = $key_class;
$maybe_required = ! empty( $field['required'] ) && get_option( 'jmfe_fields_html5_required', TRUE ) ? 'required' : '';

if ( array_key_exists( 'value', $field ) ){
	$value = esc_attr( $field['value'] );
} else {
	$value = array_key_exists( 'default', $field ) ? esc_attr( $field[ 'default' ] ) : '';
}
?>
<input type="text" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" title="<?php echo isset($field['title']) ? esc_attr( $field['title'] ) : ''; ?>" <?php echo ! empty($field['pattern']) ? "pattern=\"" . esc_attr($field['pattern']) . "\"" : ''; ?> placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo $value; ?>" <?php echo ! empty( $field['maxlength'] ) ? "maxlength=\"" . esc_attr( $field['maxlength'] ) . "\"" : ''; ?> <?php echo $maybe_required; ?> />
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description <?php echo $key_class; ?>-description"><?php echo $field['description']; ?></small><?php endif; ?>
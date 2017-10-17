<?php
$key_class = 'number-' . esc_attr( $key );
$classes = array( 'jmfe-number-field', 'input-number' );
$classes[] = $key_class;
$maybe_required = ! empty( $field['required'] ) && get_option( 'jmfe_fields_html5_required', TRUE ) ? 'required' : '';

if ( array_key_exists( 'value', $field ) ) {
	$value = esc_attr( $field[ 'value' ] );
} else {
	$value = array_key_exists( 'default', $field ) ? esc_attr( $field[ 'default' ] ) : '';
}
?>
<input type="number" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo $value; ?>" maxlength="<?php echo ! empty( $field['maxlength'] ) ? $field['maxlength'] : ''; ?>" min="<?php echo ! empty($field['min']) ? $field['min'] : ''; ?>" max="<?php echo ! empty($field['max']) ? $field['max'] : ''; ?>" step="<?php echo ! empty($field['step']) ? $field['step'] : ''; ?>" <?php echo ! empty($field['pattern']) ? "pattern=\"" . esc_attr( $field['pattern'] ) . "\"" : ''; ?> <?php echo $maybe_required; ?> />
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description <?php echo $key_class; ?>-description"><?php echo $field['description']; ?></small><?php endif; ?>
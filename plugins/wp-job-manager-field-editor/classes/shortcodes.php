<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_ShortCodes
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_ShortCodes {


	function __construct() {

		add_shortcode( 'job_field', array( $this, 'shortcode_output' ) );
		add_shortcode( 'company_field', array( $this, 'shortcode_output' ) );
		add_shortcode( 'custom_field', array( $this, 'shortcode_output' ) );
		add_shortcode( 'resume_field', array( $this, 'shortcode_output' ) );

	}

	/**
	 * Output for Shortcode
	 *
	 * @since 1.1.9
	 *
	 * @param $atts
	 *
	 * @return mixed|null
	 */
	function shortcode_output( $atts, $content = '', $tag = 'jmfe' ) {
		global $job_preview, $resume_preview;

		$listing_id = absint( get_the_ID() );

		$default_atts = array(
			'key'                  => '',
			'field'                => '',
			'listing_id'           => $listing_id,
			'if_equals'            => '',
			'if_contains'          => '',
			'has_value'            => '',
			'has_value_containing' => '',
			'case_sensitive'       => FALSE
		);

		// If job preview step, try and pull ID from the submit job class object
		if ( ! empty( $_POST['submit_job'] ) && ! empty( $job_preview ) && class_exists( 'WP_Job_Manager_Form_Submit_Job' ) ) {

			$wpjmsj = WP_Job_Manager_Form_Submit_Job::instance();
			$job_id = $wpjmsj->get_job_id();

			if ( ! empty( $job_id ) ) {
				$default_atts['listing_id'] = $job_id;
			}
		// If resume preview step, try and pull ID from the submit job class object
		} elseif ( ! empty( $_POST['submit_resume'] ) && ! empty( $resume_preview ) && class_exists( 'WP_Resume_Manager_Form_Submit_Resume' ) ) {

			$wprmsr    = WP_Resume_Manager_Form_Submit_Resume::instance();
			$resume_id = $wprmsr->get_resume_id();

			if ( ! empty( $resume_id ) ) {
				$default_atts['listing_id'] = $resume_id;
			}
		} elseif ( ! empty( $_POST['submit_job'] ) && ! empty( $_COOKIE['wp-job-manager-submitting-job-id'] ) && ! empty( $_COOKIE['wp-job-manager-submitting-job-key'] ) ) {

			$cookie_id = absint( $_COOKIE['wp-job-manager-submitting-job-id'] );

			if ( get_post_meta( $cookie_id, '_submitting_key', TRUE ) === $_COOKIE['wp-job-manager-submitting-job-key'] ) {
				// Prefer the cookie set ID over the loop ID as long as it's a guest posting, or author matches current user ID
				$default_atts['listing_id'] = absint( $_COOKIE['wp-job-manager-submitting-job-id'] );
			}

		// No value set for listing_id, let's try and use query object to get ID
		} elseif( empty( $default_atts['listing_id'] ) ) {
			// Loop ID take priority over query object ID
			$qo = get_queried_object();

			if ( is_object( $qo ) && isset( $qo->ID ) ) {

				$shortcode_post_types = apply_filters( 'job_manager_field_editor_shortcode_output_post_types', array( 'job_listing', 'resume' ) );

				// If queried object post type is supported post type, set listing_id to query object ID
				if ( in_array( get_post_type( $qo->ID, $shortcode_post_types ) ) ) {
					$default_atts['listing_id'] = $qo->ID;
				}

			}

		}

		// Check if post ID was passed as post_id, listing_id, or just id, and override current set value
		if ( isset( $atts['post_id'] ) && ! empty( $atts['post_id'] ) ) {
			$atts['listing_id'] = $atts['post_id'];
		}
		if ( isset( $atts['listing_id'] ) && ! empty( $atts['listing_id'] ) ) {
			$atts['listing_id'] = $atts['listing_id'];
		}
		if ( isset( $atts['id'] ) && ! empty( $atts['id'] ) ) {
			$atts['listing_id'] = $atts['id'];
		}

		try {

			$args = is_array( $atts ) && ! empty( $atts ) ? array_merge( $default_atts, $atts ) : $default_atts;

			// Pass through shortcode filter for attributes
			$args = apply_filters( "shortcode_atts_{$tag}", $args, $default_atts, $atts, $tag );

			// Replace listing_id with resume_id if passed in arguments
			if ( array_key_exists( 'resume_id', $args ) && ! empty( $args['resume_id'] ) ) {
				$args['listing_id'] = $args['resume_id'];
			}

			// Replace listing_id with job_id if passed in arguments
			if( array_key_exists( 'job_id', $args ) && ! empty( $args['job_id'] ) ){
				$args['listing_id'] = $args['job_id'];
			}

			if ( empty( $args['key'] ) && empty( $args['field'] ) ) {
				throw new Exception( __( 'Meta Key was not specified!', 'wp-job-manager-field-editor' ) );
			}

			if ( empty( $args['listing_id'] ) ) {
				throw new Exception( __( 'Unable to determine correct job/resume/post ID!', 'wp-job-manager-field-editor' ) );
			}

			if ( $args['key'] ) {
				$meta_key = $args['key'];
			}
			if ( $args['field'] ) {
				$meta_key = $args['field'];
			}

			/**
			 * When content is not empty, means it's being used as a sort-of "if" statement for a field, to only output what
			 * is inside the shortcode content area if the field has a value.
			 */
			if( ! empty( $content ) ){

				$content_if = $content_else = '';
				$conditional_check = FALSE;

				// Separate out content if there is an [else] inside
				if ( strpos( $content, '[else]' ) !== FALSE ) {
					list( $content_if, $content_else ) = explode( '[else]', $content, 2 );
				} else {
					$content_if = $content;
				}

				$field_value = get_custom_field( $meta_key, $args['listing_id'], $args );

				// No value means we output nothing
				if( empty( $field_value ) ){
					return '';
				}

				if( is_array( $field_value ) ){

					foreach( $field_value as $fval ){

						// Match found, either exact or containing
						if( $this->value_conditional( $fval, $args, TRUE ) ){
							$conditional_check = true;
							// Break from foreach loop after finding match
							break;
						}

					}

				} else {
					$conditional_check = $this->value_conditional( $field_value, $args );
				}

				// Check for "NOT" to negate (reverse) the statement
				$att_values = array_values( $args );
				$negate     = in_array( 'NOT', $att_values ) || in_array( 'not', $att_values );

				if( $negate ){
					$conditional_check = ! $conditional_check;
				}

				// Set output content equal to if or else content, based on the conditional check
				$output_content = $conditional_check ? $content_if : $content_else;
				// Return and run do_shortcode() for any nested shortcodes
				return do_shortcode( $output_content );
			}

			ob_start();
			the_custom_field( $meta_key, $args['listing_id'], $args );
			$shortcode_output = ob_get_contents();
			ob_end_clean();

			return $shortcode_output;

		} catch ( Exception $error ) {

			error_log( 'Shortcode output error: ' . $error->getMessage() );

		}

		// Return empty string as last resort if nothing else worked
		return '';
	}

	/**
	 * Check Value Conditional Statements
	 *
	 * This method handles checking for `has_value` or `has_value_containing` for array fields,
	 * or `if_equals` and `if_contains` for non array fields, and returns boolean based on the
	 * check.   Does NOT handle negate statement, that should be handled in main shortcode handler.
	 *
	 *
	 * @since 1.7.0
	 *
	 * @param      $value
	 * @param      $args
	 * @param bool $array
	 *
	 * @return bool
	 */
	public function value_conditional( $value, $args, $array = false ){

		$conditional_success = FALSE;
		$equals_key = $array ? 'has_value' : 'if_equals';
		$contains_key = $array ? 'has_value_containing' : 'if_contains';
		$case_sensitive = ! empty( $args['case_sensitive'] );

		$check_value = ! $case_sensitive ? strtolower( $value ) : $value;

		// if_equals or has_value
		if ( ! empty( $args[ $equals_key ] ) ) {

			$if_equals = ! $case_sensitive ? strtolower( $args[ $equals_key ] ) : $args[ $equals_key ];

			if ( $check_value == $if_equals ) {
				$conditional_success = TRUE;
			}
		// if_contains or has_value_containing
		} elseif ( ! empty( $args[ $contains_key ] ) ) {

			$if_contains = ! $case_sensitive ? strtolower( $args[ $contains_key ] ) : $args[ $contains_key ];

			if ( strpos( $check_value, $if_contains ) !== FALSE ) {
				$conditional_success = TRUE;
			}

		}

		return $conditional_success;
	}
}

new WP_Job_Manager_Field_Editor_ShortCodes();
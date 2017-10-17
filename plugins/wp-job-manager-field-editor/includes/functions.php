<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'get_job_field' ) ){

	/**
	 * Get Job Field Value
	 *
	 * Will return any default or custom job field values
	 * from specific job if post ID is included, otherwise
	 * will return from current job posting.
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug Meta key from job posting
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @return mixed|null
	 */
	function get_job_field( $field_slug, $job_id = null, $args = array() ){

		if( ! $job_id ) $job_id = get_the_ID();

		$field_value = get_custom_field_listing_meta( $field_slug, $job_id, $args );

		return $field_value;

	}

}

if ( ! function_exists( 'the_job_field' ) ) {

	/**
	 * Echo Job Field Value
	 *
	 * Same as get_job_field except will echo out the value
	 *
	 * @since    1.1.8
	 *
	 * @param       $field_slug Meta key from job posting
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @internal param null $output_as
	 * @internal param null $caption
	 * @internal param null $extra_classes
	 */
	function the_job_field( $field_slug, $job_id = null, $args = array() ) {

		$field_value = get_job_field( $field_slug, $job_id );
		the_custom_field_output_as( $field_slug, $job_id, $field_value, $args );

	}

}

if ( ! function_exists( 'get_company_field' ) ) {

	/**
	 * Get Company Field Value
	 *
	 * Will return any default or custom job field values
	 * from specific job if post ID is included, otherwise
	 * will return from current job posting.
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug Meta key from job posting
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @return mixed|null
	 */
	function get_company_field( $field_slug, $job_id = null, $args = array() ) {

		if ( ! $job_id ) $job_id = get_the_ID();

		$field_value = get_custom_field_listing_meta( $field_slug, $job_id, $args );

		return $field_value;

	}

}

if ( ! function_exists( 'the_company_field' ) ) {

	/**
	 * Echo Company Field Value
	 *
	 * Same as get_company_field except will echo out the value
	 *
	 * @since    1.1.8
	 *
	 * @param       $field_slug Meta key from job posting
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @internal param null $output_as
	 * @internal param null $caption
	 * @internal param null $extra_classes
	 */
	function the_company_field( $field_slug, $job_id = null, $args = array() ) {

		$field_value = get_company_field( $field_slug, $job_id );
		the_custom_field_output_as( $field_slug, $job_id, $field_value, $args );

	}

}

if ( ! function_exists( 'get_resume_field' ) ) {

	/**
	 * Get Resume Field Value
	 *
	 * Will return any default or custom resume field values
	 * from specific resume if post ID is included, otherwise
	 * will return from current resume listing.
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug Meta key from resume listing
	 * @param null  $resume_id  Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @return mixed|null
	 */
	function get_resume_field( $field_slug, $resume_id = null, $args = array() ) {

		if ( ! $resume_id ) $resume_id = get_the_ID();

		$field_value = get_custom_field_listing_meta( $field_slug, $resume_id, $args );

		return $field_value;

	}

}

if ( ! function_exists( 'the_resume_field' ) ) {

	/**
	 * Echo Resume Field Value
	 *
	 * Same as get_resume_field except will echo out the value
	 *
	 * @since    1.1.8
	 *
	 * @param       $field_slug Meta key from resume listing
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @internal param null $output_as
	 * @internal param null $caption
	 * @internal param null $extra_classes
	 */
	function the_resume_field( $field_slug, $job_id = null, $args = array() ) {

		$field_value = get_resume_field( $field_slug, $job_id );
		the_custom_field_output_as( $field_slug, $job_id, $field_value, $args );

	}

}

if ( ! function_exists( 'get_custom_field' ) ) {

	/**
	 * Get Custom Field Value
	 *
	 * Will return any default or custom field values
	 * from specific post if post ID is included, otherwise
	 * will return from current post.
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug Meta key from post
	 * @param null  $post_id    Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @return mixed|null
	 */
	function get_custom_field( $field_slug, $post_id = null, $args = array() ) {

		if ( ! $post_id ) $post_id = get_the_ID();

		$field_value = get_custom_field_listing_meta( $field_slug, $post_id, $args );

		return $field_value;

	}

}

if ( ! function_exists( 'the_custom_field' ) ) {

	/**
	 * Echo Custom Field Value
	 *
	 * Same as get_custom_field except will echo out the value
	 *
	 * @since    1.1.8
	 *
	 * @param       $field_slug Meta key from post
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @internal param null $output_as
	 * @internal param null $caption
	 * @internal param null $extra_classes
	 */
	function the_custom_field( $field_slug, $job_id = null, $args = array() ) {

		$field_value = get_custom_field( $field_slug, $job_id, $args );
		the_custom_field_output_as( $field_slug, $job_id, $field_value, $args );

	}

}

if ( ! function_exists( 'get_custom_field_listing_meta' ) ){

	/**
	 * Get meta key or taxonomy value from listing
	 *
	 * Check for arguments that specify taxonomy, if specified check if the listing has
	 * any values saved for taxonomy, otherwise get value from post meta
	 *
	 *
	 * @since 1.2.6
	 *
	 * @param $field_slug
	 * @param $listing_id
	 * @param $args
	 *
	 * @return mixed|null
	 */
	function get_custom_field_listing_meta( $field_slug, $listing_id, $args = array() ){

		// Make sure the listing ID passed is not a page ID
		if ( is_page( $listing_id ) ) return FALSE;

		$field_value = array();
		$post_type = get_post_type( $listing_id );

		if( $field_slug === 'job_title' || $field_slug === 'candidate_name' ) return apply_filters( 'field_listing_title', get_the_title( $listing_id ) );
		if( $field_slug === 'job_description' || $field_slug === 'resume_content' ) return apply_filters( 'field_listing_content', get_the_content( $listing_id ) );

		$jmfe = WP_Job_Manager_Field_Editor_Fields::get_instance();
		$all_fields = $jmfe->get_fields();

		// Loops through field groups checking if meta key exists
		foreach( $all_fields as $field_group => $fields ){
			if( array_key_exists( $field_slug, $all_fields[ $field_group ] ) ){
				// Merge configured arguments with arguments passed to function.
				// Arguments passed to function take precendence
				$args = array_merge( $fields[ $field_slug ], $args );
				// Break out of for loop once meta key is found
				break;
			}
		}

		// Handle taxonomy items
		if( isset($args['taxonomy']) && ! empty($args['taxonomy']) ) {

			$taxonomy_slug = $args['taxonomy'];
			$field_terms = get_the_terms( $listing_id, $taxonomy_slug );

			if( $field_terms && ! is_wp_error( $field_terms ) ) {
				$field_value = array();
				foreach( $field_terms as $field_term ) {
					$field_value[] = $field_term->name;
				}
			}

			/**
			 * Custom taxonomy output sorting support
			 *
			 * @see https://plugins.smyl.es/docs-kb/customize-taxonomy-output-order-sorting-by-name
			 */
			$tax_sorted = apply_filters( "field_editor_{$taxonomy_slug}_tax_output_sort", array(), $field_slug, $listing_id, $args );

			if( ! empty( $tax_sorted ) && ! empty( $field_value ) ){

				// Create case-insensitive arrays to search with
				$lower_values = array_map( 'strtolower', $field_value );
				$lower_sorted = array_map( 'strtolower', $tax_sorted );
				$sorted_values = array();

				/**
				 * Loop through all sort values searching for matching value in output values array
				 *
				 * In order to do a case-insensitive sorting, we create new arrays from our sort values and our actual values,
				 * then loop through each sort value in order of sort, searching for the index of that value in our actual values
				 * array.  If it's found, we add it to our new $sorted_values array, setting the value from the original values array.
				 *
				 * This allows us to do a case-insensitive search, while retaining the original formatting/capitalization for the value.
				 */
				foreach( (array) $lower_sorted as $lower_tax_sort ) {

					// Get index in output values array for sort value
					$sort_index = array_search( $lower_tax_sort, $lower_values );

					if ( $sort_index !== FALSE ) {
						$sorted_values[] = $field_value[ $sort_index ];
					}

				}

				// Check for any values that should be output/returned, and were not in sort array
				$unsorted_values = array_diff( $lower_values, $lower_sorted );

				// If values found not in sort array, append to the end of the array after sorted values
				if( ! empty( $unsorted_values ) ){
					foreach( (array) $unsorted_values as $unsorted_index => $unsorted_value ){
						$sorted_values[] = $field_value[ $unsorted_index ];
					}
				}

				// Set $field_value to our new $sorted_values array
				$field_value = $sorted_values;
			}

		}

		// If value not already set, or not set by taxonomy, pull from meta
		if ( empty( $field_value ) ) $field_value = get_post_meta( $listing_id, '_' . $field_slug, TRUE );

		if( isset( $args['type'] ) && $args['type'] === 'date' ) $field_value = WP_Job_Manager_Field_Editor_Fields_Date::convert_to_display( $field_value, $field_slug, $listing_id );

		$field_value = WP_Job_Manager_Field_Editor_Fields_Options::maybe_get_label( $field_value, $args );

		return $field_value;
	}

}

if ( ! function_exists( 'the_custom_field_output_as' ) ) {

	/**
	 * General function to output HTML for custom fields
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param string $field_slug
	 * @param null   $job_id
	 * @param mixed  $field_value
	 * @param array  $args
	 */
	function the_custom_field_output_as( $field_slug, $job_id = null, $field_value, $args = array() ) {

		global $content_width;

		// Sometimes a value of 0 is supported (checkbox false, etc)
		// so we have to check for that as well
		/** @noinspection TypeUnsafeComparisonInspection */
		if( empty( $field_value ) && ! is_numeric( $field_value ) ) return;

		$label_show_colon = true;
		$ul_exists = false;
		$field_config = get_custom_field_config( $field_slug );

		/**
		 * If set, this will merge field configuration with passed $args configuration,
		 * allowing a shortcode or field output to inherit config from auto output.
		 */
		if( array_key_exists( 'inherit_config', $args ) && ! empty( $args['inherit_config'] ) ){
			$args = array_merge( $field_config, $args );
		}

		/**
		 * Required fields needed to be set for correct output handling
		 *
		 * This will loop through all required fields, adding to $args if not already set,
		 * pulled from field config, to prevent any issues with output handling.
		 */
		$req_fields = array( 'meta_key', 'label', 'taxonomy', 'type' );
		foreach( $req_fields as $req_field ){
			if( array_key_exists( $req_field, $args ) ) continue;
			$args[ $req_field ] = array_key_exists( $req_field, $field_config ) ? $field_config[ $req_field ] : '';
		}

		$field_value = apply_filters( 'field_editor_output_as_value', $field_value, $field_slug, $job_id, $args );
		$field_value = apply_filters( "field_editor_output_as_value_{$field_slug}", $field_value, $field_slug, $job_id, $args );
		$args        = apply_filters( "field_editor_output_as_args", $args, $field_value, $field_slug, $job_id );
		$args        = apply_filters( "field_editor_output_as_args_{$field_slug}", $args, $field_value, $job_id );

		// $args['li'] means output location is already inside a <ul> tag, so set $output_fw and $wrap_in_ul to false;
		if( isset($args['li']) && ! empty($args['li']) ) {
			$ul_exists = true;
			$args['output_enable_fw'] = true;
			$args['output_enable_vw'] = false;
			$args['output_full_wrap'] = 'li';
		}

		$output_as     = isset($args['output_as']) && ! empty($args['output_as']) ? $args['output_as'] : 'text';
		$extra_classes = isset($args['output_classes']) && ! empty($args['output_classes']) ? $args['output_classes'] : '';
		$full_wrapper  = isset($args['output_full_wrap']) && ! empty($args['output_full_wrap']) ? sanitize_html_class( strtolower( $args['output_full_wrap'] ), 'div' ) : 'div';
		$value_wrapper = isset($args['output_value_wrap']) && ! empty($args['output_value_wrap']) ? sanitize_html_class( strtolower( $args['output_value_wrap'] ), 'div' ) : 'div';
		$label_wrapper = isset($args['output_label_wrap']) && ! empty($args['output_label_wrap']) ? sanitize_html_class( strtolower( $args['output_label_wrap'] ), 'strong' ) : 'strong';
		$enable_vw = isset($args['output_enable_vw']) && ! empty($args['output_enable_vw']) ? TRUE : FALSE;
		$enable_fw = isset($args['output_enable_fw']) && ! empty($args['output_enable_fw']) ? TRUE : FALSE;
		// Output wrapper attributes
		$fw_atts = array_key_exists( 'output_fw_atts', $args ) ? $args['output_fw_atts'] : '';
		$vw_atts = array_key_exists( 'output_vw_atts', $args ) ? $args['output_vw_atts'] : '';
		$lw_atts = array_key_exists( 'output_lw_atts', $args ) ? $args['output_lw_atts'] : '';
		$fw_classes = isset($args['fw_classes']) && ! empty($args['fw_classes']) ? $args['fw_classes'] : '';

		$open_wrapper  = "<{$full_wrapper} id=\"jmfe-wrap-{$field_slug}\" class=\"jmfe-custom-field-wrap {$fw_classes}\" {$fw_atts}>";
		$close_wrapper = "</{$full_wrapper}>";

		// Automatically add <p> through wpautop()
		$wpautop_fields = maybe_unserialize( get_option( 'jmfe_output_wpautop', array() ) );
		if( is_array( $wpautop_fields ) && ! empty( $wpautop_fields ) && isset($args['type']) && in_array( $args['type'], $wpautop_fields ) ) $field_value = wpautop( $field_value );

		// If output as is checkbox label only if checked, set output_show_label to true
		if( $output_as == 'checklabel' && (int) $field_value == 1 ) {
			$label_show_colon = false;
			$args[ 'output_show_label' ] = true;
		}

		$label = ( ! empty( $args[ 'output_show_label' ] ) && ! empty( $args[ 'label' ] ) ) ? $args[ 'label' ] : NULL;

		// Handle multiple values output (probably file upload or taxonomy)
		if( is_array( $field_value ) ) {

			// Put label in its own div for mutliple output
			if( ! empty($label) ) {
				echo "<div id=\"jmfe-wrap-{$field_slug}-multi-label\" class=\"jmfe-custom-field-wrap jmfe-custom-field-multi-label\">";
				echo "<{$label_wrapper} id=\"jmfe-label-{$field_slug}\" class=\"jmfe-custom-field-label\">{$label}:</{$label_wrapper}> ";
				echo "</div>";
				$args['output_show_label'] = 0;
			}

			if( $output_as !== 'value' && $enable_fw ) echo $open_wrapper;

			foreach( $field_value as $single_value ) {
				$args['output_enable_fw'] = false;
				the_custom_field_output_as( $field_slug, NULL, $single_value, $args );
				// Value and full wrap are not defined, output filterable <br />
				if( ! $enable_fw && ! $enable_vw ) echo apply_filters( 'field_editor_output_no_wrap_after', '<br />', $field_slug, $job_id, $field_value, $args, $single_value );
			}

			if( $output_as !== 'value' && $enable_fw ) echo $close_wrapper;

			return;
		}

		if( $output_as == 'value' ){
			echo $field_value;
			return;
		}

		ob_start();

		if( $enable_fw ) echo $open_wrapper;

		// Show label if set
		if ( $label ) {
			if( apply_filters( 'job_manager_field_editor_custom_field_output_as_show_colon', $label_show_colon, $field_slug, $field_value, $args, $job_id ) ) {
				$label .= apply_filters( 'job_manager_field_editor_custom_field_output_as_colon', ':', $field_slug, $field_value, $args, $job_id );
			}
			echo "<{$label_wrapper} id=\"jmfe-label-{$field_slug}\" class=\"jmfe-custom-field-label\" {$lw_atts}>{$label}</{$label_wrapper}> ";
		}

		// Output value wrapper if enabled
		if( $enable_vw ) echo "<{$value_wrapper} id=\"jmfe-custom-{$field_slug}\" class=\"jmfe-custom-field {$extra_classes}\" {$vw_atts}>";

		switch( $output_as ){

			case 'value':
				echo $field_value;
				break;

			case 'oembed':
				$width  = intval( $args['output_oembed_width'] );
				$height = intval( $args['output_oembed_height'] );

				if( empty( $height ) && ! empty( $width ) ) $height = round( ($width / 640) * 360 );
				if( empty( $width ) && ! empty( $height )) $width = round( ($height / 360) * 640 );

				if( empty( $width ) ) $width = absint( $content_width );
				if( empty( $height ) ) $height = round( ( $width / 640) * 360 );

				$oembed_html = wp_oembed_get( $field_value, array( 'height' => $height, 'width' => $width ) );

				// Exit and clear buffer if error with oembed
				if( ! $oembed_html ) {
					ob_end_clean();
					return;
				}

				// Wrap with jetpack class to support responsive videos through jetpack
				if( apply_filters( 'field_editor_output_oembed_wrap_with_jetpack', TRUE ) ){
					$oembed_html = apply_filters( 'video_embed_html', $oembed_html );
				}

				echo $oembed_html;

				break;

			case 'video':

				$video_width = ( isset( $args['output_video_width'] ) && ! empty( $args['output_video_width'] ) ) ? " width=\"" . $args[ 'output_video_width' ] . "\""  : '';
				$video_height = ( isset( $args['output_video_height'] ) && ! empty( $args['output_video_height'] ) ) ? " height=\"" . $args[ 'output_video_height' ] . "\""  : '';
				$video_poster = ( isset( $args['output_video_poster'] ) && ! empty( $args['output_video_poster'] ) ) ? " poster=\"" . $args[ 'output_video_poster' ] . "\""  : '';

				echo "<video src=\"{$field_value}\"{$video_width}{$video_height}{$video_poster} controls>";
				_e( "Sorry, your browser doesn't support embedded videos, you should upgrade to a modern browser.", 'wp-job-manager-field-editor' );
				if( isset( $args['output_video_allowdl'] ) && ! empty( $args['output_video_allowdl'] ) ) echo "<br />" . __( "Or you can", 'wp-job-manager-field-editor') . " <a href=\"{$field_value}\">" . __( "Download The File", 'wp-job-manager-field-editor' ) . "</a>. " . __("(right click and select Save As)", 'wp-job-manager-field-editor');
				echo "</video>";
				break;

			case 'link':

				if ( empty( $args[ 'output_caption' ] ) ) {
					$args[ 'output_caption' ] = basename( $field_value );
				}

				$field_value = field_editor_check_taxonomy_link_output( $field_value, $args );
				$field_value = field_editor_set_uri_scheme( $field_value, $args );
				echo "<a target=\"_blank\" id=\"jmfe-custom-{$field_slug}\" href=\"{$field_value}\" class=\"jmfe-custom-field {$extra_classes}\">{$args['output_caption']}</a>";
				break;

			case 'image':
				if( isset( $args['image_link'] ) && ! empty( $args['image_link'] ) ) echo "<a class=\"jmfe-image-link\" href=\"{$field_value}\">";
				echo "<img id=\"jmfe-custom-{$field_slug}\" src=\"{$field_value}\" class=\"jmfe-custom-field {$extra_classes}\" />";
				if( isset($args['image_link']) && ! empty($args['image_link']) ) echo "</a>";
				break;

			// Output checkbox label only if checked has no output besides the label
			case 'checklabel':
				break;

			case 'checkcustom':
				$check_caption = '';
				// Checked
				if( (int) $field_value == 1 ){
					$check_caption = __( 'True', 'wp-job-manager-field-editor' );
					if( ! empty( $args[ 'output_check_true' ] ) ) $check_caption = $args[ 'output_check_true' ];
				}
				// Unchecked
				if( (int) $field_value == 0 ){
					$check_caption == __( 'False', 'wp-job-manager-field-editor' );
					if( ! empty( $args[ 'output_check_false' ] ) ) $check_caption = $args[ 'output_check_false' ];
				}

				echo $check_caption;
				break;

			default:
				echo $field_value;
				break;

		}

		// Close value wrapper
		if( $enable_vw ) echo "</{$value_wrapper}>";
		// Close full wrapper
		if( $enable_fw ) echo $close_wrapper;

		ob_end_flush();
	}

}

if( ! function_exists( 'job_manager_field_editor_size_to_bytes' ) ){

	/**
	 * Convert User input Sizes to Bytes
	 *
	 *
	 * @since 1.7.0
	 *
	 * @param $size
	 *
	 * @return integer
	 */
	function job_manager_field_editor_size_to_bytes( $size ){

		// Remove any whitespace (ie user inputs '10 MB' instead of '10MB')
		$size = str_replace( ' ', '', $size );
		// Convert string to all lowercase (to change MB to mb, KB to kb, etc)
		$size = strtolower( $size );

		// Check for user formatting
		if( strpos( $size, 'm') !== FALSE || strpos( $size, 'mb' ) !== FALSE ){
			// formatting in megabytes
			$size = (int) $size * 1048576;
		} elseif( strpos( $size, 'k' ) !== FALSE || strpos( $size, 'kb') !== FALSE ){
			// formatting in kilobytes
			$size = (int) $size * 1024;
		}

		return $size;
	}

}

if( ! function_exists( 'get_custom_field_config' ) ){

	/**
	 * Get Custom Field Configuration
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param   string    $meta_key        Meta key of the field to obtain configuration for
	 * @param   string    $config_key      (Optional) Return a specific configuration value (eg label, description, etc)
	 *
	 * @return array|bool $value           Returns FALSE if error, or field config not found, otherwise returns an
	 *                                       array of configuration data for the field.
	 */
	function get_custom_field_config( $meta_key = '', $config_key = ''){

		if( empty( $meta_key ) || ! class_exists( 'WP_Job_Manager_Field_Editor_Fields' ) ) return false;

		$jmfe       = WP_Job_Manager_Field_Editor_Fields::get_instance();
		$all_fields = $jmfe->get_fields();

		if( empty( $all_fields ) ) return FALSE;

		/**
		 * Loop through each field group (job, company, resume_fields),
		 * checking for meta key which will be the array key.
		 */
		foreach( $all_fields as $field_group => $fields ) {

			if( array_key_exists( $meta_key, $fields ) ) {

				if( ! empty( $config_key ) && array_key_exists( $config_key, $fields[ $meta_key ] ) ){
					return $fields[ $meta_key ][ $config_key ];
				}

				return $fields[$meta_key];
			}

		}

		/**
		 * Return FALSE when all else fails
		 */
		return FALSE;

	}

}

if ( ! function_exists( 'wp_date_format_php_to_js') ){

	/**
	 * Convert a date format to a jQuery UI DatePicker format
	 *
	 * @param string $dateFormat a date format
	 *
	 * @param bool   $flatpickr
	 *
	 * @return string
	 */
	function wp_date_format_php_to_js( $dateFormat, $flatpickr = false ) {

		// jQuery UI date picker
		$chars = array(
			// Day
			'd' => 'dd',
			'j' => 'd',
			'l' => 'DD',
			'D' => 'D',
			// Month
			'm' => 'mm',
			'n' => 'm',
			'F' => 'MM',
			'M' => 'M',
			// Year
			'Y' => 'yy',
			'y' => 'y',
		);

		if( $flatpickr ){
			// Flatpickr
			$chars = array(
				// Day
				'jS' => 'J',
				// Time
			    'g' => 'h',
			    's' => 'S',
			    // AM/PM
			    'A' => 'K',
			    // am/pm to AM/PM (there is no lowercase, so just set to uppercase one)
			    'a' => 'K'
			);
		}

		return strtr( (string) $dateFormat, $chars );
	}

}

if ( ! function_exists( 'get_attachment_id_from_url' ) ){

	/**
	 * Get image attachment ID from URL
	 *
	 * Will return the attachment ID of an image when provided the URL.
	 * This is commonly needed for getting image thumbnails, etc.
	 *
	 *
	 * @since 1.2.7
	 *
	 * @param string $attachment_url
	 *
	 * @return bool|null|string|void
	 */
	function get_attachment_id_from_url( $attachment_url = '' ) {

		if ( function_exists( 'attachment_url_to_postid' ) ) {
			return attachment_url_to_postid( $attachment_url );
		}

		global $wpdb;
		$attachment_id = FALSE;

		// If there is no url, return.
		if ( '' == $attachment_url ) return;

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( FALSE !== strpos( $attachment_url, $upload_dir_paths[ 'baseurl' ] ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths[ 'baseurl' ] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

		}

		return $attachment_id;
	}

}

if( ! function_exists( 'field_editor_set_url_scheme' ) ) {

	/**
	 * Add scheme (http://) to URL if it does not exist already
	 *
	 * Option must be enabled in settings to process through function.
	 *
	 * No longer used by core of plugin as of 1.6.1+
	 *
	 * @since 1.4.0
	 *
	 * @param        $url
	 * @param string $scheme
	 *
	 * @return string
	 */
	function field_editor_set_url_scheme( $url, $scheme = 'http://' ){

		$enable_scheme = get_option( 'jmfe_output_as_link_url_scheme' );
		if( empty( $enable_scheme ) || empty( $url ) ) return $url;

		if( parse_url( $url, PHP_URL_SCHEME ) === NULL ) {
			$url = $scheme . $url;
		}

		$prepend_url = apply_filters( 'field_editor_output_as_link_prepend_url', '' );

		return $prepend_url . $url;

	}

}

if( ! function_exists( 'field_editor_set_uri_scheme' ) ) {

	/**
	 * Set custom URI scheme for link output
	 *
	 * This method will attempt to automatically determine the type of URI
	 * scheme to use based on the value passed to it.
	 *
	 *
	 * @see   http://www.iana.org/assignments/uri-schemes/uri-schemes.xhtml
	 *
	 * @since @@since
	 *
	 * @param        $value
	 * @param        $args
	 *
	 * @return string
	 */
	function field_editor_set_uri_scheme( $value, $args ) {

		$scheme = '';

		if( array_key_exists( 'type', $args ) && $args[ 'type' ] == 'phone' ){

			$scheme = 'tel:';

		} elseif ( is_email( $value ) ) {

			$scheme = 'mailto:';
			$value = sanitize_email( $value );

		} else {

			$enable_scheme = get_option( 'jmfe_output_as_link_url_scheme' );
			if ( ! empty( $enable_scheme ) && parse_url( $value, PHP_URL_SCHEME ) === NULL ){
				// Set default scheme to http://
				$scheme = 'http://';
				// Make sure to remove // from front of value if set (means value looks like //somedomain.com/something)
				$value = substr( $value, 0, 2 ) === '//' ? substr( $value, 2 ) : $value;
			}

		}

		$scheme = apply_filters( 'field_editor_set_uri_scheme', $scheme, $value, $args );
		$value = apply_filters( 'field_editor_set_uri_scheme_value', $value, $scheme, $args );

		return $scheme . $value;

	}

}

if ( ! function_exists( 'field_editor_check_taxonomy_link_output' ) ) {

	/**
	 * Return link to taxonomy if value is taxonomy name
	 *
	 *
	 * @since @@since
	 *
	 * @param string $value
	 * @param array  $args
	 *
	 * @return string
	 */
	function field_editor_check_taxonomy_link_output( $value, $args = array() ) {

		if( empty( $args ) || ! isset( $args['taxonomy'] ) || empty( $args['taxonomy'] ) || ! taxonomy_exists( $args['taxonomy'] ) ) return $value;

		$value_term = get_term_by( 'name', $value, $args['taxonomy'] );
		if( empty( $value_term ) ) return $value;

		$value_link = get_term_link( $value_term, $args['taxonomy'] );

		if( empty( $value_link ) || is_wp_error( $value_link ) ) return $value;

		return apply_filters( 'field_editor_check_taxonomy_link_output', $value_link, $value, $args, $value_term );
	}

}

if( ! function_exists( 'remove_class_filter' ) ){
	/**
	 * Remove Class Filter Without Access to Class Object
	 *
	 * In order to use the core WordPress remove_filter() on a filter added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove filters with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
	 * Updated 2-27-2017 to use internal WordPress removal for 4.7+ (to prevent PHP warnings output)
	 *
	 * @param string $tag         Filter to remove
	 * @param string $class_name  Class name for the filter's callback
	 * @param string $method_name Method name for the filter's callback
	 * @param int    $priority    Priority of the filter (default 10)
	 *
	 * @return bool Whether the function is removed.
	 */
	function remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {

		global $wp_filter;

		// Check that filter actually exists first
		if ( ! isset( $wp_filter[ $tag ] ) ) {
			return FALSE;
		}

		/**
		 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
		 * a simple array, rather it is an object that implements the ArrayAccess interface.
		 *
		 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
		 *
		 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
		 */
		if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
			// Create $fob object from filter tag, to use below
			$fob       = $wp_filter[ $tag ];
			$callbacks = &$wp_filter[ $tag ]->callbacks;
		} else {
			$callbacks = &$wp_filter[ $tag ];
		}

		// Exit if there aren't any callbacks for specified priority
		if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
			return FALSE;
		}

		// Loop through each filter for the specified priority, looking for our class & method
		foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {

			// Filter should always be an array - array( $this, 'method' ), if not goto next
			if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
				continue;
			}

			// If first value in array is not an object, it can't be a class
			if ( ! is_object( $filter['function'][0] ) ) {
				continue;
			}

			// Method doesn't match the one we're looking for, goto next
			if ( $filter['function'][1] !== $method_name ) {
				continue;
			}

			// Method matched, now let's check the Class
			if ( get_class( $filter['function'][0] ) === $class_name ) {

				// WordPress 4.7+ use core remove_filter() since we found the class object
				if ( isset( $fob ) ) {
					// Handles removing filter, reseting callback priority keys mid-iteration, etc.
					$fob->remove_filter( $tag, $filter['function'], $priority );

				} else {
					// Use legacy removal process (pre 4.7)
					unset( $callbacks[ $priority ][ $filter_id ] );
					// and if it was the only filter in that priority, unset that priority
					if ( empty( $callbacks[ $priority ] ) ) {
						unset( $callbacks[ $priority ] );
					}
					// and if the only filter for that tag, set the tag to an empty array
					if ( empty( $callbacks ) ) {
						$callbacks = array();
					}
					// Remove this filter from merged_filters, which specifies if filters have been sorted
					unset( $GLOBALS['merged_filters'][ $tag ] );
				}

				return TRUE;
			}
		}

		return FALSE;
	}
}

if( ! function_exists( 'remove_class_action' ) ){
	/**
	 * Remove Class Action Without Access to Class Object
	 *
	 * In order to use the core WordPress remove_action() on an action added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove actions with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
	 *
	 * @param string $tag         Action to remove
	 * @param string $class_name  Class name for the action's callback
	 * @param string $method_name Method name for the action's callback
	 * @param int    $priority    Priority of the action (default 10)
	 *
	 * @return bool               Whether the function is removed.
	 */
	function remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
		remove_class_filter( $tag, $class_name, $method_name, $priority );
	}
}
<?php

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Plugins_FacetWP
 *
 * @since 1.5.0
 *
 */
class WP_Job_Manager_Field_Editor_Plugins_FacetWP {

	/**
	 * @var
	 */
	public  $post_types;
	/**
	 * @var array
	 */
	private $fields = array();

	/**
	 * WP_Job_Manager_Field_Editor_Plugins_FacetWP constructor.
	 */
	public function __construct() {

		add_filter( 'facetwp_facet_sources', array( $this, 'sources' ) );
		add_filter( 'facetwp_indexer_post_facet', array( $this, 'custom_index' ), 15, 2 );

		// New filter added in FacetWP 2.6.3+
		//add_filter( 'facetwp_indexer_row_data', array( $this, 'custom_index' ), 15, 2 );

		/**
		 * This core FacetWP filter is also called in $this->custom_index
		 */
		add_filter( 'facetwp_index_row', array( $this, 'index_row' ), 9, 2 );

		$enable_force_reindex = get_option( 'jmfe_facetwp', FALSE );

		if( ! empty( $enable_force_reindex ) ){
			add_action( 'field_editor_update_field_post_meta_end', array( $this, 'force_reindex' ), 10, 5 );
		}

		add_filter( 'job_manager_field_editor_settings', array( $this, 'settings' ) );

		// Translate on output
		add_filter( 'facetwp_facet_render_args', array( $this, 'output' ) );

		add_filter( 'field_editor_check_taxonomy_link_output', array( $this, 'taxonomy_link' ), 99, 4 );
	}

	/**
	 * Translate Options on FacetWP Output
	 *
	 * By default FacetWP relies on plugins to handle translations.  Since we use string translation for translating option values, we
	 * need to filter FacetWP before output to translate the value of an option, if possible.
	 *
	 *
	 * @since 1.7.0
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	function output( $args ){

		if( ! array_key_exists( 'facet', $args ) || ! array_key_exists( 'values', $args ) || ! array_key_exists( 'source', $args['facet'] ) || empty( $args['values' ] ) ){
			return $args;
		}

		// Get key from source (has to be passed as value with `facet_source` as key in array)
		$source = $this->get_choice_key( array( 'facet_source' => $args['facet']['source'] ) );
		$meta_key = $this->get_meta_key( array( 'facet_source' => $args['facet']['source'] ) );

		if( empty( $source ) || empty( $meta_key ) ){
			return $args;
		}

		// Supported sources
		$sources = apply_filters( 'job_manager_field_editor_facetwp_translate_sources', array( 'job_fields', 'company_fields', 'resume_fields' ) );

		if( ! in_array( $source, $sources, TRUE ) ){
			return $args;
		}

		// Loop through each value to output/display through FacetWP
		foreach ( (array) $args['values'] as $index => $val_config ) {

			$field_config  = $this->get_field( $meta_key );
			$value         = $val_config['facet_value'];
			$display_value = $val_config['facet_display_value'];

			// Skip to next if no config found, or no options found in field config
			if ( empty( $field_config ) || ! array_key_exists( 'options', $field_config ) || empty( $field_config['options'] ) ) {
				continue;
			}

			// Make sure to remove configuration characters from option keys
			$field_config = WP_Job_Manager_Field_Editor_Fields_Options::clean_option_keys( $field_config );

			// Found an option in the field config, let's make sure to translate the label for output
			// Try to use the facet value first
			if ( array_key_exists( $value, $field_config['options'] ) ) {
				$args['values'][ $index ]['facet_display_value'] = WP_Job_Manager_Field_Editor_Translations::translate( $field_config['options'][ $value ], $meta_key, "options {$value}", $field_config['field_group'] );
			// IF that doesn't work, try using the display value instead
			} elseif ( array_key_exists( $display_value, $field_config['options'] ) ) {
				$args['values'][ $index ]['facet_display_value'] = WP_Job_Manager_Field_Editor_Translations::translate( $field_config['options'][ $display_value ], $meta_key, "options {$display_value}", $field_config['field_group'] );
			}

		}

		return $args;
	}

	/**
	 * Change Taxonomy Links to Submit Listing Page Filtered on Facet
	 *
	 *
	 * @since 1.7.0
	 *
	 * @param $value_link
	 * @param $value
	 * @param $args
	 * @param $value_term
	 *
	 * @return string
	 */
	function taxonomy_link( $value_link, $value, $args, $value_term ){

		if( ! function_exists( 'FWP' ) || ! get_option( 'jmfe_facetwp_taxonomy_link', true ) ){
			return $value_link;
		}

		$meta_key = $args['meta_key'];
		$taxonomy = $args['taxonomy'];
		$field_group = false;
		$all_fields = WP_Job_Manager_Field_Editor_Fields::get_instance()->get_fields();

		// First we need to figure out the field group
		foreach ( $all_fields as $group => $fields ) {

			if ( array_key_exists( $args['meta_key'], $fields ) ) {
				$field_group = $group;
				break;
			}

		}

		if( ! $field_group ){
			return $value_link;
		}

		$found  = FALSE;
		$facets = FWP()->helper->get_facets();

		// Set field group to include _fields to match Facet source type
		$field_group = strpos( $field_group, '_fields' ) !== FALSE ? $field_group : "{$field_group}_fields";

		// Loop through facets looking for our taxonomy
		foreach ( (array) $facets as $facet ) {

			if ( "tax/{$taxonomy}" == $facet['source'] || "{$field_group}/{$meta_key}" == $facet['source'] ) {
				$found = $facet['name'];
				break;
			}

		}

		// Facet was found, let's generate the new URL to use
		if( $found ) {
			$listings_url = apply_filters( 'job_manager_field_editor_facetwp_taxonomy_link_listings_url', $field_group === 'resume_fields' ? resume_manager_get_permalink( 'resumes' ) : job_manager_get_permalink( 'jobs' ), $value_link, $value, $args, $value_term );
			$value_link = add_query_arg( array( "fwp_{$found}" => $value_term->slug ), $listings_url );
		}

		return $value_link;
	}

	/**
	 * Add FacetWP settings tab to Field Editor Settings page
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	function settings( $settings ){

		$settings['facetwp'][0] = __( 'FacetWP', 'wp-job-manager-field-editor' );
		$settings['facetwp'][1][] =
			array(
				'name'       => 'jmfe_facetwp',
				'std'        => '0',
				'label'      => __( 'Indexing', 'wp-job-manager-field-editor' ),
				'cb_label'   => __( 'Yes, force reindex when adding or updating a field configuration', 'wp-job-manager-field-editor' ),
				'desc'       => __( 'FacetWP uses its own database table to store value/label information for facets.  Enable this option to automatically force FacetWP to reindex whenever a field is added or updated. <strong>If you get errors when saving/adding fields, or it takes a very long time, disable this option and manually reindex after adding/updating fields.</strong>', 'wp-job-manager-field-editor' ),
				'type'       => 'checkbox',
				'attributes' => array()
			);
		$settings['facetwp'][1][] =
			array(
				'name'       => 'jmfe_facetwp_taxonomy_link',
				'std'        => '1',
				'label'      => __( 'Taxonomy Links', 'wp-job-manager-field-editor' ),
				'cb_label'   => __( 'Yes, when using output as Link for taxonomies, link to submit listing page with facet filter', 'wp-job-manager-field-editor' ),
				'desc'       => __( 'Normally when outputting a taxonomy using the Link output as, it will link to the taxonomy archive, enable this setting to link to the listings page with the facet filter instead.', 'wp-job-manager-field-editor' ),
				'type'       => 'checkbox',
				'attributes' => array()
			);

		return $settings;
	}

	/**
	 * Add WPJM fields as custom source
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $sources
	 *
	 * @return array
	 */
	function sources( $sources ){

		$new_sources = array();

		$field_groups = $this->get_fields();
		if( empty( $field_groups ) || ! is_array( $field_groups ) ) return $sources;

		/**
		 * Loop through all field groups, building sources array
		 */
		foreach( $field_groups as $group => $fields ){

			/**
			 * Goto next group if for some reason there are no fields
			 */
			if( empty( $fields ) ) continue;

			$sources_key = strpos( $group, '_fields' ) !== FALSE ? $group : "{$group}_fields";
			$sources_label = ucfirst( $group === 'job' ? WP_Job_Manager_Field_Editor::get_job_post_label() : str_replace( '_fields', '', $sources_key ) );

			$new_sources[ $sources_key ] = array(
				'label'   => sprintf( __( '%1$s Fields', 'wp-job-manager-field-editor' ), $sources_label ),
				'choices' => array()
			);

			/**
			 * Loop through all fields adding them as available choices
			 */
			foreach( $fields as $field => $config ){

				$label = empty( $config[ 'label' ] ) ? $field : $config[ 'label' ] . " ({$field})";
				$choices_key = $sources_key;
				//$choices_key = 'jmfe';

				/**
				 * Handle taxonomy field types
				 */
				if( array_key_exists( 'taxonomy', $config ) && strpos( $config[ 'type' ], 'term-' ) !== FALSE && taxonomy_exists( $config['taxonomy'] ) ){
					$field = $config['taxonomy'];
					$choices_key = 'tax';
				}

				/**
				 * Set the available choice in our new array
				 */
				$new_sources[ $sources_key ][ 'choices' ][ "{$choices_key}/{$field}" ] = $label;
			}

		}

		/**
		 * Return our new array, plus the original one (to place ours at the top)
		 */
		return $new_sources + $sources;
	}

	/**
	 * Filter FacetWP indexing to index WP Job Manager fields (when custom fields are used)
	 *
	 * This method is specifically for when a user selects a meta key from a custom field (starts with cf/_),
	 * or one of the custom added WPJM fields @see $this->get_choice_keys()
	 *
	 * Some WP Job Manager fields are saved as serialized arrays, and due to this, we need to
	 * make sure and unserialize that data to make it indexable.
	 *
	 * This method uses the `facetwp_indexer_post_facet` filter that runs before FacetWP attempts to
	 * get the data itself.  If we return the $falsey variable (which is FALSE by default), FacetWP
	 * will attempt to pull the data itself, if we return TRUE, it will not, and will move on to
	 * the next Facet in the loop.
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $falsey   boolean     Normally FALSE to allow FacetWP to handle pulling data to index
	 * @param $config   array       Array of configuration data, includes the `defaults` and `facet` keys.
	 *
	 * @return boolean              Return TRUE to prevent FacetWP from pulling data to index, FALSE to allow it to
	 */
	function custom_index( $falsey, $config ){

		$params = $config[ 'defaults' ];
		$post_type = isset( $params['post_id'] ) ? get_post_type( $params['post_id'] ) : 'unknown';
		$meta_key = $this->get_meta_key( $params );
		$field_group = $this->get_field_group( $params );

		/**
		 * Exit if unable to get meta key, or field group.  Also exits if choice key or post type is not supported.
		 */
		if( empty( $meta_key ) || empty( $field_group ) || ! $this->supported_choice_key( $params ) || ! $this->supported_meta_key( $params ) || ! in_array( $post_type, $this->get_post_types() ) ) {
			return $falsey;
		}

		/**
		 * Get FacetWP Indexer Class Object
		 * Should exist already, but just to be on the safe side, exit if it doesn't
		 */
		$indexer = function_exists( 'FWP' ) && FWP()->indexer instanceof FacetWP_Indexer ? FWP()->indexer : FALSE;
		if( empty( $indexer ) ) return $falsey;

		/**
		 * If this is a custom field type, we need to set the meta key to the actual key WITHOUT the prepended underscore.
		 * This is done to prevent double underscores when attempting to pull meta below.
		 */
		if( strpos( $params[ 'facet_source' ], 'cf/_' ) !== FALSE ){
			$meta_key = str_replace( 'cf/_', '', $params[ 'facet_source' ] );
		}

		/**
		 * Get the value(s), unserialize if needed, and set as an array even if only a single value
		 */
		$values = (array) maybe_unserialize( get_post_meta( $params[ 'post_id' ], "_{$meta_key}", TRUE ) );

		/**
		 * Index returned value(s)
		 */
		$this->index_loop( $values, $params, $indexer );

		/**
		 * Return TRUE to tell FacetWP to continue
		 */
		return TRUE;
	}

	/**
	 * Loop through value(s)
	 *
	 * This method is used to loop through the values (single or multi) and add them to
	 * the index table.  It is called by $this->index_row when the value passed to that method
	 * is an array, and by $this->custom_index to insert any custom fields.
	 *
	 * @since 1.5.0
	 *
	 * @param array           $values   Single, or array of values to index
	 * @param array           $params   FacetWP parameters
	 * @param FacetWP_Indexer $indexer  FacetWP Indexer class object
	 *
	 * @return bool
	 */
	function index_loop( $values, $params, $indexer ){

		$values = (array) $values;

		/** @var array $values */
		foreach( $values as $val ){

			$params[ 'facet_value' ] = $val;
			$params[ 'facet_display_value' ] = $val;

			/**
			 * Pass params through core FacetWP filter (which $this->index_row has a hook for)
			 */
			$params = apply_filters( 'facetwp_index_row', $params, FWP()->indexer, $this );

			if ( ! empty( $params ) ) {
				$indexer->insert( $params );
			}
		}

		return TRUE;
	}

	/**
	 * Handle custom types/values when indexing a row for FacetWP
	 *
	 * This method handles converting values from their saved format, to a format supported by
	 * FacetWP.  This includes values that may be an array of values.
	 *
	 * @since 1.5.0
	 *
	 * @param array $params FacetWP passed parameters
	 * @param FacetWP_Indexer $class Indexer object
	 *
	 * @return bool|mixed
	 */
	function index_row( $params, $class ) {

		$val = ! empty( $params ) && is_array( $params ) && array_key_exists( 'facet_value', $params ) ? $params[ 'facet_value' ] : '';

		$meta_key    = $this->get_meta_key( $params );
		$field_group = $this->get_field_group( $params );

		if ( empty( $meta_key ) || empty( $field_group ) || ! $this->supported_choice_key( $params ) || ! $this->supported_meta_key( $params )) {
			return $params;
		}

		if( is_array( $val ) ){
			$this->index_loop( $val, $params, $class );
			return false;
		}

		$field_data = $this->get_field( $meta_key, $field_group );

		/**
		 * Make sure we have field configuration
		 */
		if ( empty( $field_data ) ) {
			return $params;
		}

		$type       = $field_data[ 'type' ];

		/**
		 * Change/customize value based on type
		 *
		 * These fields don't need custom handling:
		 * term-checklist, term-select, term-multiselect (taxonomies), text, number, range, phoned
		 */
		switch( $type ) {

			case 'select':
			case 'radio':
			case 'multiselect':

				if ( isset( $field_data[ 'options' ][ $val ] ) ) {
					$params[ 'facet_display_value' ] = $field_data[ 'options' ][ $val ];
				}

				break;

			case 'date':
				// Get epoch using the saved format (or format in settings)
				$epoch = WP_Job_Manager_Field_Editor_Fields_Date::get_epoch( $val, $meta_key );
				// FacetWP dates must be in YYYY-MM-DD format!
				$params[ 'facet_value' ] = date( 'Y-m-d', $epoch );
				break;

			case 'checkbox':

				$check_false = array_key_exists( 'output_check_false', $field_data ) && ! empty( $field_data[ 'output_check_false' ] ) ? $field_data[ 'output_check_false' ] : FALSE;
				$check_false = apply_filters( 'job_manager_field_editor_facetwp_index_row_checkbox_false', $check_false, $meta_key, $field_data, $params, $class );

				$check_true = array_key_exists( 'output_check_true', $field_data ) && ! empty( $field_data[ 'output_check_true' ] ) ? $field_data[ 'output_check_true' ] : __( 'Yes', 'wp-job-manager-field-editor' );
				$check_true = apply_filters( 'job_manager_field_editor_facetwp_index_row_checkbox_true', $check_true, $meta_key, $field_data, $params, $class );

				$params[ 'facet_display_value' ] = $check_true;

				if ( empty( $val ) ) {

					if ( empty( $check_false ) ) {
						return FALSE;
					} else {
						$params[ 'facet_display_value' ] = $check_false;
					}

				}

				break;
		}

		return apply_filters( 'field_editor_facetwp_index_row', $params, $class, $this );
	}

	/**
	 * Get meta key
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $params
	 *
	 * @return string|bool  Returns false if forward slash not found in facet source, meta key if otherwise
	 */
	function get_meta_key( $params ) {

		if( ! is_array( $params ) || ! array_key_exists( 'facet_source', $params ) || strpos( $params[ 'facet_source' ], '/' ) === FALSE ){
			return FALSE;
		}

		/** @var string $meta_key The actual meta key (or field) after the / */
		$meta_key = substr( $params[ 'facet_source' ], strpos( $params[ 'facet_source' ], '/' ) + 1 );

		return $meta_key;
	}

	/**
	 * Get custom FacetWP field choice key
	 *
	 * This method returns the choice key (value before the forward slash) @see $this->get_field_group()
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $params
	 *
	 * @return string|bool  Returns false if forward slash not found in facet source, meta key if otherwise
	 */
	function get_choice_key( $params ) {

		if ( ! is_array( $params ) || ! array_key_exists( 'facet_source', $params ) || strpos( $params[ 'facet_source' ], '/' ) === FALSE ) {
			return FALSE;
		}

		/** @var string $choice_key Choice key, before the / */
		$choice_key = substr( $params[ 'facet_source' ], 0, strpos( $params[ 'facet_source' ], '/' ) );

		return $choice_key;
	}

	/**
	 * Get supported custom FacetWP choice keys
	 *
	 * This method returns the supported custom FacetWP keys @see $this->get_field_group()
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	function get_choice_keys(){

		return apply_filters( 'field_editor_facetwp_jm_field_keys', array( 'job_fields', 'company_fields', 'resume_fields', 'cf' ) );

	}

	/**
	 * Check if choice key exists, and is supported
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $params
	 *
	 * @return bool
	 */
	function supported_choice_key( $params ) {

		$choice_key = $this->get_choice_key( $params );
		$is_supported = ! empty( $choice_key ) && in_array( $choice_key, $this->get_choice_keys() );

		return $is_supported;
	}

	/**
	 * Check if meta key is supported
	 *
	 * This method checks if we have configuration for the meta key in the passed parameters.  If meta key does not
	 *
	 *
	 * @since 1.6.0
	 *
	 * @param $params
	 *
	 * @return bool
	 */
	function supported_meta_key( $params ){
		$group = $this->get_field_group( $params );
		$meta_key = $this->get_meta_key( $params );

		/**
		 * Use this filter to add additional meta keys to skip processing for.  This is only useful if the meta key is a field
		 * that has configuration for WPJM (as below this, we check to verify the meta key is a WPJM meta key).
		 */
		if( in_array( $meta_key, apply_filters( 'field_editor_facetwp_skip_meta_keys', array( 'geolocation_lat', 'geolocation_long' ) ) ) ){
			return false;
		}

		$field_data = $this->get_field( $meta_key, $group );

		// If field config is returned, means it's a supported WPJM field
		if( ! empty( $field_data ) ){
			return TRUE;
		}

		return false;
	}

	/**
	 * Get custom field group
	 *
	 * Custom FacetWP fields use the syntax "GROUP_fields/META_KEY" with "_fields" being the constant,
	 * and "GROUP" being the choice key. This method removes _fields to return the group the custom
	 * FacetWP field is associated with.
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $params
	 *
	 * @return string|bool
	 */
	function get_field_group( $params ){

		$choice_key = $this->get_choice_key( $params );
		$field_group = ! empty( $choice_key ) ? str_replace( '_fields', '', $choice_key ) : FALSE;

		return apply_filters( 'field_editor_facetwp_get_field_group', $field_group, $params );
	}

	/**
	 * Return supported post types
	 *
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	function get_post_types() {

		if ( empty( $this->post_types ) ) $this->post_types = apply_filters( 'field_editor_facetwp_post_types', array( 'job_listing', 'resume' ) );

		return (array) $this->post_types;
	}

	/**
	 * Get specific meta key configuration
	 *
	 *
	 * @since 1.6.0
	 *
	 * @param      $meta_key
	 * @param      $group
	 *
	 * @return bool|array   Returns array of field configuration, or false if none found
	 */
	function get_field( $meta_key, $group = null ){

		$field_data = $this->get_fields( $group );

		// Remove prepended underscore (only if it exists)
		$meta_key = ltrim( $meta_key, '_' );

		if( empty( $field_data ) ) {
			return FALSE;
		}

		if( $group === null || $group === 'cf' ){

			foreach( (array) $field_data as $field_group => $field_fields ) {
				if ( array_key_exists( $meta_key, $field_fields ) ) {
					return $field_fields[ $meta_key ];
				}
			}

		} elseif( array_key_exists( $meta_key, $field_data ) ){

			return $field_data[ $meta_key ];
		}

		// Meta key was specified, but no config found
		return FALSE;
	}

	/**
	 * Get/cache all WP Job Manager Fields
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param null $group
	 *
	 * @return array
	 */
	function get_fields( $group = NULL ) {

		// If group passed is 'cf', set to null to return all
		$group = $group === 'cf' ? null : $group;

		$fields     = WP_Job_Manager_Field_Editor_Fields::get_instance();
		$field_data = $fields->get_fields( $group );

		return $field_data;
	}

	/**
	 *  Force FacetWP Reindex on Field Config Update
	 *
	 *  Because we only call the update_post_meta() whenever updating field configuration,
	 *  we need to force FacetWP to reindex the post, as it only has hooks for insert and save post.
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $post_id
	 * @param $meta_key
	 * @param $field_type
	 * @param $action
	 * @param $old_meta
	 */
	function force_reindex( $post_id, $meta_key, $field_type, $action, $old_meta ){

		if( ! function_exists( 'FWP' ) || ! isset( FWP()->indexer ) || ! ( FWP()->indexer instanceof FacetWP_Indexer ) ) return;

		add_filter( 'facetwp_indexer_query_args', array( $this, 'reindex_post_types' ) );
		FWP()->indexer->index();
	}

	/**
	 * Set supported post types for FacetWP
	 *
	 * This method is called by the facetwp filter set in force_reindex to set the
	 * supported post types, to prevent forcing reindex on all post types.
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param $args
	 *
	 * @return array
	 */
	function reindex_post_types( $args ){
		$args['post_type'] = $this->get_post_types();
		return $args;
	}
}

if( class_exists( 'FacetWP' ) || ( function_exists( 'FWP' ) && FWP() instanceof FacetWP ) ) new WP_Job_Manager_Field_Editor_Plugins_FacetWP();

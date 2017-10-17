<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Admin_JS
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Admin_JS {

	private $hooks;

	/**
	 * WP_Job_Manager_Field_Editor_Admin_JS constructor.
	 */
	function __construct() {

		add_action( 'admin_enqueue_scripts', array($this, 'build_conf'), 100 );

	}

	/**
	 * Build All Configuration to Convert To JS
	 *
	 *
	 * @since 1.4.5
	 *
	 */
	function build_conf(){

		$conf = apply_filters( 'job_manager_field_editor_js_conf', array(
				'meta_keys'  => $this->meta_keys(),
				'outputs'    => $this->outputs(),
				'types'      => $this->types(),
				'checkbox'   => $this->checkbox(),
				'query_vars' => $this->query_vars()
			)
		);

		wp_localize_script( 'jmfe-scripts', 'jmfephpconf', $conf );
	}

	/**
	 * Return Meta Key Configuration
	 *
	 *
	 * @since 1.4.5
	 *
	 * @return mixed|void
	 */
	function meta_keys(){

		return apply_filters( 'job_manager_field_editor_js_conf_meta_keys', array(
				"resume_category"      => array(
					"type_disabled_by" => array('select'),
					"disable_types"    => array(
						'text',
						'email',
						'url',
						'tel',
						'textarea',
						'wp-editor',
						'select',
						'file',
						'password',
						'multiselect',
						'radio',
						'checkbox',
						'date',
						'phone',
						'header',
						'html',
						'actionhook',
						'number',
						'range'
					),
					"taxonomy"         => 'resume_category&post_type=resume',
					"not_required"     => array('options'),
				),
				"resume_skills"        => array(
					"type_disabled_by" => array('select'),
					"disable_types"    => array(
						'textarea',
						'wp-editor',
						'select',
						'file',
						'password',
						'multiselect',
						'radio',
						'checkbox',
						'date',
						'phone',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
					    'fpdate',
					    'fptime'
					),
					"taxonomy"         => 'resume_skill&post_type=resume',
					"not_required"     => array('options'),
				),
				"job_region"           => array(
					"type_disabled_by" => array('select'),
					"disable_types"    => array(
						'text',
						'email',
						'url',
						'tel',
						'textarea',
						'wp-editor',
						'select',
						'file',
						'password',
						'multiselect',
						'radio',
						'checkbox',
						'date',
						'phone',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
					"taxonomy"         => 'job_listing_region&post_type=job_listing',
					"not_required"     => array('options'),
					"disable_field_notice" => __( 'Make sure you disable the Regions plugin (if you are using it) to prevent issues when disabling this field.', 'wp-job-manager-field-editor' )
				),
				"job_category"         => array(
					"type_disabled_by" => array('select'),
					"disable_types"    => array(
						'text',
						'email',
						'url',
						'tel',
						'textarea',
						'wp-editor',
						'select',
						'file',
						'password',
						'job-category',
						'multiselect',
						'radio',
						'checkbox',
						'date',
						'phone',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
					"taxonomy"         => 'job_listing_category&post_type=job_listing',
					"not_required"     => array('options'),
				),
				"job_type"             => array(
					"type_disabled_by" => array('select'),
					"disable_types"    => array(
						'text',
						'email',
						'url',
						'tel',
						'textarea',
						'wp-editor',
						'select',
						'file',
						'password',
						'multiselect',
						'radio',
						'checkbox',
						'date',
						'phone',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
					"taxonomy"         => 'job_listing_type&post_type=job_listing',
					"not_required"     => array('options'),
				),
				"job_tags"             => array(
					"disable_types" => array(
						'textarea',
						'wp-editor',
						'select',
						'file',
						'password',
						'radio',
						'date',
						'phone',
						'checkbox',
						'multiselect',
						'term-select',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
					"taxonomy"      => 'job_listing_tag&post_type=job_listing',
				),
				"job_title"            => array(
					"disable_fields" => array('required_0', 'admin_only_0'),
					"disable_types"  => array(
						'wp-editor',
						'file',
						'password',
						'multiselect',
						'checkbox',
						'date',
						'phone',
						'term-checklist',
						'term-multiselect',
						'term-select',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
					"hidden_tabs"    => array('packages'),
					"disable_field_notice" => __( 'This field is used for the title of the listing, and can not be disabled, as it is required by the core WP Job Manager plugin to function correctly.', 'wp-job-manager-field-editor' )
				),
				"job_location" => array(
					"disable_types" => array(
						'wp-editor',
						'file',
						'password',
						'multiselect',
						'checkbox',
						'date',
						'phone',
						'term-checklist',
						'term-multiselect',
						'term-select',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
					"disable_field_notice" => __( 'Disabling this field will disable Geo location data, and could potentially cause problems, do so at your own risk!', 'wp-job-manager-field-editor' )
				),
				"job_description" => array(
					"disable_field_notice" => __( 'Disabling this field is NOT recommended as it is the main post content! You have been WARNED! DISABLE AT YOUR OWN RISK!', 'wp-job-manager-field-editor' )
				),
				"candidate_name"       => array(
					"disable_types"  => array(
						'wp-editor',
						'file',
						'password',
						'multiselect',
						'checkbox',
						'date',
						'phone',
						'term-checklist',
						'term-multiselect',
						'term-select',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
					"disable_fields" => array('required_0', 'admin_only_0'),
					"hidden_tabs"    => array('packages'),
					"disable_field_notice" => __( 'This field is used for the title of the listing, and can not be disabled, as it is required by the core WP Job Manager plugin to function correctly.', 'wp-job-manager-field-editor' )
				),
				"candidate_title"       => array(
					"disable_types"  => array(
						'wp-editor',
						'file',
						'password',
						'multiselect',
						'checkbox',
						'date',
						'phone',
						'term-checklist',
						'term-multiselect',
						'term-select',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
				),
				"candidate_location"       => array(
					"disable_types"  => array(
						'wp-editor',
						'file',
						'password',
						'multiselect',
						'checkbox',
						'date',
						'phone',
						'term-checklist',
						'term-multiselect',
						'term-select',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
				),
				"candidate_email"       => array(
					"disable_types"  => array(
						'wp-editor',
						'file',
						'password',
						'multiselect',
						'checkbox',
						'date',
						'phone',
						'term-checklist',
						'term-multiselect',
						'term-select',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
				),
				"allow_linkedin"       => array(
					"disable_types" => array(
						'textarea',
						'select',
						'radio',
						'wp-editor',
						'file',
						'password',
						'multiselect',
						'text',
						'email',
						'tel',
						'url',
						'date',
						'phone',
						'term-checklist',
						'term-multiselect',
						'term-select',
						'header',
						'html',
						'actionhook',
						'number',
						'range',
						'fpdate',
						'fptime'
					),
					"hidden_fields" => array('required', 'admin_only', 'placeholder'),
					"hidden_tabs"   => array('packages'),
				),
				"candidate_education"  => array("hidden_tabs" => array('populate'), "disable_fields" => array('meta_key')),
				"candidate_experience" => array("hidden_tabs" => array('populate'), "disable_fields" => array('meta_key')),
				"links"                => array("hidden_tabs" => array('populate'), "disable_fields" => array('meta_key')),
				"resume_file"          => array("disable_fields" => array('multiple_0')),
				"company_logo"         => array("disable_fields" => array('multiple_0')),
				"featured_image"       => array("disable_fields" => array('multiple_0', 'admin_only_0')),
				'application'          => array("disable_field_notice" => __( 'Disable this field and ... The APPLY NOW button will no longer show on listing, and the GeoMyWP plugin will not function correctly!', 'wp-job-manager-field-editor' ) ),
				'resume_content'       => array("disable_field_notice" => __( 'Disabling this field is NOT recommended as it is the main post content! You have been WARNED! DISABLE AT YOUR OWN RISK!', 'wp-job-manager-field-editor' ) )
			)
		);

	}

	/**
	 * Return WordPress Public Query Vars
	 *
	 *
	 * @since 1.4.5
	 *
	 * @return mixed|void
	 */
	function query_vars(){
		global $wp;

		if( isset( $wp, $wp->public_query_vars ) ){
			$query_vars = apply_filters( 'query_vars', $wp->public_query_vars );
		} else {
			$query_vars = array('m', 'p', 'posts', 'w', 'cat', 'withcomments', 'withoutcomments', 's', 'search', 'exact', 'sentence', 'calendar', 'page', 'paged', 'more', 'tb', 'pb', 'author', 'order', 'orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'tag', 'feed', 'author_name', 'static', 'pagename', 'page_id', 'error', 'attachment', 'attachment_id', 'subpost', 'subpost_id', 'preview', 'robots', 'taxonomy', 'term', 'cpage', 'post_type', 'embed' );
		}

		return apply_filters( 'job_manager_field_editor_js_conf_query_vars', $query_vars );
	}

	/**
	 * Return Checkbox Configuration
	 *
	 *
	 * @since 1.4.5
	 *
	 * @return mixed|void
	 */
	function checkbox(){

		return apply_filters( 'job_manager_field_editor_js_conf_checkbox', array(
				"checked"   => array(
					"output_enable_fw"  => array(
						"show" => array('output_full_wrap', 'output_fw_atts'),
						"hide" => array()
					),
					"output_enable_vw"  => array(
						"show" => array('output_value_wrap', 'output_vw_atts')
					),
					"output_show_label" => array(
						"show" => array('output_label_wrap', 'output_lw_atts')
					),
					"multiple" => array(
						"show" => array( 'max_uploads' )
					)
				),
				"unchecked" => array(
					"output_enable_fw"  => array(
						"hide" => array('output_full_wrap', 'output_fw_atts')
					),
					"output_enable_vw"  => array(
						"hide" => array('output_value_wrap', 'output_vw_atts')
					),
					"output_show_label" => array(
						"hide" => array('output_label_wrap', 'output_lw_atts')
					),
					"multiple" => array(
						"hide" => array( 'max_uploads' )
					)
				)
			)
		);

	}

	/**
	 * Return Outputs Configuration
	 *
	 *
	 * @since 1.4.5
	 *
	 * @return mixed|void
	 */
	function outputs(){

		return apply_filters( 'job_manager_field_editor_js_conf_outputs', array(
				"all" => array(
					"enable_fields"  => array(
						'output_classes',
						'output_show_label',
						'output_enable_fw',
						'output_enable_vw',
						'output_priority'
					),
					"disable_fields" => array(
						'output_caption',
						'output_oembed_height',
						'output_oembed_width',
						'output_check_true',
						'output_check_false',
						'output_video_allowdl',
						'output_video_poster',
						'output_video_height',
						'output_video_width',
						'image_link'
					)
				),
				"text"        => array(),
				"email"        => array(),
				"url"        => array(),
				"tel"        => array(),
				"link"        => array(
					"enable_fields" => array( 'output_caption' )
				),
				"image"       => array(
					"enable_fields" => array( 'image_link' )
				),
				"oembed"      => array(
					"enable_fields" => array(
						'output_oembed_height',
						'output_oembed_width'
					)
				),
				"checkcustom" => array(
					"enable_fields" => array(
						'output_check_true',
						'output_check_false'
					)
				),
				"video" => array(
					"enable_fields" => array(
						'output_video_allowdl',
						'output_video_poster',
						'output_video_height',
						'output_video_width'
					)
				)
			)
		);

	}

	/**
	 * Return Field Type Configurations
	 *
	 *
	 * @since 1.4.5
	 *
	 * @return mixed|void
	 */
	function types(){

		return apply_filters( 'job_manager_field_editor_js_conf_types', array(
				"text" => array(
					"show" => array( 'title', 'default', 'maxlength', 'pattern' )
				),
				"email" => array(
					"show" => array( 'title', 'default', 'maxlength', 'pattern' )
				),
				"url" => array(
					"show" => array( 'title', 'default', 'maxlength', 'pattern' )
				),
				"tel" => array(
					"show" => array( 'title', 'default', 'maxlength', 'pattern' )
				),
				"number" => array(
				  "show" => array(
				      'default',
				      'maxlength',
				      'min',
				      'max',
				      'size',
				      'pattern',
				      'step',
				      'title'
				  )
				),
				"range" => array(
				  "show"     => array('default', 'min', 'max', 'step', 'title', 'prepend', 'append'),
				  "hide"     => array('placeholder'),
				  "required" => array('max')
				),
				"password"         => array( "show" => array('maxlength') ),
				"textarea"         => array( "show" => array('maxlength') ),
				"file"             => array(
				  "show_tabs"       => array('options'),
				  "hide"            => array('placeholder'),
				  "show"            => array( 'multiple', 'ajax', 'max_upload_size' ),
				  "option_ph_label" => __( 'image/jpeg', 'wp-job-manager-field-editor' ),
				  "option_ph_value" => __( 'jpg', 'wp-job-manager-field-editor' ),
				  "option_label"    => __( 'Type', 'wp-job-manager-field-editor' ),
				  "option_value"    => __( 'Extension', 'wp-job-manager-field-editor' ),
				  "option_hide"     => array('option_default', 'option_disabled')
				),
				"select"           => array(
				  "show_tabs"       => array('options'),
				  "show"            => array('label_over_value'),
				  "hide"            => array('placeholder'),
				  "required"        => array('options'),
				  "option_ph_label" => __( 'Caption', 'wp-job-manager-field-editor' ),
				  "option_ph_value" => __( 'value', 'wp-job-manager-field-editor' ),
				  "option_label"    => __( 'Label', 'wp-job-manager-field-editor' ),
				  "option_value"    => __( 'Value', 'wp-job-manager-field-editor' )
				),
				"multiselect"      => array(
				  "show_tabs"       => array('options'),
				  "show"            => array( 'max_selected', 'label_over_value' ),
				  "hide"            => array(),
				  "required"        => array('options'),
				  "option_ph_label" => __( 'Caption', 'wp-job-manager-field-editor' ),
				  "option_ph_value" => __( 'value', 'wp-job-manager-field-editor' ),
				  "option_label"    => __( 'Label', 'wp-job-manager-field-editor' ),
				  "option_value"    => __( 'Value', 'wp-job-manager-field-editor' )
				),
				"radio"            => array(
				  "show_tabs"       => array('options'),
				  "hide"            => array('placeholder'),
				  'show'            => array( 'label_over_value' ),
				  "required"        => array('options'),
				  "option_ph_label" => __( 'Caption', 'wp-job-manager-field-editor' ),
				  "option_ph_value" => __( 'value', 'wp-job-manager-field-editor' ),
				  "option_label"    => __( 'Label', 'wp-job-manager-field-editor' ),
				  "option_value"    => __( 'Value', 'wp-job-manager-field-editor' )
				),
				"wp-editor"        => array("hide" => array('placeholder')),
				"term-checklist"   => array(
				  "show"     => array('taxonomy', 'default'),
				  "hide"     => array('placeholder'),
				  "required" => array('taxonomy')
				),
				"term-multiselect" => array(
					"show"     => array('taxonomy', 'default', 'max_selected'),
					"hide"     => array(),
					"required" => array('taxonomy')
				),
				"term-select" => array(
					"show"     => array('taxonomy', 'default'),
					"hide"     => array(),
					"required" => array('taxonomy')
				),
				"header" => array(
					'show'      => array('hide_in_admin'),
					"hide"      => array('placeholder', 'required', 'admin_only'),
					"hide_tabs" => array('output', 'populate')
				),
				"html" => array(
					'show'      => array( 'hide_in_admin' ),
					"hide"      => array('placeholder', 'required'),
					"hide_tabs" => array( 'output', 'populate' )
				),
				"actionhook" => array(
					'show'      => array('hide_in_admin'),
					"hide"      => array('placeholder', 'required'),
					"hide_tabs" => array('output', 'populate')
				),
				"checkbox" => array(
					"hide"      => array('placeholder')
				),
		        'fpdate' => array(
		        	'show' => array( 'picker_mode', 'picker_min_date', 'picker_max_date' ),
		        ),
		        'fptime' => array(
		        	'show' => array( 'picker_increment' ),
		        )
			)
		);

	}
}
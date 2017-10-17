<?php
/*
Plugin Name: WP Job Manager - Apply with Facebook
Plugin URI: https://wpjobmanager.com/add-ons/apply-with-facebook/
Description: Add an "Apply with Facebook" button to job listings which have an 'email' apply method. Requires an <a href="https://developers.facebook.com/quickstarts/?platform=web">App on Facebook</a> - please follow the <a href="https://wpjobmanager.com/document/apply-with-facebook/">documentation</a> to set it up correctly.
Version: 1.0.3
Author: Automattic
Author URI: https://wpjobmanager.com/
Requires at least: 3.8
Tested up to: 4.7

	Copyright: 2017 WP Job Manager
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPJM_Updater' ) ) {
	include( 'includes/updater/class-wpjm-updater.php' );
}

/**
 * WP_Job_Manager_Apply_With_Facebook class.
 */
class WP_Job_Manager_Apply_With_Facebook extends WPJM_Updater {

	private $error   = "";
	private $message = "";

	/**
	 * __construct function.
	 */
	public function __construct() {

		// Define constants
		define( 'JOB_MANAGER_APPLY_WITH_FACEBOOK_VERSION', '1.0.2' );
		define( 'JOB_MANAGER_APPLY_WITH_FACEBOOK_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'JOB_MANAGER_APPLY_WITH_FACEBOOK_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		if ( defined( 'JOB_MANAGER_APPLY_WITH_FACEBOOK_APP_ID' ) ) {
			define( 'JOB_MANAGER_APPLY_WITH_FACEBOOK_CONFIG_KEYS', true );
		} else {
			define( 'JOB_MANAGER_APPLY_WITH_FACEBOOK_CONFIG_KEYS', false );
			define( 'JOB_MANAGER_APPLY_WITH_FACEBOOK_APP_ID', get_option( 'job_manager_facebook_app_id' ) );
		}

		// Add actions
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'handle_http_post' ), 0 );
		add_filter( 'job_manager_settings', array( $this, 'settings' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'job_application_start', array( $this, 'apply_button' ) );
		add_action( 'job_application_end', array( $this, 'apply_content' ) );
		add_action( 'wp_job_manager_apply_with_facebook_application', array( $this, 'email_application' ), 10, 4 );

		if ( get_option( 'job_manager_allow_facebook_applications_field' ) == '1' ) {
			add_filter( 'submit_job_form_fields', array( $this, 'allow_facebook_field' ) );
			add_filter( 'job_manager_job_listing_data_fields', array( $this, 'allow_facebook_field_admin' ) );
		}

		// Init updates
		$this->init_updates( __FILE__ );
	}

	/**
	 * Enqueue scripts
	 */
	public function frontend_scripts() {
		wp_enqueue_style( 'wp-job-manager-apply-with-facebook-styles', JOB_MANAGER_APPLY_WITH_FACEBOOK_PLUGIN_URL . '/assets/css/frontend.css' );
		wp_register_script( 'wp-job-manager-apply-with-facebook-js', JOB_MANAGER_APPLY_WITH_FACEBOOK_PLUGIN_URL . '/assets/js/apply-with-facebook.js', array( 'jquery' ), JOB_MANAGER_APPLY_WITH_FACEBOOK_VERSION, true );
		wp_localize_script( 'wp-job-manager-apply-with-facebook-js', 'apply_with_facebook', array( 'appID' => esc_js( JOB_MANAGER_APPLY_WITH_FACEBOOK_APP_ID ) ) );
	}

	/**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-apply-with-facebook' );
		load_textdomain( 'wp-job-manager-apply-with-facebook', WP_LANG_DIR . "/wp-job-manager-apply-with-facebook/wp-job-manager-apply-with-facebook-$locale.mo" );
		load_plugin_textdomain( 'wp-job-manager-apply-with-facebook', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add Settings
	 * @param  array $settings
	 * @return array
	 */
	public function settings( $settings = array() ) {
		$settings['facebook'] = array(
			__( 'Apply with Facebook', 'wp-job-manager-apply-with-facebook' ),
			apply_filters(
				'wp_job_manager_apply_with_facebook_settings',
				array(
					'app_id' => array(
						'name' 		=> 'job_manager_facebook_app_id',
						'std' 		=> '',
						'label' 	=> __( 'App ID', 'wp-job-manager-apply-with-facebook' ),
						'desc'		=> sprintf( __( 'Get your App ID by creating a new application on %s. Skip the quick start.', 'wp-job-manager-apply-with-facebook' ), '<a href="https://developers.facebook.com/quickstarts/?platform=web" target="_blank">Facebook</a>' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_apply_with_facebook_cover_letter',
						'std' 		=> 'optional',
						'label' 	=> __( 'Cover letter field', 'wp-job-manager-apply-with-facebook' ),
						'desc'		=> '',
						'type'      => 'select',
						'options'   => array(
							'optional' => __( 'Optional', 'wp-job-manager-apply-with-facebook' ),
							'required' => __( 'Required', 'wp-job-manager-apply-with-facebook' ),
							'hidden'   => __( 'Hidden', 'wp-job-manager-apply-with-facebook' ),
						)
					),
					array(
						'name'		=> 'job_manager_allow_facebook_applications_field',
						'std'		=> '',
						'label'		=> __( 'Allow Facebook Field', 'wp-job-manager-apply-with-facebook' ),
						'cb_label'		=> __( 'Display Field', 'wp-job-manager-apply-with-facebook' ),
						'desc'		=> __( 'Add a field to the job submission form, allowing employers to enable/disable Facebook applications', 'wp-job-manager-apply-with-facebook' ),
						'type'		=> 'checkbox'
					),
				)
			)
		);

		if ( JOB_MANAGER_APPLY_WITH_FACEBOOK_CONFIG_KEYS ) {
			unset( $settings['facebook'][1]['app_id'] );
		}

		return $settings;
	}

	/**
	 * Add allow Facebook field to the frontend job submission form
	 */
	function allow_facebook_field( $fields ) {

	  $fields['job']['allow_facebook'] = array(
	    'label' => __( 'Allow Facebook Applications', 'wp-job-manager-apply-with-facebook' ),
	    'type' => 'checkbox',
	    'required' => false,
	    'placeholder' => '',
	    'priority' => 7,
	    'description' => __( 'Do you want to allow employees to apply with their Facebook profile?', 'wp-job-manager-apply-with-facebook' ),
	  );
	  return $fields;

	}

	/**
	 * Add allow Facebook field to the admin job listing meta
	 */
	function allow_facebook_field_admin( $fields ) {
	  $fields['_allow_facebook'] = array(
	    'label' => __( 'Allow Facebook Applications', 'wp-job-manager-apply-with-facebook' ),
	    'type' => 'checkbox',
	    'required' => false,
	    'placeholder' => '',
	    'description' => __( 'Do you want to allow employees to apply with their Facebook profile?', 'wp-job-manager-apply-with-facebook' ),
	  );
	  return $fields;
	}

	/**
	 * Allow application to a job?
	 * @return bool
	 */
	public function allow_application( $job_id ) {
		if ( get_option( 'job_manager_allow_facebook_applications_field' ) === '1' ) {
			return get_post_meta( $job_id, '_allow_facebook', true );
		} else {
			return true;
		}
	}

	/**
	 * Apply button
	 */
	public function apply_button( $apply ) {
		global $post;

		// Only add the apply button if 'allow Facebook applications' was checked
		if ( $this->allow_application( $post->ID ) ) {
			// For email based applications
			if ( isset( $apply->raw_email ) ) {
				$email = apply_filters( 'wp_job_manager_apply_with_facebook_email', $apply->raw_email, $post, $apply );
			} else {
				$email = apply_filters( 'wp_job_manager_apply_with_facebook_email', '', $post, $apply );
			}

			// Post application to URL (JSON)
			if ( apply_filters( 'wp_job_manager_apply_with_facebook_enable_http_post', false, $post, $apply ) ) {
				$url   = add_query_arg( array( 'job_id' => $post->ID, 'apply_with_facebook_application' => 1 ), home_url( '/' ) );
			} else {
				$url   = '';
			}

			if ( empty( $email ) && empty( $url ) ) {
				return;
			}

			// Output button template
			get_job_manager_template( 'apply-with-facebook.php', array(
				'company_name' => get_the_company_name(),
				'job_title'    => $post->post_title,
				'cover_letter' => get_option( 'job_manager_apply_with_facebook_cover_letter', 'optional' ),
				'job_id'       => $post->ID
			), 'wp-job-manager-apply-with-facebook', JOB_MANAGER_APPLY_WITH_FACEBOOK_PLUGIN_DIR . '/templates/' );
		}
	}

	/**
	 * Apply content
	 */
	public function apply_content( $apply ) {
		global $post;

		// Only add the apply button if 'allow Facebook applications' was checked
		if ( $this->allow_application( $post->ID ) ) {

			// For email based applications
			if ( isset( $apply->raw_email ) ) {
				$email = apply_filters( 'wp_job_manager_apply_with_facebook_email', $apply->raw_email, $post, $apply );
			} else {
				$email = apply_filters( 'wp_job_manager_apply_with_facebook_email', '', $post, $apply );
			}

			// Post application to URL (JSON)
			if ( apply_filters( 'wp_job_manager_apply_with_facebook_enable_http_post', false, $post, $apply ) ) {
				$url   = add_query_arg( array( 'job_id' => $post->ID, 'apply_with_facebook_application' => 1 ), home_url( '/' ) );
			} else {
				$url   = '';
			}

			if ( empty( $email ) && empty( $url ) ) {
				return;
			}

			// Output button template
			get_job_manager_template( 'apply-with-facebook-form.php', array(
				'company_name' => get_the_company_name(),
				'job_title'    => $post->post_title,
				'cover_letter' => get_option( 'job_manager_apply_with_facebook_cover_letter', 'optional' ),
				'job_id'       => $post->ID
			), 'wp-job-manager-apply-with-facebook', JOB_MANAGER_APPLY_WITH_FACEBOOK_PLUGIN_DIR . '/templates/' );
		}
	}

	/**
	 * Handle a posted application - fire off an action to be handled elsewhere.
	 */
	public function handle_http_post() {
		if ( ! empty( $_POST['apply-with-facebook-submit'] ) ) {
			$cover_letter = isset( $_POST['apply-with-facebook-cover-letter'] ) ? wp_kses_post( stripslashes( $_POST['apply-with-facebook-cover-letter'] ) ) : '';
			$profile_data = json_decode( stripslashes( $_POST['apply-with-facebook-profile-data'] ) );
			$profile_picture = sanitize_text_field( $_POST['apply-with-facebook-profile-picture'] );
			$job_id       = absint( $_POST['apply-with-facebook-job-id'] );

			if ( $job_id && 'job_listing' === get_post_type( $job_id ) && $profile_data ) {
				do_action( 'wp_job_manager_apply_with_facebook_application', $job_id, $profile_data, $profile_picture, $cover_letter );
				add_action( 'job_content_start', array( $this, 'apply_result' ) );
			}
		}
	}

	/**
	 * Email the employer a new facebook Application
	 */
	public function email_application( $job_id, $profile_data, $profile_picture, $cover_letter ) {
		$apply = get_the_job_application_method( $job_id );

		// For email based applications
		if ( isset( $apply->raw_email ) ) {
			$email = apply_filters( 'wp_job_manager_apply_with_facebook_email', $apply->raw_email, $job_id, $apply );
		} else {
			$email = apply_filters( 'wp_job_manager_apply_with_facebook_email', '', $job_id, $apply );
		}

		if ( !isset($profile_data->name ) ) {
			 $profile_data->name = $profile_data->first_name . ' ' . $profile_data->last_name;
		}

		if ( !isset( $profile_data->bio ) ) {
			 $profile_data->bio = $profile_data->about;
		}

		if ( is_email( $email ) ) {
			$subject = sprintf( _x( '%s - %s has submitted an application', 'Job - Name has submitted an application', 'wp-job-manager-apply-with-facebook' ), get_the_title( $job_id ), $profile_data->name );

			ob_start();

			get_job_manager_template( 'apply-with-facebook-email.php', array(
				'company_name' => get_the_company_name(  $job_id ),
				'job_title'    => get_the_title( $job_id ),
				'cover_letter' => $cover_letter,
				'job_id'       => $job_id,
				'profile_data' => $profile_data,
				'profile_picture' => $profile_picture,
			), 'wp-job-manager-apply-with-facebook', JOB_MANAGER_APPLY_WITH_FACEBOOK_PLUGIN_DIR . '/templates/' );

			$content   = ob_get_clean();
			$headers   = array();
			$headers[] = "Reply-To: " . esc_attr( $profile_data->name ) . " <" . esc_attr( $profile_data->email ) . ">";
			$headers[] = "Content-type: text/html";

			wp_mail( $email, $subject, $content, $headers );
		}

		$this->message = __( 'Your job application has been submitted successfully', 'wp-job-manager-apply-with-facebook' );
	}

	/**
	 * Show results - errors and messages
	 */
	public function apply_result() {
		if ( $this->message ) {
			echo '<p class="job-manager-message">' . esc_html( $this->message ) . '</p>';
		} elseif ( $this->error ) {
			echo '<p class="job-manager-error">' . esc_html( $this->error ) . '</p>';
		}
	}
}
$GLOBALS['wp-job-manager-apply-with-facebook'] = new WP_Job_Manager_Apply_With_Facebook();

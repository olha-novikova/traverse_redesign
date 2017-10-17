<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Field_Editor_reCAPTCHA {

	/**
	 * WP_Job_Manager_Field_Editor_reCAPTCHA constructor.
	 */
	public function __construct() {

		if( self::is_enabled() ){
			$recaptcha_action = get_option( 'jmfe_recaptcha_output_after_job' ) ? 'submit_job_form_job_fields_end' : 'submit_job_form_company_fields_end';
			add_action( $recaptcha_action, array( $this, 'output' ) );
			add_filter( 'submit_job_form_validate_fields', array($this, 'validate') );
		}

		if( self::is_enabled( 'resume' ) ){
			add_action( 'submit_resume_form_resume_fields_end', array($this, 'output') );
			add_filter( 'submit_resume_form_validate_fields', array($this, 'validate') );
		}

	}

	/**
	 * Output reCAPTCHA field on submit page
	 *
	 *
	 * @since 1.3.5
	 *
	 */
	function output(){
		$label_option = get_option( 'jmfe_recaptcha_label' );
		$label = $label_option ? $label_option : __( "Are you human?", 'wp-job-manager-field-editor' );
		?>
		<fieldset>
			<label><?php _e( $label, 'wp-job-manager-field-editor' ); ?></label>
			<div class="field">
				<div class="g-recaptcha" data-sitekey="<?php echo get_option( 'jmfe_recaptcha_site_key' ); ?>" data-theme="<?php echo get_option( 'jmfe_recaptcha_theme', 'light' ); ?>" data-size="<?php echo get_option( 'jmfe_recaptcha_size', 'normal' ); ?>" data-type="<?php echo get_option( 'jmfe_recaptcha_type', 'image' ); ?>"></div>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Validate reCAPTCHA field
	 *
	 *
	 * @since 1.3.5
	 *
	 * @param $success
	 *
	 * @return \WP_Error
	 */
	function validate( $success ){

		$response = wp_remote_get( add_query_arg( array(
			                                          'secret'   => get_option( 'jmfe_recaptcha_secret_key' ),
			                                          'response' => isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '',
			                                          'remoteip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
		                                          ), 'https://www.google.com/recaptcha/api/siteverify' ) );

		if( is_wp_error( $response ) || empty($response['body']) || ! ($json = json_decode( $response['body'] )) || ! $json->success ) {
			$label_option = get_option( 'jmfe_recaptcha_label' );
			$label        = $label_option ? $label_option : __( "Are you human?", 'wp-job-manager-field-editor' );
			return new WP_Error( 'validation-error', sprintf( __('"%s" check failed. Please try again.', 'wp-job-manager-field-editor'), $label ) );
		}

		return $success;

	}

	/**
	 * Check if Site/Secret Key and Listing Type is Set/Enabled
	 *
	 *
	 * @since 1.3.5
	 *
	 * @param string $for
	 *
	 * @return bool
	 */
	public static function is_enabled( $for = 'job' ) {

		$is_enabled = get_option( "jmfe_recaptcha_enable_{$for}" );
		if( empty($is_enabled) ) return FALSE;

		$site_key   = get_option( 'jmfe_recaptcha_site_key' );
		$secret_key = get_option( 'jmfe_recaptcha_secret_key' );

		// If missing site or secret key return false
		if( empty($site_key) || empty($secret_key) ) return FALSE;

		return TRUE;
	}

	/**
	 * Get Google reCAPTCHA supported language codes
	 *
	 * @see   https://developers.google.com/recaptcha/docs/language
	 *
	 * @since 1.5.0
	 *
	 * @param bool $inc_get_locale
	 *
	 * @return array
	 */
	public static function language_codes( $inc_get_locale = FALSE ){

		$codes = array(
			'get_locale' => __( 'Auto (based on WordPress get_locale)', 'wp-job-manager-field-editor' ),
			'ar'     => __( 'Arabic', 'wp-job-manager-field-editor' ),
			'af'     => __( 'Afrikaans', 'wp-job-manager-field-editor' ),
			'am'     => __( 'Amharic', 'wp-job-manager-field-editor' ),
			'hy'     => __( 'Armenian', 'wp-job-manager-field-editor' ),
			'az'     => __( 'Azerbaijani', 'wp-job-manager-field-editor' ),
			'eu'     => __( 'Basque', 'wp-job-manager-field-editor' ),
			'bn'     => __( 'Bengali', 'wp-job-manager-field-editor' ),
			'bg'     => __( 'Bulgarian', 'wp-job-manager-field-editor' ),
			'ca'     => __( 'Catalan', 'wp-job-manager-field-editor' ),
			'zh-HK'  => __( 'Chinese (Hong Kong)', 'wp-job-manager-field-editor' ),
			'zh-CN'  => __( 'Chinese (Simplified)', 'wp-job-manager-field-editor' ),
			'zh-TW'  => __( 'Chinese (Traditional)', 'wp-job-manager-field-editor' ),
			'hr'     => __( 'Croatian', 'wp-job-manager-field-editor' ),
			'cs'     => __( 'Czech', 'wp-job-manager-field-editor' ),
			'da'     => __( 'Danish', 'wp-job-manager-field-editor' ),
			'nl'     => __( 'Dutch', 'wp-job-manager-field-editor' ),
			'en-GB'  => __( 'English (UK)', 'wp-job-manager-field-editor' ),
			'en'     => __( 'English (US)', 'wp-job-manager-field-editor' ),
			'et'     => __( 'Estonian', 'wp-job-manager-field-editor' ),
			'fil'    => __( 'Filipino', 'wp-job-manager-field-editor' ),
			'fi'     => __( 'Finnish', 'wp-job-manager-field-editor' ),
			'fr'     => __( 'French', 'wp-job-manager-field-editor' ),
			'fr-CA'  => __( 'French (Canadian)', 'wp-job-manager-field-editor' ),
			'gl'     => __( 'Galician', 'wp-job-manager-field-editor' ),
			'ka'     => __( 'Georgian', 'wp-job-manager-field-editor' ),
			'de'     => __( 'German', 'wp-job-manager-field-editor' ),
			'de-AT'  => __( 'German (Austria)', 'wp-job-manager-field-editor' ),
			'de-CH'  => __( 'German (Switzerland)', 'wp-job-manager-field-editor' ),
			'el'     => __( 'Greek', 'wp-job-manager-field-editor' ),
			'gu'     => __( 'Gujarati', 'wp-job-manager-field-editor' ),
			'iw'     => __( 'Hebrew', 'wp-job-manager-field-editor' ),
			'hi'     => __( 'Hindi', 'wp-job-manager-field-editor' ),
			'hu'     => __( 'Hungarain', 'wp-job-manager-field-editor' ),
			'is'     => __( 'Icelandic', 'wp-job-manager-field-editor' ),
			'id'     => __( 'Indonesian', 'wp-job-manager-field-editor' ),
			'it'     => __( 'Italian', 'wp-job-manager-field-editor' ),
			'ja'     => __( 'Japanese', 'wp-job-manager-field-editor' ),
			'kn'     => __( 'Kannada', 'wp-job-manager-field-editor' ),
			'ko'     => __( 'Korean', 'wp-job-manager-field-editor' ),
			'lo'     => __( 'Laothian', 'wp-job-manager-field-editor' ),
			'lv'     => __( 'Latvian', 'wp-job-manager-field-editor' ),
			'lt'     => __( 'Lithuanian', 'wp-job-manager-field-editor' ),
			'ms'     => __( 'Malay', 'wp-job-manager-field-editor' ),
			'ml'     => __( 'Malayalam', 'wp-job-manager-field-editor' ),
			'mr'     => __( 'Marathi', 'wp-job-manager-field-editor' ),
			'mn'     => __( 'Mongolian', 'wp-job-manager-field-editor' ),
			'no'     => __( 'Norwegian', 'wp-job-manager-field-editor' ),
			'fa'     => __( 'Persian', 'wp-job-manager-field-editor' ),
			'pl'     => __( 'Polish', 'wp-job-manager-field-editor' ),
			'pt'     => __( 'Portuguese', 'wp-job-manager-field-editor' ),
			'pt-BR'  => __( 'Portuguese (Brazil)', 'wp-job-manager-field-editor' ),
			'pt-PT'  => __( 'Portuguese (Portugal)', 'wp-job-manager-field-editor' ),
			'ro'     => __( 'Romanian', 'wp-job-manager-field-editor' ),
			'ru'     => __( 'Russian', 'wp-job-manager-field-editor' ),
			'sr'     => __( 'Serbian', 'wp-job-manager-field-editor' ),
			'si'     => __( 'Sinhalese', 'wp-job-manager-field-editor' ),
			'sk'     => __( 'Slovak', 'wp-job-manager-field-editor' ),
			'sl'     => __( 'Slovenian', 'wp-job-manager-field-editor' ),
			'es'     => __( 'Spanish', 'wp-job-manager-field-editor' ),
			'es-419' => __( 'Spanish (Latin America)', 'wp-job-manager-field-editor' ),
			'sw'     => __( 'Swahili', 'wp-job-manager-field-editor' ),
			'sv'     => __( 'Swedish', 'wp-job-manager-field-editor' ),
			'ta'     => __( 'Tamil', 'wp-job-manager-field-editor' ),
			'te'     => __( 'Telugu', 'wp-job-manager-field-editor' ),
			'th'     => __( 'Thai', 'wp-job-manager-field-editor' ),
			'tr'     => __( 'Turkish', 'wp-job-manager-field-editor' ),
			'uk'     => __( 'Ukrainian', 'wp-job-manager-field-editor' ),
			'ur'     => __( 'Urdu', 'wp-job-manager-field-editor' ),
			'vi'     => __( 'Vietnamese', 'wp-job-manager-field-editor' ),
			'zu'     => __( 'Zulu', 'wp-job-manager-field-editor' )
		);

		if( ! $inc_get_locale ) unset( $codes['get_locale'] );

		return apply_filters( 'field_editor_recaptcha_language_codes', $codes );
	}

	/**
	 * Get supported language code, converted from get_locale()
	 *
	 * This method will attempt to use the core WordPress get_locale() and if that code is not found
	 * in the supported language codes, it will attempt to strip everything after (and including),
	 * an underscore (_), or hyphen (-), to try to return a general language code.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $err_over_false
	 *
	 * @return bool|string
	 */
	public static function get_locale_code( $err_over_false = FALSE ){

		$supported = self::language_codes();
		$current_locale = get_locale();
		$return_locale = $err_over_false ? __( 'Error converting get_locale()!', 'wp-job-manager-field-editor' ) : FALSE;

		/**
		 * Convert underscores to hyphen (Google uses hyphens, WordPress uses underscores)
		 */
		if ( strpos( $current_locale, '_' ) !== FALSE ) {
			$current_locale = str_replace( '_', '-', $current_locale );
		}

		if( array_key_exists( $current_locale, $supported ) ){
			$return_locale = $current_locale;
		} else {

			if( strpos( $current_locale, '_' ) !== FALSE ){
				$general_locale = substr( $current_locale, 0, strpos( $current_locale, '_' ) );
			} elseif( strpos( $current_locale, '-' ) !== FALSE ){
				$general_locale = substr( $current_locale, 0, strpos( $current_locale, '-' ) );
			}

			if( array_key_exists( $general_locale, $supported ) ){
				$return_locale = $general_locale;
			}

		}

		return apply_filters( 'field_editor_recaptcha_get_locale_code', $return_locale, $current_locale, $supported );
	}
}
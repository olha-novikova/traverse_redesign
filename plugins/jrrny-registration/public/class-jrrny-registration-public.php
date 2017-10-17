<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       olha.novikova@gmail.com
 * @since      1.0.0
 *
 * @package    Jrrny_Registration
 * @subpackage Jrrny_Registration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Jrrny_Registration
 * @subpackage Jrrny_Registration/public
 * @author     Olha Novikova <olha.novikova@gmail.com>
 */
class Jrrny_Registration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

      //  add_action( 'woocommerce_created_customer', array( $this, 'jrrny_registration'), 10 , 3);
	}

    function jrrny_registration( $customer_id, $new_customer_data, $password_generated ) {

        if ( isset($new_customer_data['user_login']) ) {
            $username = $new_customer_data['user_login'];
        }
        else {
            $username = '';
        }

        if ( isset($new_customer_data['user_email']) ) {
            $email = $new_customer_data['user_email'];
        }
        else {
            $email = '';
        }

        if ( isset($new_customer_data['user_pass']) ) {
            $password = $new_customer_data['user_pass'];
        }
        else {
            $password = '';
        }

        $opt_name = 'jrrny_api_path';

        $opt_val = get_option( $opt_name );

        $request = wp_remote_get( $opt_val.'/get_nonce/?controller=user&method=register' );

        $add_body = false;

        $response = '';

        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ){

            $body = json_decode( wp_remote_retrieve_body( $request ) );

            $nonce = $body->nonce;

            if ( $nonce ){

                $add_request = wp_remote_get( $opt_val.'/user/register?username='.$username.'&email='.$email.'&nonce='.$nonce.'&display_name='.$username.'&insecure=cool&user_pass='.$password."&notify=no" );

                if ( ! is_wp_error( $add_request ) || wp_remote_retrieve_response_code( $add_request ) === 200 ){

                    $add_body = json_decode( wp_remote_retrieve_body( $add_request ) );

                    if ( $add_body->status != 'error'){

                        $this->jrrry_user_notification($customer_id, $password);

                        $response = 'http://jrrny.com/author/'.$username;

                        update_user_meta($customer_id,'_jrrny_link', $response);

                    }elseif ( $add_body->error=='E-mail address is already in use.'){

                        $response = 'error';
                    }

                }else
                    return false;

            }else
                return false;

        }else
            return false;

        return $response;
    }


    function jrrry_user_notification( $user_id, $plaintext_pass ) {
        $user = new WP_User($user_id);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $user_email = stripslashes($user->user_email);

        $jrrny_url = "<a href = \"http://jrrny.com/\">Jrrny.com</a>";

        $message   = '<div style="padding: 20px;">';

        $message  .= '<h1 style="margin: 20px 0; padding:20px; background: #05a4e6; color:#fff; ">'.__('Hi there,') . '</h1>';

        $message .= '<h3 style="padding:20px;">'.__("Thank you for joining ").get_option('blogname').__(". You must have an account jrrny.com to join Traverse; but don’t worry - we’ve done the legwork for you! See your login details, below. ").'</h3>';

        $message .= '<p style=" padding:20px;">';

        $message .= '<strong>'.sprintf(__('Go to: %s'), $jrrny_url) . '</strong><br>';

        $message .= '<strong>'.sprintf(__('Username: %s'), $user_email) . '</strong><br>';

        $message .= '<strong>'.sprintf(__('Password: same as on %s'),  home_url() ) . '</strong><br><br>';

        $message .= sprintf(__('If you have any problems, please contact me at %s.'), 'admin@jrrny.com') . '</p>';

        $message .= '<p style="text-align:center; padding:20px 0; background: #05a4e6; color:#fff; ">'.sprintf(__('Good luck! Jrrny — Share Your Jrrny! <br> %s'),get_option('blogname') ).'</p><br>';

        $message .= '</div>';

        wp_mail($user_email, __('Account on Jrrny — Share Your Jrrny'), $message, $headers);

    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Jrrny_Registration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Jrrny_Registration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jrrny-registration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Jrrny_Registration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Jrrny_Registration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jrrny-registration-public.js', array( 'jquery' ), $this->version, false );

	}

}

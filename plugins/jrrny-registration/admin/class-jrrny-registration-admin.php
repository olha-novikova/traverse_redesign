<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       olha.novikova@gmail.com
 * @since      1.0.0
 *
 * @package    Jrrny_Registration
 * @subpackage Jrrny_Registration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Jrrny_Registration
 * @subpackage Jrrny_Registration/admin
 * @author     Olha Novikova <olha.novikova@gmail.com>
 */
class Jrrny_Registration_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_action('admin_menu',  array( $this, 'jrrny_setup_settings') );

	}

    function jrrny_setup_settings() {

        add_options_page(__('JRRNY Registration Settings','menu-test'), __('JRRNY Registration Settings','menu-test'), 'manage_options', 'jrrnysettings', array( $this, 'jrrny_settings_page' ));

        //call register settings function
        add_action( 'admin_init', array( $this, 'jrrny_register_settings'));
    }

    function jrrny_register_settings() {
        //register our settings
        register_setting( 'jrrny-settings-group', 'jrrny_api_path' );
    }

    function jrrny_settings_page() {

        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        // variables for the field and option names
        $opt_name = 'jrrny_api_path';
        $hidden_field_name = 'jrrny_api_path_hidden';
        $data_field_name = 'jrrny_api_path';

        // Read in existing option value from database
        $opt_val = get_option( $opt_name );

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
            // Read their posted value
            $opt_val = $_POST[ $data_field_name ];

            // Save the posted value in the database
            update_option( $opt_name, $opt_val );

            // Put a "settings saved" message on the screen

            ?>
            <div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
        <?php

        }

        // Now display the settings editing screen

        echo '<div class="wrap">';

        // header

        echo "<h2>" . __( 'JRRNY Registration Settings', 'menu-test' ) . "</h2>";

        // settings form

        ?>

        <form name="form1" method="post" action="">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

            <p><?php _e("Jrrny api path:", 'menu-test' ); ?>
                <input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="100">
            </p><hr />

            <p class="submit">
                <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
            </p>

        </form>
        </div>

    <?php

    }
	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jrrny-registration-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jrrny-registration-admin.js', array( 'jquery' ), $this->version, false );

	}

}
<?php

class AcceptStripePayments_Admin {

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct() {

        /*
         * Call $plugin_slug from public plugin class.
         */
        $plugin = AcceptStripePayments::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        // Load admin style sheet and JavaScript.
        // add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        // add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        // Add the options page and menu item.
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename(plugin_dir_path(realpath(dirname(__FILE__))) . $this->plugin_slug . '.php');
        add_filter('plugin_action_links_' . $plugin_basename, array($this, 'add_action_links'));

        // register custom post type
        $ASPOrder = ASPOrder::get_instance();
        add_action('init', array($ASPOrder, 'register_post_type'), 0);
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_styles() {

        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();
        if ($this->plugin_screen_hook_suffix == $screen->id) {
            wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), array(), AcceptStripePayments::VERSION);
        }
    }

    /**
     * Register and enqueue admin-specific JavaScript.
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_scripts() {

        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();
        if ($this->plugin_screen_hook_suffix == $screen->id) {
            wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), AcceptStripePayments::VERSION);
        }
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         * @TODO:
         *
         * - Change 'Page Title' to the title of your plugin admin page
         * - Change 'Menu Text' to the text for menu item for the plugin settings page
         * - Change 'manage_options' to the capability you see fit
         *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
         */
        $this->plugin_screen_hook_suffix = add_options_page(
                __('Accept Stripe Payments', 'stripe-payments'), __('Accept Stripe Payments', 'stripe-payments'), 'manage_options', $this->plugin_slug, array($this, 'display_plugin_admin_page')
        );
        add_action('admin_init', array(&$this, 'register_settings'));
    }

    /**
     * Register Admin page settings
     *
     * @since    1.0.0
     */
    public function register_settings($value = '') {
        register_setting('AcceptStripePayments-settings-group', 'AcceptStripePayments-settings', array(&$this, 'settings_sanitize_field_callback'));

        add_settings_section('AcceptStripePayments-documentation', 'Plugin Documentation', array(&$this, 'general_documentation_callback'), $this->plugin_slug);

        add_settings_section('AcceptStripePayments-global-section', 'Global Settings', null, $this->plugin_slug);
        add_settings_section('AcceptStripePayments-credentials-section', 'Credentials', null, $this->plugin_slug);

        add_settings_field('checkout_url', 'Checkout Result Page URL', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-global-section', array('field' => 'checkout_url', 'desc' => 'This page is automatically created for you when you install the plugin. Do not delete this page as the plugin will send the customer to this page after the payment.', 'size' => 100));
        add_settings_field('currency_code', 'Currency Code', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-global-section', array('field' => 'currency_code', 'desc' => 'Example: USD, CAD etc', 'size' => 10));
        add_settings_field('button_text', 'Button Text', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-global-section', array('field' => 'button_text', 'desc' => 'Example: Buy Now, Pay Now etc.'));
        add_settings_field('use_new_button_method', 'Use New Method To Display Buttons', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-global-section', array('field' => 'use_new_button_method',
            'desc' => 'Use new method to display Stripe buttons. It makes connection to Stripe website only when button is clicked, which makes the page with buttons load faster. A little drawback is that Stripe pop-up is displayed with a small delay after button click. If you have more than one button on a page, enabling this option is highly recommended.'));

        add_settings_field('is_live', 'Live Mode', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-credentials-section', array('field' => 'is_live', 'desc' => 'Check this to run the transaction in live mode. When unchecked it will run in test mode.'));
        add_settings_field('api_publishable_key', 'Stripe Publishable Key', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-credentials-section', array('field' => 'api_publishable_key', 'desc' => ''));
        add_settings_field('api_secret_key', 'Stripe Secret Key', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-credentials-section', array('field' => 'api_secret_key', 'desc' => ''));

        add_settings_section('AcceptStripePayments-email-section', 'Email Settings', null, $this->plugin_slug);
        add_settings_field('send_emails_to_buyer', 'Send Emails to Buyer After Purchase', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-email-section', array('field' => 'send_emails_to_buyer',
            'desc' => 'If checked the plugin will send an email to the buyer with the sale details. If digital goods are purchased then the email will contain the download links for the purchased products.')
        );
        add_settings_field('from_email_address', 'From Email Address', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-email-section', array('field' => 'from_email_address',
            'desc' => 'Example: Your Name &lt;sales@your-domain.com&gt; This is the email address that will be used to send the email to the buyer. This name and email address will appear in the from field of the email.')
        );
        add_settings_field('buyer_email_subject', 'Buyer Email Subject', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-email-section', array('field' => 'buyer_email_subject',
            'desc' => 'This is the subject of the email that will be sent to the buyer.')
        );
        add_settings_field('buyer_email_body', 'Buyer Email Body', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-email-section', array('field' => 'buyer_email_body',
            'desc' => 'This is the body of the email that will be sent to the buyer. Do not change the text within the braces {}. You can use the following email tags in this email body field:
                <br>{payer_email} – Email Address of the buyer
                <br>{shipping_address} – Shipping address of the buyer     
                <br>{billing_address} – Billing address of the buyer     
                <br>{product_details} – The item details of the purchased product (this will include the download link for digital items).   
                <br>{transaction_id} – The unique transaction ID of the purchase 
                <br>{purchase_amt} – The amount paid for the current transaction
                <br>{purchase_date} – The date of the purchase')
        );
        add_settings_field('send_emails_to_seller', 'Send Emails to Seller After Purchase', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-email-section', array('field' => 'send_emails_to_seller',
            'desc' => 'If checked the plugin will send an email to the seller with the sale details.')
        );
        add_settings_field('seller_notification_email', 'Notification Email Address', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-email-section', array('field' => 'seller_notification_email',
            'desc' => 'This is the email address where the seller will be notified of product sales. You can put multiple email addresses separated by comma (,) in the above field to send the notification to multiple email addresses.')
        );
        add_settings_field('seller_email_subject', 'Seller Email Subject', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-email-section', array('field' => 'seller_email_subject',
            'desc' => 'This is the subject of the email that will be sent to the seller for record.')
        );
        add_settings_field('seller_email_body', 'Seller Email Body', array(&$this, 'settings_field_callback'), $this->plugin_slug, 'AcceptStripePayments-email-section', array('field' => 'seller_email_body',
            'desc' => 'This is the body of the email that will be sent to the seller. Do not change the text within the braces {}. You can use the following email tags in this email body field:
                <br>{payer_email} – Email Address of the buyer
                <br>{shipping_address} – Shipping address of the buyer     
                <br>{billing_address} – Billing address of the buyer     
                <br>{product_details} – The item details of the purchased product (this will include the download link for digital items).   
                <br>{transaction_id} – The unique transaction ID of the purchase 
                <br>{purchase_amt} – The amount paid for the current transaction
                <br>{purchase_date} – The date of the purchase')
        );

        add_settings_section('AcceptStripePayments-settings-footer', '', array(&$this, 'general_settings_menu_footer_callback'), $this->plugin_slug);
    }

    public function general_documentation_callback($args) {
        ?>
        <div style="background: none repeat scroll 0 0 #FFF6D5;border: 1px solid #D1B655;color: #3F2502;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">
            <p>Please read the
                <a target="_blank" href="https://www.tipsandtricks-hq.com/ecommerce/wordpress-stripe-plugin-accept-payments-using-stripe">WordPress Stripe</a> plugin setup instructions to configure and use it.
            </p>
        </div>
        <?php
    }

    public function general_settings_menu_footer_callback($args) {
        ?>
        <div style="background: none repeat scroll 0 0 #FFF6D5;border: 1px solid #D1B655;color: #3F2502;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">
            <p>
                If you need a feature rich and supported plugin for selling your products and services then check out our 
                <a target="_blank" href="https://www.tipsandtricks-hq.com/wordpress-estore-plugin-complete-solution-to-sell-digital-products-from-your-wordpress-blog-securely-1059">WP eStore Plugin</a>.
            </p>
        </div>
        <?php
    }

    /**
     * Settings HTML
     *
     * @since    1.0.0
     */
    public function settings_field_callback($args) {
        $settings = (array) get_option('AcceptStripePayments-settings');

        extract($args);

        $field_value = esc_attr(isset($settings[$field]) ? $settings[$field] : '');

        if (empty($size))
            $size = 40;

        switch ($field) {
            case 'send_emails_to_seller':
            case 'send_emails_to_buyer':
            case 'use_new_button_method':
            case 'is_live':
                echo "<input type='checkbox' name='AcceptStripePayments-settings[{$field}]' value='1' " . ($field_value ? 'checked=checked' : '') . " /><p class=\"description\">{$desc}</p>";
                break;
            case 'buyer_email_body':
            case 'seller_email_body':
                echo "<textarea cols='70' rows='7' name='AcceptStripePayments-settings[{$field}]'>{$field_value}</textarea><p class=\"description\">{$desc}</p>";
                break;
            default:
                // case 'currency_code':
                // case 'button_text':
                // case 'api_username':
                // case 'api_password':
                // case 'api_signature':
                echo "<input type='text' name='AcceptStripePayments-settings[{$field}]' value='{$field_value}' size='{$size}' /> <p class=\"description\">{$desc}</p>";
                break;
        }
    }

    /**
     * Validates the admin data
     *
     * @since    1.0.0
     */
    public function settings_sanitize_field_callback($input) {
        $output = get_option('AcceptStripePayments-settings');

        if (empty($input['is_live']))
            $output['is_live'] = 0;
        else
            $output['is_live'] = 1;

        if (empty($input['api_secret_key']) || empty($input['api_publishable_key'])) {
            add_settings_error('AcceptStripePayments-settings', 'invalid-credentials', 'You must fill all API credentials for plugin to work correctly.');
        }

        if (!empty($input['checkout_url']))
            $output['checkout_url'] = $input['checkout_url'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-checkout_url', 'Please specify a checkout page.');

        if (!empty($input['button_text']))
            $output['button_text'] = $input['button_text'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-button-text', 'Button text should not be empty.');

        if (empty($input['use_new_button_method']))
            $output['use_new_button_method'] = 0;
        else
            $output['use_new_button_method'] = 1;        
        
        if (!empty($input['currency_code']))
            $output['currency_code'] = $input['currency_code'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-currency-code', 'You must specify payment currency.');

        if (!empty($input['api_publishable_key']))
            $output['api_publishable_key'] = $input['api_publishable_key'];

        if (!empty($input['api_secret_key']))
            $output['api_secret_key'] = $input['api_secret_key'];

        if (empty($input['send_emails_to_buyer']))
            $output['send_emails_to_buyer'] = 0;
        else
            $output['send_emails_to_buyer'] = 1;

        if (!empty($input['from_email_address']))
            $output['from_email_address'] = $input['from_email_address'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-from-email-address', 'You must specify from email address.');

        if (!empty($input['buyer_email_subject']))
            $output['buyer_email_subject'] = $input['buyer_email_subject'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-buyer-email-subject', 'You must specify buyer email subject.');

        if (!empty($input['buyer_email_body']))
            $output['buyer_email_body'] = $input['buyer_email_body'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-buyer-email-body', 'You must fill in buyer email body.');

        if (empty($input['send_emails_to_seller']))
            $output['send_emails_to_seller'] = 0;
        else
            $output['send_emails_to_seller'] = 1;

        if (!empty($input['seller_notification_email']))
            $output['seller_notification_email'] = $input['seller_notification_email'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-seller-notification-email', 'You must specify seller notification email address.');

        if (!empty($input['seller_email_subject']))
            $output['seller_email_subject'] = $input['seller_email_subject'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-seller-email-subject', 'You must specify seller email subject.');

        if (!empty($input['seller_email_body']))
            $output['seller_email_body'] = $input['seller_email_body'];
        else
            add_settings_error('AcceptStripePayments-settings', 'invalid-seller-email-body', 'You must fill in seller email body.');

        return $output;
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() {
        include_once( 'views/admin.php' );
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links) {

        return array_merge(
                array(
            'settings' => '<a href="' . admin_url('options-general.php?page=' . $this->plugin_slug) . '">' . __('Settings', 'stripe-payments') . '</a>'
                ), $links
        );
    }

}

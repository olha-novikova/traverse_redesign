<?php
/**
 * @package iflychat
 * @version 4.2.7
 */
/*
Plugin Name: iFlyChat
Plugin URI: http://wordpress.org/extend/plugins/iflychat/
Description: One on one chat, Multiple chatrooms, Embedded chatrooms
Author: iFlyChat Team
Version: 4.2.7
Author URI: https://iflychat.com/
*/

if (!function_exists('is_plugin_active_for_network')) {
    require_once(ABSPATH . '/wp-admin/includes/plugin.php');
}
if(session_id() == ''){
    session_start();
}

if(!defined('DRUPALCHAT_EXTERNAL_HOST')){
  define('DRUPALCHAT_EXTERNAL_HOST', 'http://api5.iflychat.com');
}

if(!defined('DRUPALCHAT_EXTERNAL_PORT')){
  define('DRUPALCHAT_EXTERNAL_PORT', '80');
}

if(!defined('DRUPALCHAT_EXTERNAL_A_HOST')){
  define('DRUPALCHAT_EXTERNAL_A_HOST', 'https://api5.iflychat.com');
}

if(!defined('DRUPALCHAT_EXTERNAL_A_PORT')){
  define('DRUPALCHAT_EXTERNAL_A_PORT', '443');
}

if(!defined('DRUPALCHAT_EXTERNAL_CDN_HOST')){
  define('DRUPALCHAT_EXTERNAL_CDN_HOST', 'cdn.iflychat.com');
}

define('IFLYCHAT_PLUGIN_VERSION', 'WP-4.2.7');
if (!defined('IFLYCHAT_DEBUG')) {
  define('IFLYCHAT_DEBUG',          false);
}
$iflychat_engine = TRUE;

function iflychat_get_hash_session()
{
    $data = uniqid(mt_rand(), TRUE);
    $hash = base64_encode(hash('sha256', $data, TRUE));
    return strtr($hash, array('+' => '-', '/' => '_', '=' => ''));
}

function iflychat_get_user_id()
{
  $current_user =  wp_get_current_user();
    global $wpdb;
    if ($current_user->ID) {
        return strval($current_user->ID);
    } else {
        return false;
    }
}

function iflychat_get_user_name()
{
  $current_user =  wp_get_current_user();
  $hook_user_name = apply_filters('iflychat_get_username_filter', '',$current_user->ID);
  if (!empty($hook_user_name)) {
    return $hook_user_name;
  }
    if ($current_user->ID) {
        if (empty($current_user->display_name) || (iflychat_get_option('iflychat_use_display_name') == '2')) {
            return $current_user->user_login;
        } else {
            return $current_user->display_name;
        }
    } else {
        return false;
    }
}


// Async load
function iflychat_async_scripts($url)
{
    if ( strpos( $url, '#asyncload') === false )
        return $url;
    else if ( is_admin() )
        return str_replace( '#asyncload', '', $url )."' async='async";
    else
        return str_replace( '#asyncload', '', $url )."' async='async";
}
add_filter( 'clean_url', 'iflychat_async_scripts');


function iflychat_init()
{
    $user_data = false;
    if(iflychat_check_access()){

      $_iflychat_protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';

        wp_enqueue_script('iflychat-ajax', plugin_dir_url( __FILE__ ) . 'js/iflychat.js', array(), false, true);
        wp_localize_script('iflychat-ajax', 'iflychat_app_id', iflychat_get_option('iflychat_app_id'));
        wp_localize_script('iflychat-ajax', 'iflychat_external_cdn_host', DRUPALCHAT_EXTERNAL_CDN_HOST);
        if (is_user_logged_in()) {
            $user_data = json_encode(_iflychat_get_user_auth());
        }
        if(iflychat_get_option('iflychat_session_caching') == '1' && isset($_SESSION['user_data']) && $_SESSION['user_data'] == $user_data){
            //if(iflychat_get_option('iflychat_enable_friends') == '1'){
            if (isset($_SESSION['token']) && !empty($_SESSION['token'])) {
                wp_localize_script('iflychat-ajax', 'iflychat_auth_token', $_SESSION['token']);
            }
        }
        if (is_user_logged_in()) {
            wp_localize_script('iflychat-ajax', 'iflychat_auth_url', admin_url('admin-ajax.php', $_iflychat_protocol));
        }

      if(iflychat_get_option('iflychat_popup_chat') == '1'){
        wp_enqueue_script('iflychat-popup', plugin_dir_url( __FILE__ ) . 'js/iflychat-popup.js', array(), false, true);
      }else if(iflychat_get_option('iflychat_popup_chat') == '2' && !is_admin()){
        wp_enqueue_script('iflychat-popup', plugin_dir_url( __FILE__ ) . 'js/iflychat-popup.js', array(), false, true);
      }else if((iflychat_get_option('iflychat_popup_chat') == '3' || iflychat_get_option('iflychat_popup_chat') == '4') && iflychat_path_check()){
        wp_enqueue_script('iflychat-popup', plugin_dir_url( __FILE__ ) . 'js/iflychat-popup.js', array(), false, true);
      }

    }
}


// add_filter('iflychat_get_user_groups_filter','iflychat_get_user_groups');
// function iflychat_get_user_groups(){
//$current_user =  wp_get_current_user();
// $arr = array();
// if ($current_user->ID % 2 == 0 ) {
//    $arr['A'] = "A";
// } else {
//    $arr['B'] = "B";
// }
//
//    return (array)$arr;
// };

/**
 * function to get user_details
 */
function _iflychat_get_user_auth()
{
  $current_user =  wp_get_current_user();
  $admin_check = FALSE;
  if (iflychat_check_chat_admin()) {
      $chat_role = "admin";
  } else if(iflychat_check_chat_moderator()){
      $chat_role = "moderator";
  }else{
      $chat_role = "participant";
  }
  $role = array();
  foreach ($current_user->roles as $rkey => $rvalue) {
      $role[$rvalue] = $rvalue;
  }
  if (iflychat_get_user_id() && iflychat_get_user_name()) {
    $user_data = array(
      'user_id' => iflychat_get_user_id(),
      'user_name' => iflychat_get_user_name(),
      'user_roles' => $role,
      'chat_role' => $chat_role,
      'user_list_filter' => 'all',
      'user_status' => TRUE,
    );
  }

  $user_data['user_avatar_url'] = iflychat_get_user_pic_url();
  $user_data['user_profile_url'] = iflychat_get_user_profile_url();

  //Added allRoles if chat_role is admin or moderator
  if ($chat_role == 'admin' || $chat_role == 'moderator') {
    global $wp_roles;
    $user_data['user_site_roles'] = $wp_roles->get_names();
  }

  $hook_user_groups = apply_filters('iflychat_get_user_groups_filter', array(),$current_user->ID);
  $hook_user_friends = apply_filters('iflychat_get_user_friends_filter', array(),$current_user->ID);
  $hook_user_roles = apply_filters('iflychat_get_user_roles_filter', array(),$current_user->ID);
  if ((iflychat_get_option('iflychat_enable_friends') == '2') && function_exists('friends_get_friend_user_ids')) { // filtering based on buddypress friends
      //echo 'enable friends';
      $user_data['user_list_filter'] = 'friend';
      $final_list = array();
      $final_list['1']['name'] = 'friend';
      $final_list['1']['plural'] = 'friends';
      $final_list['1']['valid_uids'] = friends_get_friend_user_ids(iflychat_get_user_id());
      $user_data['user_relationships'] = $final_list;
  }else {
      $user_data['user_list_filter'] = 'all';
  }
  if (!empty($hook_user_friends)) {
      if (iflychat_get_option('iflychat_enable_friends') != '2') {
          iflychat_update_option('iflychat_enable_friends', '2');
      }
      $user_data['user_list_filter'] = 'friend';
      $final_list = array();
      $final_list['1']['name'] = 'friend';
      $final_list['1']['plural'] = 'friends';
      $final_list['1']['valid_uids'] = $hook_user_friends;
      $user_data['user_relationships'] = $final_list;
  }
  if (!empty($hook_user_groups)) {
     // echo 'hook_user_groups';
      $user_data['user_list_filter'] = 'group';
      $user_data['user_groups'] = $hook_user_groups;
      if (iflychat_get_option('iflychat_enable_user_groups') != '1') {
          iflychat_update_option('iflychat_enable_user_groups', '1');
      }
  }
  if (empty($hook_user_groups)) {
      if (iflychat_get_option('iflychat_enable_user_groups') != '2') {
          iflychat_update_option('iflychat_enable_user_groups', '2');
      }
  }
  if (!empty($hook_user_roles)) {
    $user_data['user_roles'] = $hook_user_roles;
  }


  return $user_data;
}


function _iflychat_get_auth()
{   $current_user =  wp_get_current_user();
    global $wp_version;
    if (iflychat_get_option('iflychat_api_key') == " ") {
        return null;
    }
    $admin_check = FALSE;
    if (iflychat_check_chat_admin()) {
        $chat_role = "admin";
    } else if(iflychat_check_chat_moderator()){
        $chat_role = "moderator";
    }else{
        $chat_role = "participant";
    }
    $role = array();
    foreach ($current_user->roles as $rkey => $rvalue) {
        $role[$rvalue] = $rvalue;
    }

  if (iflychat_get_user_id() && iflychat_get_user_name()) {
    $data = array(
      'api_key' => iflychat_get_option('iflychat_api_key'),
      'app_id' =>  iflychat_get_option('iflychat_app_id')? iflychat_get_option('iflychat_app_id'):'',
      'version' => IFLYCHAT_PLUGIN_VERSION,
    );
  }

    $user_data = _iflychat_get_user_auth();
    $data = array_merge($data, $user_data);
    $_SESSION['user_data'] = json_encode($user_data);

    $options = array(
        'method' => 'POST',
        'body' => $data,
        'timeout' => 15,
        'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
        'sslverify' => false,
    );

    $result = wp_remote_head(iflychat_get_host(TRUE) . ':' . DRUPALCHAT_EXTERNAL_A_PORT . '/api/1.1/token/generate', $options);
    if (!is_wp_error($result) && $result['response']['code'] == 200) {
        $result = json_decode($result['body']);
        if (is_user_logged_in()) {
            $_SESSION['token'] = $result->key;
            //print_r($result);
            //print_r(gettype($result->app_id));

          //  if(iflychat_get_option('iflychat_app_id') == ''){
          //    iflychat_update_option('iflychat_app_id', $result->app_id);
          //  }
        }
        //print_r(gettype($result));
        return $result;
    }
    //  else {
    //   //return null;
    //   return $result['response'];
    // }
    else if(!is_wp_error($result) && $result['response']['code'] != 200){
      //return null;
      return $result['response'];
    } else {
      $error = array(
        'code' => $result->get_error_code(),
        'message' => $result->get_error_message()
      );
      //print_r($error);
      return $error;
    }
}

function iflychat_mobile_auth()
{
    if (iflychat_get_option('iflychat_enable_mobile_sdk_integration', '2') == '1') {
        $uid = wp_authenticate_username_password(null, $_POST['username'], $_POST['password']);
        $id = ($uid->data->ID);
        if ($id) {
            $user = wp_set_current_user($id, $_POST['username']);
            $result = json_encode(_iflychat_get_auth($_POST['username']));
            header("Content-Type: application/json");
            echo $result;
        } else {
            header('HTTP/1.1 403 Access Denied');
            echo 'Access Denied';
        }
    } else {
        header('HTTP/1.1 403 Access Denied');
        echo "Please Enable Mobile SDK Integration";
    }
    exit;
}

function iflychat_submit_uth()
{

    $json = NULL;
    $json = _iflychat_get_auth();
    //print_r(gettype($json));

    $response = json_encode($json);
    //print_r(gettype($response));
     header("Content-Type: application/json");
     echo $response;
     exit;
}

function iflychat_install()
{
    global $wpdb;
}

function iflychat_uninstall()
{
    //delete_option('iflychat_api_key');
    global $wpdb;
}

function iflychat_set_options()
{
    $options = array(
        'app_id' => array(
            'name' => 'iflychat_app_id',
            'default' => ' ',
            'desc' => '<b>APP ID</b> (register at <a href="https://iflychat.com">iFlyChat.com</a> to get it)',
            'input_type' => 'text'
        ),
        'api_key' => array(
            'name' => 'iflychat_api_key',
            'default' => ' ',
            'desc' => '<b>API key</b> (register at <a href="https://iflychat.com">iFlyChat.com</a> to get it)',
            'input_type' => 'text'
        ),
        'use_display_name' => array (
            'name' => 'iflychat_use_display_name',
            'default' => '1',
            'desc' => 'Specify whether to use display name or username for logged-in user',
            'input_type' => 'dropdown',
            'data' => array(
                '1' => 'Display Name',
                '2' => 'Username')
        ),
        'embed_chat' => array (
            'name' => 'iflychat_embed_chat',
            'desc' => 'Show embed chat',
            'default' => 'View Tutorial',
            'input_type' => 'button',
            'link' => 'https://iflychat.com/embedded-chatroom-example-public-chatroom'
        ),
        'enable_friends' => array (
            'name' => 'iflychat_enable_friends',
            'default' => '1',
            'desc' => 'Show only friends in online user list',
            'input_type' => 'dropdown',
            'data' => array(
                '1' => 'No',
                '2' => 'BuddyPress Friends')
        ),
        'popup_chat' => array (
            'name' => 'iflychat_popup_chat',
            'default' => '1',
            'desc' => 'Show Popup Chat',
            'input_type' => 'dropdown',
            'data' => array(
                '1' => 'Everywhere',
                '2' => 'Frontend Only',
                '3' => 'Everywhere except those listed',
                '4' => 'Only the listed pages',
                '5' => 'Disable')
        ),
        'path_pages' => array (
            'name' => 'iflychat_path_pages',
            'default' => '',
            'desc' => "Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard. Example paths are <b>/2012/10/my-post</b> for a single post and <b>/2012/*</b> for a group of posts. The path should always start with a forward slash(/).",
            'input_type' => 'textarea'
        ),
        'chat_moderators_array' => array (
          'name' => 'iflychat_chat_moderators_array',
          'default' => '',
          'desc' => "Specify WordPress username of users who should be chat moderators (separated by comma)",
          'input_type' => 'textarea'
        ),
        'chat_admins_array' => array (
          'name' => 'iflychat_chat_admins_array',
          'default' => '',
          'desc' => "Specify WordPress username of users who should be chat admininstrators (separated by comma)",
          'input_type' => 'textarea'
        ),
        'session_caching' => array (
            'name' => 'iflychat_session_caching',
            'default' => '2',
            'desc' => 'Enable Session Caching',
            'input_type' => 'dropdown',
            'data' => array(
                '1' => 'Yes',
                '2' => 'No')
        ),

    );

    return $options;

}

//create settings page
function iflychat_settings()
{
    wp_enqueue_script( 'iflychat-admin', plugin_dir_url( __FILE__ ) . 'js/iflychat.admin.script.js', array('jquery'));
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'plugin_settings';
    $i = 0;
    ?>
    <div class="wrap">
    <h1><?php _e('iFlyChat Settings', 'iflychat_settings'); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="?page=iflychat_settings&tab=plugin_settings"
           class="nav-tab <?php echo $active_tab == 'plugin_settings' ? 'nav-tab-active' : ''; ?>">Plugin Settings</a>
        <a href="?page=iflychat_settings&tab=app_settings"
           class="nav-tab <?php echo $active_tab == 'app_settings' ? 'nav-tab-active' : ''; ?>">App Settings</a>
    </h2>

        <?php
        if ($active_tab == 'plugin_settings') {
          $result = _iflychat_get_auth();
          if(gettype($result) == 'array'){

            if($result['code'] == 403){?>
              <div id="message" class="error"><p><strong><?php _e('Invalid API Key.', 'iflychat_settings'); ?></strong></p></div>
              <?php
            }else if($result['code'] == 503){?>
              <div id="message" class="error"><p><strong><?php _e('503 Error. Service Unavailable.', 'iflychat_settings'); ?></strong></p></div>
              <?php
            }else{?>
              <div id="message" class="error"><p><strong><?php _e('Error Message - '.$result['message'].'.Error Code - '.$result['code'], 'wp_error'); ?></strong></p></div>
              <?php
            }
          }
        if(iflychat_validate_fields()){
          if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
            ?>
            <div id="message" class="updated fade"><p><strong><?php _e('Settings Updated', 'iflychat_settings'); ?></strong></p></div>
            <?php
          }
        }else{
          ?>
          <div id="message" class="error"><p><strong><?php _e('Invalid APP ID.', 'iflychat_settings'); ?></strong></p></div>
          <?php
        }
        ?>
        <form method="post" action="<?php
        if(is_multisite() && is_plugin_active_for_network(plugin_basename(__FILE__ ))) {
            echo esc_url('edit.php?action=iflychat_network_settings');
        }
        else {
            echo esc_url('options.php');
        }
        ?>">
            <div>
                <?php settings_fields('iflychat-settings'); ?>
            </div>

            <?php
            $options = iflychat_set_options();
            ?>
            <table class="form-table">
                <?php foreach($options as $option){ ?>
                    <?php
                    //if option type is a dropdown, do this
                    if ( $option['input_type'] == 'dropdown'){ ?>
                        <tr valign="top">
                            <th scope="row"><?php _e($option['desc'], 'iflychat_settings'); ?></th>
                            <td><select id="<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>">
                                    <?php foreach($option['data'] as $opt => $value){ ?>
                                        <option <?php if(iflychat_get_option($option['name']) == $opt){ echo 'selected="selected"';}?> name="<?php echo $option['name']; ?>" value="<?php echo $opt; ?>"><?php echo $value ; ?></option>
                                    <?php } //endforeach ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                        //if option type is text, do this
                    }elseif ( $option['input_type'] == 'text'){ ?>
                        <tr valign="top">
                            <th scope="row"><?php _e($option['desc'], 'iflychat_settings'); ?></th>
                            <td><input id="<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" value="<?php echo iflychat_get_option($option['name']); ?>" size="64" />
                            </td>
                        </tr>
                        <?php
                        //if option type is text, do this
                    }elseif ( $option['input_type'] == 'textarea'){ ?>
                        <tr valign="top" id="<?php if($option['name'] === 'iflychat_path_pages') echo $option['name'] ?>">
                            <th scope="row"><?php _e($option['desc'], 'iflychat_settings'); ?></th>
                            <td><textarea  cols="80" rows="6" name="<?php echo $option['name']; ?>"><?php echo iflychat_get_option($option['name']); ?>
									</textarea>
                            </td>
                        </tr>
                        <?php
                    }elseif ($option['input_type'] == 'button') { ?>
                      <tr valign="top">
                        <th scope="row"><?php _e($option['desc'], 'iflychat_settings'); ?></th>
                        <td><a target="_blank" href="<?php echo $option['link']; ?>"><input type="button" value="<?php echo $option['default']; ?>"</a>
                        </td>
                      </tr>
                      <?php

                    }else{} //endif

                } //endforeach ?>

      </table>
      <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Update', 'iflychat_settings'); ?>" /></p>
    </form>
    </div>
    <br />
    <hr />
    <br />
    <h3><?php echo 'Debug Information'; ?></h3>
    <p>
      <?php echo 'Having problems with iFlyChat? Check out our installation guide'; ?>
      <a href="https://iflychat.com/installation/wordpress-chat-plugin" target="_blank"><?php echo 'here'; ?></a>
      <?php echo '. You can also open a support ticket and we will look into it immediately. Please include the debug information given below.'; ?>
      <a href="https://iflychat.com/contact" target="_blank"><?php echo 'Contact support'; ?></a>
    </p>
    <textarea style="width:90%; height:200px;">
URL: <?php echo esc_url( get_option('siteurl') ); ?>

PHP Version: <?php echo esc_html( phpversion() ); ?>

Wordpress Version: <?php echo esc_html( $wp_version ); ?>

Active Theme: <?php
      if ( !function_exists('wp_get_theme') ) {
        $theme =  wp_get_themes();
        echo esc_html( $theme['Name'] . ' ' . $theme['Version'] );
      } else {
        $theme = wp_get_theme();
        echo esc_html( $theme->Name . ' ' . $theme->Version );
      }
      ?>

URL Open Method: <?php echo esc_html( iflychat_url_method() ); ?>

Plugin Version: <?php echo esc_html( IFLYCHAT_PLUGIN_VERSION ); ?>

Settings: iflychat_is_installed: <?php echo esc_html( iflychat_is_installed().' | ' ); ?>
      <?php foreach (iflychat_options() as $opt) {
        echo esc_html( ' '.$opt.': '.get_option($opt).' | ' );
      }
      ?>

Plugins: <?php
      foreach (get_plugins() as $key => $plugin) {
        $isactive = '';
        if (is_plugin_active($key)) {
          $isactive = '(active)';
        }
        echo esc_html( ' '.$plugin['Name'].' '.$plugin['Version'].' '.$isactive.' | ' );
      }
      ?>
        </textarea><br/>
    <?php
  } else {
    $iflychat_host = DRUPALCHAT_EXTERNAL_A_HOST;
    $host = explode("/", $iflychat_host);
    $host_name = $host[2];
    if (isset($_SESSION['token']) && !empty($_SESSION['token'])) {
      $token = $_SESSION['token'];
    } else {
      $token = _iflychat_get_auth()->key;
    }
    $dashboardUrl = "//cdn.iflychat.com/apps/dashboard/#/settings/app?sessid=" . $token . "&hostName=" . $host_name . "&hostPort=" . DRUPALCHAT_EXTERNAL_A_PORT;
    ?>
    <br/>
    <input type="button" class="button-primary" value="Open App Dashboard in new tab" onclick="window.open('<?php echo $dashboardUrl ?>')">
    <?php
  }

        ?>
    </form>
    <?php
}
//check if iflychat is installed
function iflychat_is_installed() {
  $iflychat_api_key = get_option('iflychat_api_key');
  $iflychat_app_id = get_option('iflychat_app_id');
  if ( strlen( $iflychat_api_key ) > 0 && strlen( $iflychat_app_id ) > 0 ) {
    return true;
  }
  else {
    return false;
  }
}
function iflychat_options() {
  return array(
    'iflychat_app_id',
    'iflychat_api_key',
    'iflychat_use_display_name',
    'iflychat_enable_friends',
    'iflychat_popup_chat',
    'iflychat_path_pages',
    'iflychat_chat_moderators_array',
    'iflychat_chat_admins_array',
    'iflychat_session_caching'
  );
}
function iflychat_url_method() {
  if(function_exists('curl_init')) {
    return 'curl';
  } else if(ini_get('allow_url_fopen') && function_exists('stream_get_contents')) {
    return 'fopen';
  } else {
    return 'fsockopen';
  }
}
//add settings page
function iflychat_settings_page()
{
    if (is_multisite() && is_plugin_active_for_network(plugin_basename(__FILE__))) {
        add_submenu_page('settings.php', 'iFlyChat Settings', 'iFlyChat Settings', 'manage_options', 'iflychat_settings', 'iflychat_settings');
    } else {
        add_options_page('iFlyChat Settings', 'iFlyChat Settings', 'manage_options', 'iflychat_settings', 'iflychat_settings');
    }
}
//register settings loops through options
function iflychat_register_settings()
{
    $options = iflychat_set_options(); //get options array

    foreach($options as $option){
        register_setting('iflychat-settings', $option['name']);
        //register each setting with option's 'name'

        if (iflychat_get_option($option['name']) === false) {
            iflychat_add_option($option['name'], $option['default'], '', 'yes'); //set option defaults
        }
    }

//    if (iflychat_get_option('iflychat_promote_plugin') === false) {
//        iflychat_add_option('iflychat_promote_plugin', '0', '', 'yes');
//    }
//
//    if (iflychat_get_option('iflychat_ext_d_i') === false) {
//        iflychat_add_option('iflychat_ext_d_i', '1', '', 'yes');
//    }

}
add_action( 'admin_init', 'iflychat_register_settings' );

function iflychat_validate_fields(){
    $app_id = iflychat_get_option('iflychat_app_id');
    if(strlen($app_id) == 36 && $app_id[14] == '4'){
        return true;
        //$errors->add( 'iflychat_app_id_error', __( '<strong>ERROR</strong>: Invalid APP ID.' ) );
    }else{
        return false;
    }
    //return $errors;
}


if (is_multisite() && is_plugin_active_for_network(plugin_basename(__FILE__))) {
    add_action('network_admin_menu', 'iflychat_settings_page');
} else {
    add_action('admin_menu', 'iflychat_settings_page');
}


function iflychat_network_settings()
{

    if (($_POST['option_page'] == "iflychat-settings") && ($_POST['action'] == "update")) {
          foreach ((array)$_POST as $key => $value) {
              if (substr($key, 0, 9) === "iflychat_") {
                update_site_option($key, $value);
              }
          }


    }
    // redirect to settings page in network
    wp_redirect(
        add_query_arg(
            array('page' => 'iflychat_settings', 'updated' => 'true'),
            network_admin_url('settings.php')
        )
    );
    exit;
}

if (is_multisite() && is_plugin_active_for_network(plugin_basename(__FILE__))) {
    add_action('network_admin_edit_iflychat_network_settings', 'iflychat_network_settings');
}

add_action('init', 'iflychat_init');
add_action('wp_ajax_nopriv_iflychat-mobile-auth', 'iflychat_mobile_auth');
add_action('wp_ajax_iflychat-mobile-auth', 'iflychat_mobile_auth');
add_action('wp_ajax_nopriv_iflychat-get', 'iflychat_submit_uth');
add_action('wp_ajax_iflychat-get', 'iflychat_submit_uth');
add_action('wp_ajax_nopriv_iflychat-offline-msg', 'iflychat_send_offline_message');
add_action('wp_ajax_iflychat-offline-msg', 'iflychat_send_offline_message');
add_action('wp_ajax_nopriv_iflychat-change-guest-name', 'iflychat_change_guest_name');
add_action('wp_login', 'iflychat_user_login');
add_action('wp_logout', 'iflychat_user_logout');
add_shortcode('iflychat_inbox', 'iflychat_get_inbox');
add_shortcode('iflychat_message_thread', 'iflychat_get_message_thread');
add_shortcode('iflychat_embed', 'iflychat_get_embed_code');
register_activation_hook(__FILE__, 'iflychat_install');
register_deactivation_hook(__FILE__, 'iflychat_uninstall');
function iflychat_match_path($path, $patterns)
{
    $to_replace = array(
        '/(\r\n?|\n)/',
        '/\\\\\*/',
    );
    $replacements = array(
        '|',
        '.*',
    );
    $patterns_quoted = preg_quote($patterns, '/');
    $regexps[$patterns] = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
    return (bool)preg_match($regexps[$patterns], $path);
}

function iflychat_path_check()
{
    $page_match = FALSE;

    if (trim(iflychat_get_option('iflychat_path_pages')) != '') {
        if (function_exists('mb_strtolower')) {
            $pages = mb_strtolower(iflychat_get_option('iflychat_path_pages'));
            $path = mb_strtolower($_SERVER['REQUEST_URI']);
        } else {
            $pages = strtolower(iflychat_get_option('iflychat_path_pages'));
            $path = strtolower($_SERVER['REQUEST_URI']);
        }
        $page_match = iflychat_match_path($path, $pages);
        if(iflychat_get_option('iflychat_popup_chat') == '3') $page_match = !$page_match;
        if(iflychat_get_option('iflychat_popup_chat') == '4') $page_match = $page_match;
        //$page_match = (iflychat_get_option('iflychat_popup_chat') == '3') ? (!$page_match) : $page_match;
    } else if (iflychat_get_option('iflychat_popup_chat') == '1') {
        $page_match = TRUE;
    }
    return $page_match;
}

function iflychat_mail_set_content_type()
{
    return "text/html";
}

function iflychat_send_offline_message()
{
    if (isset($_POST['drupalchat_m_contact_details']) && isset($_POST['drupalchat_m_message'])) {
        global $user;
        $drupalchat_offline_mail = array();
        $drupalchat_offline_mail['subject'] = 'iFlyChat: Message from Customer';
        $drupalchat_offline_mail['contact_details'] = '<p>' . iflychat_get_option('iflychat_support_chat_offline_message_contact') . ': ' . ($_POST['drupalchat_m_contact_details']) . '</p>';
        $drupalchat_offline_mail['message'] = '<p>' . iflychat_get_option('iflychat_support_chat_offline_message_label') . ': ' . ($_POST['drupalchat_m_message']) . '</p>';
        $drupalchat_offline_mail['message'] = $drupalchat_offline_mail['contact_details'] . '<br><br>' . $drupalchat_offline_mail['message'];
        add_filter('wp_mail_content_type', 'iflychat_mail_set_content_type');
        $result = wp_mail(iflychat_get_option('iflychat_support_chat_offline_message_email'), $drupalchat_offline_mail['subject'], $drupalchat_offline_mail['message']);
    }
    $response = json_encode($result);
    header("Content-Type: application/json");
    echo $response;
    exit;
}

function iflychat_check_chat_admin()
{
  $current_user =  wp_get_current_user();
    if (current_user_can('activate_plugins')) {
        return TRUE;
    }
    $a = iflychat_get_option('iflychat_chat_admins_array');
    if (!empty($a) && ($current_user->ID)) {
        $a_names = explode(",", $a);
        foreach ($a_names as $an) {
            $aa = trim($an);
            if ($aa == $current_user->user_login) {
                return TRUE;
                break;
            }
        }
    }
    return FALSE;
}

function iflychat_check_chat_moderator()
{
  $current_user =  wp_get_current_user();
    
    $a = iflychat_get_option('iflychat_chat_moderators_array');
    
    if (!empty($a) && ($current_user->ID)) {
        $a_names = explode(",", $a);
        foreach ($a_names as $an) {
            $aa = trim($an);
            if ($aa == $current_user->user_login) {
                return TRUE;
                break;
            }
        }
    }
    return FALSE;
}

function iflychat_token_destroy()
{
    $data = array(
        'api_key' => iflychat_get_option('iflychat_api_key')
    );
    $options = array(
        'method' => 'POST',
        'body' => $data,
        'timeout' => 15,
        'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
        'sslverify' => false,
    );
    $result = wp_remote_head(iflychat_get_host(TRUE) . ':' . DRUPALCHAT_EXTERNAL_A_PORT . '/api/1.1/token/'
        . $_SESSION['token'] . '/delete', $options);
//if(!is_wp_error($result) && $result['response']['code'] == 200) {
    session_unset();
//}
}

function iflychat_user_login()
{
    setcookie("iflychat_key", "", time() - 3600, "/");
    setcookie("iflychat_css", "", time() - 3600, "/");
    setcookie("iflychat_time", "", time() - 3600, "/");
}

function iflychat_user_logout()
{
    setcookie("iflychat_key", "", time() - 3600, "/");
    setcookie("iflychat_css", "", time() - 3600, "/");
    setcookie("iflychat_time", "", time() - 3600, "/");
    iflychat_token_destroy();
}

function iflychat_get_inbox()
{
    $data = array(
        'uid' => iflychat_get_user_id(),
        'api_key' => iflychat_get_option('iflychat_api_key'),
    );
    $options = array(
        'method' => 'POST',
        'body' => $data,
        'timeout' => 15,
        'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
        'sslverify' => false,
    );
    $result = wp_remote_head(iflychat_get_host(TRUE) . ':' . DRUPALCHAT_EXTERNAL_A_PORT . '/r/', $options);
    $output = '';
    if (!is_wp_error($result) && $result['response']['code'] == 200) {
        $query = json_decode($result['body']);
        $timezone_offet = iflychat_get_option('gmt_offset');
        $date_format = iflychat_get_option('date_format');
        $time_format = iflychat_get_option('time_format');
        foreach ($query as $record) {
            $rt = $record->timestamp + ($timezone_offet * 3600);
            $output .= '<div style="display:block;border-bottom: 1px solid #ccc; padding: 10px;"><div style="font-size:130%; display: inline;">' . $record->name . '</div><div style="float:right;color:#AAA; font-size: 70%;">' . date("{$date_format} {$time_format}", $rt) . '</div><div style="display: block; padding: 10px;">' . $record->message . '</div></div>';
        }
    }
    return $output;
}

function iflychat_get_message_thread($atts)
{
    extract(shortcode_atts(array(
        'room_id' => '0',
        'id' => '0',
    ), $atts));
    if (($room_id[0] == 'c') && ($room_id[1] == '-')) {
        $room_id = substr($room_id, 2);
    } else if ($id != '0') {
        if (($id[0] == 'c') && ($id[1] == '-')) {
            $room_id = substr($id, 2);
        } else {
            $room_id = $id;
        }
    }
    $data = array(
        'uid1' => iflychat_get_user_id(),
        'uid2' => ('c-' . $room_id),
        'api_key' => iflychat_get_option('iflychat_api_key'),
    );
    $options = array(
        'method' => 'POST',
        'body' => $data,
        'timeout' => 15,
        'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
        'sslverify' => false,
    );
    $result = wp_remote_head(iflychat_get_host(TRUE) . ':' . DRUPALCHAT_EXTERNAL_A_PORT . '/q/', $options);
    $output = '';
    if (!is_wp_error($result) && $result['response']['code'] == 200) {
        $query = json_decode($result['body']);
        $timezone_offet = iflychat_get_option('gmt_offset');
        $date_format = iflychat_get_option('date_format');
        $time_format = iflychat_get_option('time_format');
        foreach ($query as $record) {
            $rt = $record->timestamp + ($timezone_offet * 3600);
            $output .= '<div style="display:block;border-bottom: 1px solid #ccc; padding: 1% 0% 1% 0%;"></div><div style="display:block; padding-top: 1%; padding-bottom: 0%"><div style="font-size:100%; display: inline;"><a href="#">' . $record->from_name . '</a></div><div style="float:right;font-size: 70%;">' . date("{$date_format} {$time_format}", $rt) . '</div><div style="display: block; padding-top: 1%; padding-bottom: 0%">' . $record->message . '</div></div>';
        }
    }
    return $output;
}

function iflychat_get_embed_code($atts)
{

    global $iflychat_engine;

    extract(shortcode_atts(array(
        'room_id' => '0',
        'id' => '0',
        'hide_user_list' => 'no',
        'hide_popup_chat' => 'no',
        'height' => '550px',
    ), $atts));

    $output = '';

    if ($iflychat_engine) {

        $output = '<style>.drupalchat-embed-chatroom-content {height: ' . $height . ' !important;}';
        if ($hide_user_list == "yes") {
            $output .= '#drupalchat-embed-user-list {display:none !important;}.drupalchat-embed-chatroom-content {width:95% !important;}';
        }
        $output .= '</style>';
        $output .= '<script type="text/javascript">if(typeof(iflyembed) === "undefined") {iflyembed = {};iflyembed.settings = {};iflyembed.settings.ifly = {};}iflyembed.settings.ifly.embed = "1";iflyembed.settings.ifly.ur_hy = "1";iflyembed.settings.ifly.embed_msg = "Type your message here. Press Enter to send.";iflyembed.settings.ifly.embed_online_user_text = "Online Users";</script>';
        if (($room_id[0] == 'c') && ($room_id[1] == '-')) {
            $room_id = substr($room_id, 2);
        } else if ($id != '0') {
            if (($id[0] == 'c') && ($id[1] == '-')) {
                $room_id = substr($id, 2);
            } else {
                $room_id = $id;
            }
        }
        $output .= '<div id="drupalchat-embed-chatroom-' . $room_id . '" class="drupalchat-embed-chatroom-container';
        if ($hide_popup_chat == "yes") {
            $output .= ' drupalchat-hide-popup-chat';
        }
        $output .= '"></div>';
    } else if (iflychat_check_chat_admin()) {
        $output .= '<div style="background-color:#eee;color:red;padding:5px;">iFlyChat is NOT set to load on this page. Please check the path visibility settings on iFlyChat plugin settings page. In case of any query, please create a support ticket <a href="https://iflychat.com/contact" target="_blank">here</a>. This error message is shown only to chat admins.</div>';
    }
    return $output;
}

function iflychat_get_user_pic_url()
{
     $current_user = wp_get_current_user();
    $url = 'http://www.gravatar.com/avatar/' . (($current_user->ID) ? (md5(strtolower($current_user->user_email))) : ('00000000000000000000000000000000')) . '?d=mm&size=24';
    $hook_url = apply_filters('iflychat_get_user_avatar_url_filter', '',$current_user->ID);
    if (!empty($hook_url)) {
        return $hook_url;
    }
    if (function_exists("bp_core_fetch_avatar") && ($current_user->ID > 0)) {
        $url = iflychat_get_avatar_url_from_html(bp_core_fetch_avatar(array('item_id' => iflychat_get_user_id(), 'html' => false)));
    } else if (function_exists("user_avatar_fetch_avatar") && ($current_user->ID > 0)) {
        $local_url = user_avatar_fetch_avatar(array('html' => false, 'item_id' => $current_user->ID));
        if ($local_url) {
            $url = $local_url;
        }
    } else if (function_exists("userpro_profile_data") && ($current_user->ID > 0)) {
        $user_id = $current_user->ID;
        $url = userpro_profile_data('profilepicture', $user_id);
    } else if (function_exists("um_get_avatar_url") && ($current_user->ID > 0)) {
        $user_id = $current_user->ID;
        $url = um_get_avatar_url(get_avatar($user_id, $size = 96));
    } else if (function_exists("get_wp_user_avatar_src") && ($current_user->ID > 0)) {
        $url = get_wp_user_avatar_src(iflychat_get_user_id());
    } else if (function_exists("get_simple_local_avatar") && ($current_user->ID > 0)) {
        $source = get_simple_local_avatar(iflychat_get_user_id());
        $source = explode('src="', $source);
        if (isset($source[1])) {
            $source = explode('"', $source[1]);
        } else {
            $source = explode("src='", $source[0]);
            if (isset($source[1])) {
                $source = explode("'", $source[1]);
            } else {
                $source[0] = 'http://www.gravatar.com/avatar/' . (($current_user->ID) ? (md5(strtolower($current_user->user_email))) : ('00000000000000000000000000000000')) . '?d=mm&size=24';
            }
        }
        $url = $source[0];
    } else if ($current_user->ID > 0) {
        if (false && function_exists("get_avatar_url")) {
            $url = get_avatar_url(iflychat_get_user_id());
        } else {
            $url = iflychat_get_avatar_url_from_html(get_avatar(iflychat_get_user_id()));
        }
    }

    $pos = strpos($url, ':');
    if ($pos !== false) {
        $url = substr($url, $pos + 1);
    }
    return $url;
}

function iflychat_get_user_profile_url()
{
    global $userpro;
    $current_user =  wp_get_current_user();

    $upl = 'javascript:void(0)';
    $hook_upl = apply_filters('iflychat_get_user_profile_url_filter', 'javascript:void(0)',$current_user->ID);
    if ($hook_upl == $upl) {
        if (function_exists("bp_core_get_userlink") && ($current_user->ID > 0)) {
            $upl = bp_core_get_userlink($current_user->ID, false, true);
        } else if (function_exists("um_user_profile_url") && ($current_user->ID > 0)) {
            $upl = um_user_profile_url($current_user->ID, false, true);
        } else if (($current_user->ID > 0) && ($userpro != null)) {
            $upl = ($userpro->permalink($current_user->ID));
        }
        return $upl;
    } else {
        return $hook_upl;
    }
}

function iflychat_get_option($name)
{
    if (is_multisite() && is_plugin_active_for_network(plugin_basename(__FILE__))) {
        return get_site_option($name);
    } else {
        return get_option($name);
    }
}

function iflychat_add_option($name, $value, $v2, $v3)
{
    if (is_multisite() && is_plugin_active_for_network(plugin_basename(__FILE__))) {
        return add_site_option($name, $value, $v2, $v3);
    } else {
        return add_option($name, $value, $v2, $v3);
    }
}

function iflychat_update_option($name, $value)
{
    if (is_multisite() && is_plugin_active_for_network(plugin_basename(__FILE__))) {
        return update_site_option($name, $value);
    } else {
        return update_option($name, $value);
    }
}

function iflychat_check_access()
{
  global $current_user;
    $flag = apply_filters('iflychat_check_access_filter', true, $current_user->ID);
    if ($flag == true) {
        return true;
    } else {
        return false;
    }
    exit;
}

function iflychat_get_avatar_url_from_html($source)
{
    $source = explode('src="', $source);
    if (isset($source[1])) {
        $source = explode('"', $source[1]);
    } else {
        $source = explode("src='", $source[0]);
        if (isset($source[1])) {
            $source = explode("'", $source[1]);
        } else {
            //$source[0] = '';
        }
    }
    return $source[0];
}

function iflychat_get_host($https = FALSE)
{
    if (iflychat_get_option('iflychat_show_admin_list') == '1') {
        if ($https) {
            return 'https://support1.iflychat.com';
        } else {
            return 'http://support1.iflychat.com';
        }
    } else {
        if ($https) {
            return DRUPALCHAT_EXTERNAL_A_HOST;
        } else {
            return DRUPALCHAT_EXTERNAL_HOST;
        }
    }
}

function iflychat_process_stop_word_list($words)
{
    $new_arr = array_map('trim', explode(',', $words));
    $final = implode(",", $new_arr);
    return $final;
}

?>

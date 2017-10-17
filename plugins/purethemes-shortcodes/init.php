<?php /*
Plugin Name: Purethemes.net Shortcodes
Plugin URI:
Description: Adds user-friendly popup to generate shortcodes.
Version: 2.2
Author: Purethems.net
Author URI: http://purethemes.net
*/

class PureThemes_Shortcodes {

    function __construct() {

        define('PT_TINYMCE_URI', plugin_dir_url( __FILE__ ) .'inc');
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('wp_ajax_form_generate', array($this, 'form_generate_callback'));

    }

    function init() {
        if( ! is_admin() ) {
          
            wp_enqueue_style( 'purethemes-shortcodes', plugin_dir_url( __FILE__ ) . 'css/shortcodes.css' );
            wp_enqueue_script( 'purethemes-shortcodes', plugin_dir_url( __FILE__ ) . 'js/shortcodes.js',  array( 'jquery' ), '', true  );
            
        }

        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
            return;

        if ( get_user_option('rich_editing') == 'true' ) {
            add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );
            add_action( 'admin_footer', array( $this, 'shortcode_modal_template' ) );
        }
        require_once( 'inc/shortcodes_list.php' );
        require_once( 'shortcodes.php' );
    }

    function admin_init() {
            // css
            wp_enqueue_style( 'purethemes-popup', PT_TINYMCE_URI . '/css/purethemes_shortcodes.css', false, '1.0', 'all' );

            // js
            //wp_enqueue_script( 'jquery-appendo', PT_TINYMCE_URI . '/js/jquery.appendo.js', false, '1.0', false );
           /* wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );*/
            wp_enqueue_script( 'custom-purethemes-shortcodes', PT_TINYMCE_URI . '/custom.js', false, '1.0', false );

    }


        /**
     * Insert shortcode media button
     *
     *
     */
    function shortcode_insert_button(){
        global $post;
        if(!empty($post)){
            echo '<a id="purethemes-shortcodes-insert" title="Pure Shortcodes Builder style="padding-left: 0.4em;" class="button editor-button" href="#inst">';
            echo '<img src="'. self::get_url( __FILE__ ) .'assets/images/icon.png" alt="Build Shortcode" style="padding:0 2px 1px;" />Shortcodes </a>';
        }
    }

  

    function form_generate_callback($shortcode){

        global $post;
        $pt_shortcodes = ptsc_shortcodes_list();
        $shortcode = $_POST['shortcode'];
        $output = '';
        if( isset( $pt_shortcodes ) && is_array( $pt_shortcodes ) ){
                // get shortcode config stuff
           if(isset($pt_shortcodes[$shortcode]['params'])){
                $params = $pt_shortcodes[$shortcode]['params'];
           }
           $labelbefore = '<tr valign="top"><th scope="row"><label>';
           $labelafter = '</label></th><td>';
           $rowend = '</td></tr>';
           $output .= '<input type="hidden" id="purethemes-shortcodes-key" class="exclude" value="'.$shortcode.'"/>';

           if(isset($pt_shortcodes[$shortcode]['wrapper'])) {
                $output .= '<table class="form-table"><tbody class="multi">';
           } else {
                $output .= '<table class="form-table"><tbody>';
           }
           if($pt_shortcodes[$shortcode]['has_content'] === true) {
                 $output .= '<input type="hidden" name="content_flag" id="content_flag" value="1" />';
           }
           if(isset($pt_shortcodes[$shortcode]['wrapper'])) {
                 $output .= '<input type="hidden" name="wrapper_tag" id="wrapper_tag" value="'.$pt_shortcodes[$shortcode]['wrapper'].'" />';
           }
           if(isset($params)){
               foreach( $params as $key => $param ) {

                $output .= $labelbefore. $param['label'] .$labelafter;
                switch( $param['type'] ) {
                    case 'text' :
                        $output .= '<input type="text" name="' . $key . '" id="' . $key . '" class="ptsc" value="' . $param['std'] . '" />';
                        if(!empty($param['desc'])) { $output .= '<p class="description">'.$param['desc'].'</p>'; }
                        $output .= $rowend . "\n";
                    break;

                    case 'gallery' :
                        $upload_link = esc_url( get_upload_iframe_src( 'image') );
                  
                        $output .= ' <a class="ptsc-upload-images button"  href="'.$upload_link.'">'.__('Select images').'</a>';
                        $output .= '<input type="text" name="' . $key . '" id="' . $key . '" class="ptsc ptsc-img-ids" value="' . $param['std'] . '" />';
                       
                        $output .= $rowend . "\n";
                    break;

                    case 'colorpicker' :
                        $output .= '<input type="text" name="' . $key . '" data-default-color="#ffffff" id="' . $key . '" class="ptsc wp-color-picker-field" value="' . $param['std'] . '" />';
                        if(!empty($param['desc'])) { $output .= '<p class="description">'.$param['desc'].'</p>'; }
                        $output .= $rowend . "\n";
                    break;

                    case 'select' :
                        $output .= '<select name="' . $key . '"  class="ptsc" id="' . $key . '">' . "\n";
                        foreach( $param['options'] as $value => $option ) {
                            if($value == $param['std']) {
                                $output .= '<option selected value="' . $value . '">' . $option . '</option>' . "\n";
                            } else {
                                $output .= '<option value="' . $value . '">' . $option . '</option>' . "\n";
                            }
                        }
                        $output .= '</select>';
                        if(!empty($param['desc'])) { $output .= '<p class="description">'.$param['desc'].'</p>'; }
                        $output .= $rowend . "\n";
                    break;

                    case 'textarea' :
                        $output .= '<textarea name="' . $key . '" class="ptsc-content" rows="4" cols="50" id="ptsc-' . $key . '"></textarea>';
                        if(!empty($param['desc'])) { $output .= '<p class="description">'.$param['desc'].'</p>'; }
                        $output .= $rowend . "\n";
                    break;

                    case 'checkbox' :
                        $output .= '<input type="checkbox"  class="ptsc"  name="' . $key . '" id="' . $key . '" ' . ( $param['std'] ? 'checked' : '' ) . ' />' . "\n";
                        if(!empty($param['desc'])) { $output .= '<p class="description">'.$param['desc'].'</p>'; }
                        $output .= $rowend . "\n";
                    break;

                

                    case 'checkbox-multi' :
                        $output .= "<ul>";
                        foreach( $param['options'] as $value => $option ) {
                            $output .= '<li><input type="checkbox"  class="ptsc"  name="' . $value . '" id="' . $value . '"/>';
                            $output .= ' <span>' . $option . '</span></li>';
                        }
                        $output .= "</ul>";
                        if(!empty($param['desc'])) { $output .= '<p class="description">'.$param['desc'].'</p>'; }
                        $output .= $rowend . "\n";
                    break;

                    case 'select-multi' :
                        $output .= '<select multiple name="' . $key . '"  class="ptsc" id="' . $key . '">' . "\n";
                        foreach( $param['options'] as $value => $option ) {
                           if($value == $param['std']) {
                                $output .= '<option selected value="' . $value . '">' . $option . '</option>' . "\n";
                            } else {
                                $output .= '<option value="' . $value . '">' . $option . '</option>' . "\n";
                            }
                        }
                        $output .= '</select>';
                        if(!empty($param['desc'])) { $output .= '<p class="description">'.$param['desc'].'</p>'; }
                        $output .= $rowend . "\n";
                    break;
                }
            } 
        }   else {
                $output .= 'This shortcode doesn\'t offer any attributes. Just click "Insert Shortcode" to add it to the content';
        }

        if(isset($pt_shortcodes[$shortcode]['wrapper'])) {
            $output .= '<tr valign="top"><th scope="row"></th><td><a href="#" class="button button-secondary button-small ptsc-duplicate">Duplicate</a>'.$rowend . "\n";
        }
        $output .= '</tbody></table>';
    }
    echo $output;
    die();
    }

        /**
     * Insert shortcode modal template
     *
     *
     */
    function shortcode_modal_template(){
        $screen = get_current_screen();

        if($screen->base != 'post'){return;}

        echo "<script type=\"text/html\" id=\"purethemes-shortcodes-shortcode-panel-tmpl\">\r\n";
        echo "  <div tabindex=\"0\" id=\"purethemes-shortcodes-shortcode-panel\"  class=\"hidden\">\r\n";
        echo "      <div class=\"media-modal-backdrop\"></div>\r\n";
        echo "      <div class=\"purethemes-shortcodes-modal-modal\">\r\n";
        echo "          <div class=\"purethemes-shortcodes-modal-content\">\r\n";
        echo "              <div class=\"purethemes-shortcodes-modal-header\">\r\n";
        echo "                  <a title=\"Close\" href=\"#\" class=\"media-modal-close\">\r\n";
        echo "                      <span class=\"media-modal-icon\"></span>\r\n";
        echo "                  </a>\r\n";
        echo "                  <h2 style=\"background: url(".self::get_url( '/assets/images/icon.png', __FILE__ ) . ") no-repeat scroll 0px 2px transparent; padding-left: 20px;\">".__('Purethemes','purethemes-shortcodes')." <small>".__("Shortcode Builder","purethemes-shortcodes")."</small></h2>\r\n";
        echo "              </div>\r\n";
        echo "              <div class=\"purethemes-shortcodes-modal-body\">\r\n";
        echo "                  <div id=\"purethemes-shortcodes-shortcode-config\" class=\"purethemes-shortcodes-shortcode-config\">\r\n";
        echo '                      <div id="purethemes-shortcodes-shortcode-config-nav">';
        echo '                              <ul>';
                                                $pt_shortcodes = ptsc_shortcodes_list();
                                                foreach ($pt_shortcodes as $shortcode => $key) {
                                                  echo '<li><a title="'.$key['label'].'" data-shortcode="'.$shortcode.'" href="#'.$shortcode.'">'.$key['label'].'</a></li>';
                                                } 
        echo "                              </ul>";
        echo "                      </div>\r\n";
        echo '                      <div class="purethemes-shortcodes-shortcode-config-content">
                   
                                    </div>
                                </div>';
        echo "              </div>\r\n";
        echo "          <div class=\"purethemes-shortcodes-modal-footer\">\r\n";
        echo "              <button class=\"button button-primary button-large\" id=\"purethemes-shortcodes-insert-shortcode\">".__("Insert Shortcode","purethemes-shortcodes")."</button>\r\n";
        echo "          </div>\r\n";
        echo "          </div>\r\n";
        echo "      </div>\r\n";
        echo "      </div>\r\n";
        echo "  </div>\r\n";
        echo "</script>\r\n";

        
    }

    /***
     * Get the current URL
     *
     */
    static function get_url($src = null, $path = null) {
        if(!empty($path)){
            return plugins_url( $src, $path);
        }
        return trailingslashit( plugins_url( $path , __FILE__ ) );
    }
}

$pt_shortcodes = new PureThemes_Shortcodes();

?>
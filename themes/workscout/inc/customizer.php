<?php
/**
 * WorkScout Theme Customizer.
 *
 * @package WorkScout
 */


Kirki::add_config( 'workscout', array(
    'capability'    => 'edit_theme_options',
    'option_type'   => 'option',
    'option_name'   => 'workscout',
    'disable_output'   => false,
) );



/**
 * Customizer additions.
 */

require get_template_directory() . '/inc/customizer/header.php';
require get_template_directory() . '/inc/customizer/jobs.php';
require get_template_directory() . '/inc/customizer/resumes.php';
require get_template_directory() . '/inc/customizer/maps.php';
require get_template_directory() . '/inc/customizer/colors.php';
require get_template_directory() . '/inc/customizer/layout.php';
require get_template_directory() . '/inc/customizer/registration.php';
require get_template_directory() . '/inc/customizer/blog.php';
require get_template_directory() . '/inc/customizer/shop.php';
require get_template_directory() . '/inc/customizer/footer.php';


require get_template_directory() . '/inc/customizer/title_tagline.php';
/*section blog*/


/*


Max Zoom In Level
Max Zoom Out Level*/

add_action('wp_head', 'workscout_stylesheet_content');


function workscout_generate_typo_css($typo){
    if($typo){
        $wpv_ot_default_fonts = array('arial','georgia','helvetica','palatino','tahoma','times','trebuchet','verdana');        
        $ot_google_fonts = get_theme_mod( 'ot_google_fonts', array() );
        foreach ($typo as  $key => $value) {
            if(isset($value) && !empty($value)) {
                if($key=='font-color') { $key = "color"; }
                if($key=='font-family') { 
                    if ( ! in_array( $value, $wpv_ot_default_fonts ) ) {
                        $value = $ot_google_fonts[$value]['family']; } 
                    }
                echo $key.":".$value.";";
                
            }
        }
    }
}

function workscout_generate_bg_css($typo){
    if($typo){
        foreach ($typo as  $key => $value) {
            if(isset($value) && !empty($value)) {
                if($key=='background-image') $value = "url('".$value."')";
                return esc_attr($key).":".$value.";";
            }
        }
    }
}


function mobile_menu_css(){
    $bodytypo = ot_get_option( 'workscout_body_font');
    $menutypo = ot_get_option( 'workscout_menu_font');
    $logotypo = ot_get_option( 'workscout_logo_font');
    $headerstypo = ot_get_option( 'workscout_headers_font');

    $ot_google_fonts = get_theme_mod( 'ot_google_fonts', array() );
 
    if(isset($bodytypo['font-family'])) {
        $tempfamily = $bodytypo['font-family'];
        
        $wpv_ot_default_fonts = array('arial','georgia','helvetica','palatino','tahoma','times','trebuchet','verdana');
        if(!empty($tempfamily)) {
	        if ( in_array( $tempfamily, $wpv_ot_default_fonts ) ) {
	            $family = $tempfamily;
	        } else {
	            $ot_google_fonts = get_theme_mod( 'ot_google_fonts', array() );
	            $family = $ot_google_fonts[$tempfamily]['family'];  
	        }
        }
    } else {
        $family = '';
    }
?>
<style type="text/css">

<?php if(isset($family) && !empty($family)){ ?>
    body,
    input[type="text"],
    input[type="password"],
    input[type="email"],
    textarea,
    select,
    input.newsletter,
    .map-box p,select#archives-dropdown--1, select#cat, select#categories-dropdown--1,
    .widget_search input.search-field, .widget_text select,.map-box p {
        font-family: "<?php echo $family; ?>";
    }
<?php } ?>
    body { <?php workscout_generate_typo_css($bodytypo); ?> }
    h1, h2, h3, h4, h5, h6  { <?php workscout_generate_typo_css($headerstypo); ?> }
    #logo h1 a, #logo h2 a { <?php workscout_generate_typo_css($logotypo); ?> }
    body .menu ul > li > a, body .menu ul li a {  <?php workscout_generate_typo_css($menutypo); ?>  }
   
    </style>
  <?php
}
add_action('wp_head', 'mobile_menu_css');


function workscout_stylesheet_content() { 

$maincolor = Kirki::get_option( 'workscout', 'pp_main_color' ); 
$mapheight = Kirki::get_option( 'workscout', 'pp_map_height', '400px' ); 
$logo_height = Kirki::get_option( 'workscout', 'pp_retina_logo_height',65 ); 

?>
<style type="text/css">

.current-menu-item > a,a.button.gray.app-link.opened,ul.float-right li a:hover,.menu ul li.sfHover a.sf-with-ul,.menu ul li a:hover,a.menu-trigger:hover,
.current-menu-parent a,#jPanelMenu-menu li a:hover,.search-container button,.upload-btn,button,input[type="button"],input[type="submit"],a.button,.upload-btn:hover,#titlebar.photo-bg a.button.white:hover,a.button.dark:hover,#backtotop a:hover,.mfp-close:hover,.woocommerce-MyAccount-navigation li.is-active a,.tabs-nav li.active a, .tabs-nav-o li.active a,.accordion h3.active-acc,.highlight.color, .plan.color-2 .plan-price,.plan.color-2 a.button,.tp-leftarrow:hover,.tp-rightarrow:hover,
.pagination ul li a.current-page,.woocommerce-pagination .current,.pagination .current,.pagination ul li a:hover,.pagination-next-prev ul li a:hover,
.infobox,.load_more_resumes,.job-manager-pagination .current,.hover-icon,.comment-by a.reply:hover,.chosen-container .chosen-results li.highlighted,
.chosen-container-multi .chosen-choices li.search-choice,.list-search button,.checkboxes input[type=checkbox]:checked + label:before, .listings-loader,
.widget_range_filter .ui-state-default,.tagcloud a:hover,.filter_by_tag_cloud a.active,.filter_by_tag_cloud a:hover,#wp-calendar tbody td#today,.footer-widget .tagcloud a:hover,.nav-links a:hover, .icon-box.rounded i:after, #mapnav-buttons a:hover,
.comment-by a.comment-reply-link:hover,#jPanelMenu-menu .current-menu-item > a, .button.color { background-color: <?php echo esc_attr($maincolor); ?>; }

a,table td.title a:hover,table.manage-table td.action a:hover,#breadcrumbs ul li a:hover,#titlebar span.icons a:hover,.counter-box i,
.counter,#popular-categories li a i,.list-1 li:before,.dropcap,.resume-titlebar span a:hover i,.resume-spotlight h4, .resumes-content h4,.job-overview ul li i,
.company-info span a:hover,.infobox a:hover,.meta-tags span a:hover,.widget-text h5 a:hover,.app-content .info span ,.app-content .info ul li a:hover,
table td.job_title a:hover,table.manage-table td.action a:hover,.job-spotlight span a:hover,.widget_rss li:before,.widget_rss li a:hover,
.widget_categories li:before,.widget-out-title_categories li:before,.widget_archive li:before,.widget-out-title_archive li:before,
.widget_recent_entries li:before,.widget-out-title_recent_entries li:before,.categories li:before,.widget_meta li:before,.widget_recent_comments li:before,
.widget_nav_menu li:before,.widget_pages li:before,.widget_categories li a:hover,.widget-out-title_categories li a:hover,.widget_archive li a:hover,
.widget-out-title_archive li a:hover,.widget_recent_entries li a:hover,.widget-out-title_recent_entries li a:hover,.categories li a:hover,
.widget_meta li a:hover,#wp-calendar tbody td a,.widget_nav_menu li a:hover,.widget_pages li a:hover,.resume-title a:hover, .company-letters a:hover, .companies-overview li li a:hover,.icon-box.rounded i, .icon-box i,
#titlebar .company-titlebar span a:hover{ color:  <?php echo esc_attr($maincolor); ?>; }
.icon-box.rounded i { border-color: <?php echo esc_attr($maincolor); ?>; }
.resumes li a:before,.resumes-list li a:before,.job-list li a:before,table.manage-table tr:before {	-webkit-box-shadow: 0px 1px 0px 0px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>,0.7);	-moz-box-shadow: 0px 1px 0px 0px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>,0.7);	box-shadow: 0px 1px 0px 0px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>,0.7);}
#popular-categories li a:before {-webkit-box-shadow: 0px 0px 0px 1px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>,0.7);-moz-box-shadow: 0px 0px 0px 1px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>,0.7);box-shadow: 0px 0px 0px 1px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>,0.7);}
table.manage-table tr:hover td,.resumes li:hover,.job-list li:hover { border-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>,0.7); }

table.manage-table tr:hover td,.resumes li:hover,.job-list li:hover, #popular-categories li a:hover { background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>,0.05); }


 #logo img {
    max-height: <?php echo $logo_height?>px;
}

#search_map {
	height: <?php echo $mapheight; ?>;
}

<?php $ordering = Kirki::get_option( 'workscout', 'pp_shop_ordering' ); 
if($ordering) { ?>
	.woocommerce-ordering { display: none; }
	.woocommerce-result-count { display: none; }
<?php } ?>

<?php echo ot_get_option( 'pp_custom_css' );  ?>

</style>

<?php }	



/**
 * Convert a hexa decimal color code to its RGB equivalent
 *
 * @param string $hexStr (hexadecimal color value)
 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
 */
function workscout_hex2rgb($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}
<?php 
// Begin Shortcodes
class WorkScoutShortcodes {
    
    function __construct() {
    
        //Initialize shortcodes
        add_action( 'init', array( $this, 'add_shortcodes' ) );
            
    }

    function add_shortcodes() {

        $shortcodes = array(
            'accordion',
            'accordion_wrap',
            'accordionwrap',
            'actionbox',
            'box',
            'box_job_categories',
            'box_resume_categories',
            'button',
            'centered_headline',
            'clear',
            'clients_carousel',
            'column',
            'counter',
            'counters',
            'dropcap',
            'headline',
            'icon',
            'iconbox',
            'infobanner',
            'jobs',
            'jobs_categories',
            'jobs_searchbox',
            'latest_from_blog',
            'liststyle',
            'list',
            'popup',
            'pricing_table',
            'pricing_woo_tables',
            'resume_categories',
            'skill_categories',
            'resumes',
            'simple_resumes',
            'spacer',
            'space',
            'spotlight_jobs',
            'spotlight_resumes',
            'tab',
            'tab_group',
            'tabgroup',
            'testimonials_wide',
            'vc_clients_carousel'
        );

        foreach ( $shortcodes as $shortcode ) {
            $function = 'workscout_' .  $shortcode ;
            if (!function_exists($function)) {
                include_once wp_normalize_path( dirname( __FILE__ ) . '/shortcodes/'.$shortcode.'.php' );
                add_shortcode( $shortcode, $function);
            }
        }
    }


}

new WorkScoutShortcodes();


/*
 * Helpers
 */
function workscout_string_to_bool( $value ) {
    return ( is_bool( $value ) && $value ) || in_array( $value, array( '1', 'true', 'yes' ) ) ? true : false;
}

function workscout_partition( $list, $p ) {
    $listlen = count( $list );
    $partlen = floor( $listlen / $p );
    $partrem = $listlen % $p;
    $partition = array();
    $mark = 0;
    for ($px = 0; $px < $p; $px++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice( $list, $mark, $incr );
        $mark += $incr;
    }
    return $partition;
}

/* Visual Composer Shortcodes*/
?>
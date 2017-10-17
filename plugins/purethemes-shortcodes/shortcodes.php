<?php


/**
* Clear shortcode
* Usage: [clear]
*/
if (!function_exists('pp_clear')) {
    function pp_clear() {
       return '<div class="clear"></div>';
    }
    add_shortcode( 'clear', 'pp_clear' );
}

/**
* Dropcap shortcode
* Usage: [dropcap color="gray"] [/dropcap]// margin-down margin-both
*/
if (!function_exists('pp_dropcap')) {
    function pp_dropcap($atts, $content = null) {
        extract(shortcode_atts(array(
            'color'=>''), $atts));
        return '<span class="dropcap '.$color.'">'.$content.'</span>';
    }
    add_shortcode('dropcap', 'pp_dropcap');
}

/**
* Accordion shortcode
* Usage: [accordion title="Tab"] [/accordion]
*/
if (!function_exists('pp_accordion')) {
    function pp_accordion( $atts, $content ) {
        extract(shortcode_atts(array(
            'title' => 'Tab'
            ), $atts));
        return '<h3><span class="ui-accordion-header-icon ui-icon ui-accordion-icon"></span>'.$title.'</h3><div><p>'.do_shortcode( $content ).'</p></div>';
    }
    add_shortcode( 'accordion', 'pp_accordion' );

    function pp_accordion_wrap( $atts, $content ) {
        extract(shortcode_atts(array(), $atts));
        return '<div class="accordion">'.do_shortcode( $content ).'</div>';
    }
    add_shortcode( 'accordionwrap', 'pp_accordion_wrap' );
}

/**
* Button shortcodes
* Usage: [button url="" color="light" customcolor="#eee" size="small" iconcolor="black" icon="icon-lock" ] click me  [/button]
*/
if (!function_exists('pp_button')) {
    function pp_button($atts, $content = null) {
        extract(shortcode_atts(array(
            "url" => '',
            "color" => 'color',  //gray color light
            "customcolor" => '',
            "iconcolor" => 'white',
            "icon" => '',
            "size" => '',
            "target" => '',
            "customclass" => '',
            ), $atts));
        $output = '<a class="button '.$size.' '.$color.' '.$customclass.'" href="'.$url.'" ';
        if(!empty($target)) { $output .= 'target="'.$target.'"'; }
        if(!empty($customcolor)) { $output .= 'style="background-color:'.$customcolor.'"'; }
        $output .= '>';
        if(!empty($icon)) { $output .= '<i class="'.$icon.'  '.$iconcolor.'"></i> '; }
        $output .= $content.'</a>';

        return $output;
    }
    add_shortcode('button', 'pp_button');
}

/**
* Tabs shortcodes
* Usage:
*/
if (!function_exists('etdc_tab_group')) {
    function etdc_tab_group( $atts, $content ) {
        $GLOBALS['pptab_count'] = 0;
        do_shortcode( $content );
        $count = 0;
        if( is_array( $GLOBALS['tabs'] ) ) {
            foreach( $GLOBALS['tabs'] as $tab ) {
                $count++;
                $tabs[] = '<li><a href="#tab'.$count.'">'.$tab['title'].'</a></li>';
                $panes[] = '<div class="tab-content" id="tab'.$count.'">'.$tab['content'].'</div>';
            }
            $return = "\n".'<ul class="tabs-nav">'.implode( "\n", $tabs ).'</ul>'."\n".'<div class="tabs-container">'.implode( "\n", $panes ).'</div>'."\n";
        }
        return $return;
    }

    /**
    * Usage: [tab title="" ] [/tab]
    */
    function etdc_tab( $atts, $content ) {
        extract(shortcode_atts(array(
            'title' => 'Tab %d',
            ), $atts));

        $x = $GLOBALS['pptab_count'];
        $GLOBALS['tabs'][$x] = array( 'title' => sprintf( $title, $GLOBALS['pptab_count'] ), 'content' =>  do_shortcode( $content ) );
        $GLOBALS['pptab_count']++;
    }
    add_shortcode( 'tabgroup', 'etdc_tab_group' );
    add_shortcode( 'tab', 'etdc_tab' );
}

/**
* Toggle shortcodes
* Usage: [toggle title="" open="no"] [/toggle]
*/
if (!function_exists('pp_toggle')) {
    function pp_toggle( $atts, $content ) {
        extract(shortcode_atts(array(
            'title' => '',
            'open' => 'no'
            ), $atts));
        if($open != 'no') { $opclass = "opened"; } else { $opclass = ''; }
        return ' <div class="toggle-wrap"><span class="trigger '.$opclass.'"><a href="#"><i class="toggle-icon"></i> '.$title.'</a></span><div class="toggle-container"><p>'.do_shortcode( $content ).'</p></div></div>';
    }
    add_shortcode( 'toggle', 'pp_toggle' );
}


?>
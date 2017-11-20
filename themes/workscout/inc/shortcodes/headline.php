<?php


/**
* Headline shortcode
* Usage: [headline ] [/headline] // margin-down margin-both
*/
function workscout_headline( $atts, $content ) {
  extract(shortcode_atts(array(
    'margintop' => 0,
    'marginbottom' => 25,
    'clearfix' => 0,
    'type' => 'h3'
    ), $atts));
  $output = '<'.$type.' class="margin-top-'.$margintop.' margin-bottom-'.$marginbottom.'">'.do_shortcode( $content ).'</'.$type.'>';
    if($clearfix == 1) {   $output .= '<div class="clearfix"></div>';}
    return $output;
}

?>
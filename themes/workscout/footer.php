<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WorkScout
 */

?>
<!-- Footer
================================================== -->
<div class="margin-top-45"></div>

<div id="footer">
<!-- Main -->
	<div class="container">
		<?php 
		$footer_layout = Kirki::get_option( 'workscout', 'pp_footer_widgets' ); 
        $footer_layout_array = explode(',', $footer_layout); 
        $x = 0;
        foreach ($footer_layout_array as $value) {
            $x++;
             ?>
             <div class="<?php echo esc_attr(workscout_number_to_width($value)); ?> columns">
                <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('footer'.$x)) : endif; ?>
            </div>
        <?php } ?>
	</div>

	<!-- Bottom -->
	<div class="container">
		<div class="footer-bottom">
			<div class="sixteen columns">
				
                <?php /* get the slider array */
                $footericons = ot_get_option( 'pp_footericons', array() );
                if ( !empty( $footericons ) ) {
                    echo '<h4>'.esc_html__('Follow us','workscout').'</h4>';
                    echo '<ul class="social-icons">';
                    foreach( $footericons as $icon ) {
                        echo '<li><a target="_blank" class="' . $icon['icons_service'] . '" title="' . esc_attr($icon['title']) . '" href="' . esc_url($icon['icons_url']) . '"><i class="icon-' . $icon['icons_service'] . '"></i></a></li>';
                    }
                    echo '</ul>';
                }
                ?>
				
				<div class="copyrights"><?php $copyrights = Kirki::get_option( 'workscout', 'pp_copyrights' ); 
		        if (function_exists('icl_register_string')) {
		            icl_register_string('Copyrights in footer','copyfooter', $copyrights);
		            echo icl_t('Copyrights in footer','copyfooter', $copyrights);
		        } else {
		            echo wp_kses($copyrights,array('br' => array(),'em' => array(),'strong' => array(),'a' => array('href' => array(),'title' => array())));
		        } ?></div>
			</div>
		</div>
	</div>

</div>

<!-- Back To Top Button -->
<div id="backtotop"><a href="#"></a></div>
<div id="ajax_response"></div>
</div>
<!-- Wrapper / End -->


<?php if ( is_page_template( 'template-contact.php' ) ) { ?>
<script type="text/javascript">
(function($){
    $(document).ready(function(){
        $('#googlemaps').gMap({
            maptype: '<?php echo ot_get_option('pp_contact_maptype','ROADMAP') ?>',
            scrollwheel: false,
            zoom: <?php echo ot_get_option('pp_contact_zoom',13) ?>,
            markers: [
                <?php $markers = ot_get_option('pp_contact_map');
                if(!empty($markers)) {
                    $allowed_tags = wp_kses_allowed_html( 'post' );
                    foreach ($markers as $marker) { 
                        $str = str_replace(array("\n", "\r"), '', $marker['content']);?>
                    {
                        address: '<?php echo esc_js($marker['address']); ?>', // Your Adress Here
                        html: '<strong style="font-size: 14px;"><?php echo esc_js($marker['title']); ?></strong></br><?php echo wp_kses($str,$allowed_tags); ?>',
                        popup: true,
                    },
                    <?php }
                } ?>
                    ],
                });
    });
})(this.jQuery);
</script>
<?php } //eof is_page_template ?>
<?php wp_footer(); ?>

</body>
</html>

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
            $role = '';
            if ( is_user_logged_in() ) {
                $user = new WP_User(get_current_user_id());
                $role = $user->roles[0];
            }

            if ( $role != "employer" && $role !="candidate"){
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

                <?php
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
                }
            }
            ?>
		
        </div>
</div>
</div>
<div><input type="hidden" id="job_id_hidden" name="job_id_hidden" value="" /></div>
<!-- Back To Top Button -->
<div id="backtotop"><a href="#"></a></div>
<div id="ajax_response"></div>
</div>
<!-- Wrapper / End --> 



<script>

jQuery('.fieldset-rate_min').remove();
jQuery('.page-id-691 .fieldset-candidate_location').insertAfter('.page-id-691 .fieldset-candidate_photo');

jQuery("#job_preview_submit_button").click(function(){
    var job_id =	jQuery('input[name="job_id"]').val();
    jQuery('#job_id_hidden').val(job_id);
    var value =   jQuery('#job_id_hidden').val();

});

jQuery('.archive ul.products .plan-price h3 a').removeAttr('href');
jQuery('.archive ul.products .plan-features a.button').remove();

var j = 1;
jQuery('.entry-summary .col-sm-4').each(function(i) {
jQuery(this).addClass("get_add-" + j);
j++;
});


var k = 1;
jQuery('#pa_package .attached').each(function(i) {
jQuery(this).addClass("get_add-" + j);
k++;
});
//jQuery('#resume_preview input[name="edit_resume"]').removeAttr('val');
//jQuery('#resume_preview input[name="continue"]').removeAttr('val');

jQuery('#resume_preview input[name="edit_resume"]').val("← I'd like to make edits");
jQuery('#resume_preview input[name="continue"]').val('let\'s go →');


jQuery(document).ready(function() {
	if(jQuery('body.single-job_listing p').hasClass('job-manager-message'))
	{
		 setTimeout(function() {
       window.location.href = "<?php echo get_site_url(); ?>/influencer.php"
      }, 5000);
	}

jQuery('.tax-product_cat article > ul.products > li:first-child').addClass('add_border');
jQuery('.tax-product_cat article > ul.products > li').click(function()
{
	jQuery('.tax-product_cat article > ul.products > li:first-child').removeClass('add_border');
	jQuery('.tax-product_cat article > ul.products > li').removeClass('add_border');
	jQuery(this).addClass('add_border');
	
});



});



jQuery(document).ready(function() {
	if(jQuery('body article p').hasClass('job-manager-message'))
	{
		 setTimeout(function() {
       window.location.href = "<?php echo get_site_url(); ?>/influencer.php"
      }, 5000);
	}

});


jQuery('.page-template-brand #container').removeClass('vc_row');

jQuery('.entry-summary .col-sm-4').click(function()
{
	jQuery('.entry-summary .col-sm-4').removeClass('border_outer');
	jQuery(this).addClass('border_outer');
var pacage_value=jQuery(this).attr('package') ;
		//alert(pacage_value);
	jQuery('#pa_package option').eq(pacage_value).prop('selected', true).trigger('change');
	
	//$('#mySelect option').eq(2).prop('selected', true).trigger('change');
	

});
jQuery('#plans .plan:first-child').addClass('border_outer');



jQuery('.archive  .products li.plan').click(function()
{
	jQuery('.archive  .products li.plan').removeClass('border_outer');
    //var label =jQuery(this).find('.plan-price h3 a').text();
	//var estimate = jQuery(this).find('.estimate').text();
	
	jQuery(this).addClass('border_outer');
	jQuery('.archive  .products li.plan .add_new_package').removeClass('add_new_package_open');
	jQuery(this).find('.add_new_package').toggleClass('add_new_package_open');
	
	
    var pacage_value=jQuery(this).attr('package') ;
		//alert(pacage_value);
	jQuery('.job_packages input[type="radio"]').val(pacage_value);
	jQuery('.job_packages input[type="radio"]').removeAttr('id');
	
	jQuery('.job_packages input[type="radio"]').attr('id','package-'+pacage_value);	
	//jQuery('.job_packages label').empty();
	//jQuery('.job_packages .estimate').empty();
	//jQuery('.job_packages .estimate').html(estimate);
	//jQuery('.job_packages label').html(label);
	
	 
	jQuery('.job_packages label').removeAttr('for');
	jQuery('.job_packages label').attr('id','package-'+pacage_value);
	//jQuery('#pa_package option').eq(pacage_value).prop('selected', true).trigger('change');
	//jQuery('.woocommerce-variation-add-to-cart .single_add_to_cart_button').trigger('click');
	//$('#mySelect option').eq(2).prop('selected', true).trigger('change');
	

});
jQuery('.job-application-submit-content').click(function()
{
	 jQuery('.chosen-select-no-single').val("new").trigger('change');
	 jQuery('.job-manager-form').submit();
});


</script>
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
                        popup: true
                    },
                    <?php }
                } ?>
                    ]
                });
    });
})(this.jQuery);
</script>
<?php } ?>
<?php

wp_footer();
?>

</body></html>
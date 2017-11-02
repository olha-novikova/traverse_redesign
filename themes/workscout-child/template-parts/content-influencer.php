<div class="carousel__influencer influencer-<?php the_ID();?>">
    <div class="carousel__influencer__top"></div>
    <div class="carousel__influencer__person"><?php output_candidate_photo(); ?>
        <p class="carousel__influencer__name"><a href="<?php echo get_permalink()?>"><?php the_title(); ?></a></p>
    </div>
    <div class="carousel__influencer__info">
        <div class="carousel__influencer__info-block">
            <p class="carousel__influencer__number"><?php echo output_candidate_campaigns_count( get_the_ID() ); ?></p>
            <p class="carousel__influencer__description"><?php echo  _n( 'Campaign', 'Campaigns', output_candidate_campaigns_count( get_post_field('post_author', get_the_ID() ) ) ); ?></p>
        </div>
        <div class="carousel__influencer__info-block">
            <p class="carousel__influencer__number"><?php echo output_candidate_audience( get_the_ID() );?></p>
            <p class="carousel__influencer__description">Audience</p>
        </div>
        <div class="carousel__influencer__info-block">
            <p class="carousel__influencer__number"><?php echo output_candidate_channels_count(get_the_ID());?></p>
            <p class="carousel__influencer__description"><?php echo  _n( 'Channel', 'Channels', output_candidate_channels_count(get_the_ID()) ); ?></p>
        </div>
    </div>
    <div class="carousel__influencer__buttons">
<div class="carousel__influencer__button carousel__influencer__button_star">
      
      
      
      <?php
global $resume_preview;

if ( $resume_preview ) {
	return;
}
$current_user = get_current_user_id();
$resume_author = get_post_field('post_author', $post->ID, 'db');
if ( $current_user == $resume_author ) return;
if ( resume_manager_user_can_view_contact_details( $post->ID ) ) :
	wp_enqueue_script( 'message-by-job' );
	?>
	<div class="resume_contact">
		
		<a href="#resume-dialog" class="small-dialog popup-with-zoom-anim button"><?php esc_html_e( 'Invite to Campaign', 'workscout' ); ?></a>
		<div id="resume-dialog" class="small-dialog zoom-anim-dialog mfp-hide apply-popup">
			<div class="small-dialog-headline">
				<h2><?php esc_html_e('Send Message to Candidate','workscout'); ?></h2>
			</div>
			<div class="small-dialog-content">
             <?php //do_action( 'resume_manager_contact_details' ); ?>
            <?php if(brand_has_listing()){?>
                <form class= "send_msg_by_jo_form">
                    <p>
                        <label>
                            Select listing
                            <br>
                            <?php  echo get_brand_listings_list( true ); ?>
                        </label>
                    </p>
                    <p>
                        <label>
                            Your Message
                            <br>
                            <span>
                            <textarea name="appl_message" class = "send_msg_by_job_text" cols="40" rows="10"></textarea>
                            </span>
                        </label>
                    </p>
                    <p>
                        <input type="hidden" name="resume_id" value="<?php echo $post->ID;?>">
                        <?php wp_nonce_field('my-nonce'); ?>
                        <input  value="Send" type="button" class = "send_msg_by_job">
                    </p>
                </form>
            <?php } else{?>
                <p><?php _e( 'Before contact to influencer you need to submit your <strong>lising</strong>. Click the button below to create.', 'wp-job-manager-resumes' ); ?></p>
                <p>
                    <?php
                    $submit_job_page = get_option('job_manager_submit_job_form_page_id');
                    if ( !empty($submit_job_page)) {  ?>
                        <a href="<?php echo get_permalink($submit_job_page) ?>" class="button"><?php esc_html_e('Add Listing','workscout'); ?></a>
                    <?php } ?>
                </p>
            <?php }?>
            <?php //echo do_shortcode('[contact-form-7 id="1459" title="Contact Candidate"]'); ?>
			</div>
			<?php //echo do_shortcode('[contact-form-7 id="1398" title="Contact Candidate"]'); ?>

		</div>
	</div>
<?php else : ?>

	<?php get_job_manager_template_part( 'access-denied', 'contact-details', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

<?php endif; ?>
  
  
  
  
  
  </div>
      
      
      <div class="carousel__influencer__button carousel__influencer__button_message"></div>
    </div>
</div>
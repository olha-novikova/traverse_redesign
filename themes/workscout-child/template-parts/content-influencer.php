<div class="carousel__influencer influencer-<?php the_ID();?>">
    <div class="carousel__influencer__top"></div>
    <div class="carousel__influencer__person"><?php output_candidate_photo('full'); ?>
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
		<div class="openinvitechat button" data-reciever-id="<?php echo $resume_author; ?>">
			<?php esc_html_e( 'Invite to Campaign', 'workscout' ); ?>
			<div class="chat__html" style="display:none;">
                <?php  
				$listinings_list = get_brand_listings_list( false ); 
				foreach ($listinings_list->posts as $post )
				{
					$theid = $post->ID;
                    $thename = $post->post_title;
					
					echo '<option value="'.$theid.'">'.$thename;
                    if (user_has_applied_for_job( $resume_author, $theid ) ) 
						echo ' <b> - applied for this job</b>';
					echo '</option>';
					
				}
                wp_reset_postdata();
				?>
			</div>
		</div>
	</div>
<?php else : ?>

	<?php get_job_manager_template_part( 'access-denied', 'contact-details', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

<?php endif; ?>
  </div>
  </div>
</div>
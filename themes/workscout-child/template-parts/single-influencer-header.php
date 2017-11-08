<section class="section_profile">
	<div class="profile__background">
		<div style="overflow: hidden" class="profile__button profile__button_add">
            <?php the_candidate_photo('full', get_template_directory_uri().'/images/candidate.png'); ?></div>
		<div class="profile__data">
			<?php 	
				$candidate_name = get_post_meta($post->ID, '_candidate_name', true); 
				$resume_author = get_post_field('post_author', $post->ID, 'db');
			?>
			<p class="profile__data___name"><?php echo $candidate_name ?></p>
			<p class="profile__data__geo"><?php echo get_the_candidate_location($post) ?></p>
		</div>
		<div class="profile__button profile__button_favourite openinvitechat " data-reciever-id="<?php echo $resume_author; ?>">
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
<!--		<div class="profile__button profile__button_message"></div>-->
	</div>
<!--	<div class="profile__action">-->
<!--		<ul class="profile__links">-->
<!--			<li class="profile__link"><a href="#" class="profile__link__a">About</a></li>-->
<!--			<li class="profile__link"><a href="#" class="profile__link__a">Photos</a></li>-->
<!--			<li class="profile__link"><a href="#" class="profile__link__a">Videos</a></li>-->
<!--		</ul>-->
<!--	</div>-->
</section>
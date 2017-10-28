<section class="section_profile">
	<div class="profile__background">
		<div style="overflow: hidden" class="profile__button profile__button_add"><?php the_candidate_photo('workscout-resume', get_template_directory_uri().'/images/candidate.png'); ?></div>
		<div class="profile__data">
			<?php $candidate_name = get_post_meta($post->ID, '_candidate_name', true); ?>
			<p class="profile__data___name"><?php echo $candidate_name ?></p>
			<p class="profile__data__geo"><?php echo get_the_candidate_location($post) ?></p>
		</div>
		<div class="profile__button profile__button_favourite">

		</div>
		<div class="profile__button profile__button_message"></div>
	</div>
<!--	<div class="profile__action">-->
<!--		<ul class="profile__links">-->
<!--			<li class="profile__link"><a href="#" class="profile__link__a">About</a></li>-->
<!--			<li class="profile__link"><a href="#" class="profile__link__a">Photos</a></li>-->
<!--			<li class="profile__link"><a href="#" class="profile__link__a">Videos</a></li>-->
<!--		</ul>-->
<!--	</div>-->
</section>
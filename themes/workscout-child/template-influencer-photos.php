<?php

/**
 * Template Name: Single Influencer Photos
 * Template Post Type: resume
 */

$photo_samples = array_shift(get_post_meta( $post->ID, '_photo_sample'));

get_header('new');

get_sidebar();
while ( have_posts() ) : the_post();
	?>
	<main class="main">
		<?php get_template_part('template-parts/single-influencer-header')?>

		<div class="content">
			<div class="section section__photo">
				<div class="section__container">
					<p class="section__container__header">John Doe's Photos</p>
					<div class="buttons"><a href="#" class="button button__more button__more_photos"></a><a href="#" class="button button__more button__more_albums"></a><a href="#" class="button button__more">
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="6px" class="button__svg">
								<path fill-rule="evenodd" d="M20.890,5.984 C19.308,5.984 18.025,4.703 18.025,3.121 C18.025,1.539 19.308,0.257 20.890,0.257 C22.472,0.257 23.755,1.539 23.755,3.121 C23.755,4.703 22.472,5.984 20.890,5.984 ZM12.295,5.984 C10.713,5.984 9.430,4.703 9.430,3.121 C9.430,1.539 10.713,0.257 12.295,0.257 C13.877,0.257 15.160,1.539 15.160,3.121 C15.160,4.703 13.877,5.984 12.295,5.984 ZM3.700,5.984 C2.118,5.984 0.835,4.703 0.835,3.121 C0.835,1.539 2.118,0.257 3.700,0.257 C5.283,0.257 6.565,1.539 6.565,3.121 C6.565,4.703 5.283,5.984 3.700,5.984 Z"></path>
							</svg></a></div>
				</div>
				<div class="photos"><img src="#" alt="" class="photo photo_long"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/><img src="#" alt="" class="photo"/></div>
				<div class="load-more">
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="6px" class="button__svg">
						<path fill-rule="evenodd" d="M20.890,5.984 C19.308,5.984 18.025,4.703 18.025,3.121 C18.025,1.539 19.308,0.257 20.890,0.257 C22.472,0.257 23.755,1.539 23.755,3.121 C23.755,4.703 22.472,5.984 20.890,5.984 ZM12.295,5.984 C10.713,5.984 9.430,4.703 9.430,3.121 C9.430,1.539 10.713,0.257 12.295,0.257 C13.877,0.257 15.160,1.539 15.160,3.121 C15.160,4.703 13.877,5.984 12.295,5.984 ZM3.700,5.984 C2.118,5.984 0.835,4.703 0.835,3.121 C0.835,1.539 2.118,0.257 3.700,0.257 C5.283,0.257 6.565,1.539 6.565,3.121 C6.565,4.703 5.283,5.984 3.700,5.984 Z"></path>
					</svg>
				</div>
			</div>
		</div>
	</main>
	<?php
endwhile; // End of the loop.
get_footer('new');
?>



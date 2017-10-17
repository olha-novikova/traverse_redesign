<?php 
/**
* 
*/
class WorkScoutMaps 
{
	
	protected $plugin_slug = 'workscout-map';

	function __construct() {

		add_shortcode( 'workscout-map', array( $this, 'show_map' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_register_script( $this->plugin_slug . '-script',  get_template_directory_uri() . '/js/workscout.big.map.min.js', array( 'jquery' ),'1.0', true );
	}

	public function show_map($atts){
		extract(shortcode_atts(array(
			'class' => '',
			'type' => 'job_listing',
			'height' => '450',
			), $atts));

	
		$query_args = array( 
			 	'post_type'              => $type,
        		'post_status'            => 'publish',
        		'posts_per_page'		 => -1,
			);

		
		$markers = array();
		// The Loop
		 $wp_query = new WP_Query( $query_args );
   		if ( $wp_query->have_posts() ):
			$i = 0;
			while( $wp_query->have_posts() ) : 
				$wp_query->the_post(); 
				
				$lat = $wp_query->post->geolocation_lat;
				$id = $wp_query->post->ID;
					if (!empty($lat)) {
					    
						$title = get_the_title();
						$ibcontet = '';
						ob_start();
						if($type == 'resume'){ //type resume 
							?>
						<a href="<?php the_permalink(); ?>">
							<?php the_candidate_photo(); ?>
							<div class="resumes-content">
								<h4><?php the_title(); ?> <?php the_candidate_title( '<span>', '</span> ' ); ?></h4>
								<span><i class="fa fa-map-marker"></i> <?php the_candidate_location( false ); ?></span>
								<?php $rate = get_post_meta( $id, '_rate_min', true );
								if(!empty($rate)) { ?>
									<span class="icons"><i class="fa fa-money"></i> <?php echo get_workscout_currency_symbol();  echo get_post_meta( $id, '_rate_min', true ); ?> <?php esc_html_e('/ hour','workscout') ?></span>
								<?php } ?>
						
								<?php if ( ( $skills = wp_get_object_terms( $id, 'resume_skill', array( 'fields' => 'names' ) ) ) && is_array( $skills ) ) : ?>
									<div class="skills">
										<?php echo '<span>' . implode( '</span><span>', $skills ) . '</span>'; ?>
									</div>
									<div class="clearfix"></div>
								<?php endif; ?>
							</div>
						</a>
						<?php } else { //type job
							?>
						<a href="<?php the_job_permalink(); ?>">
							<div class="job-list-content">
								<h4><?php the_title(); ?> 
								<?php if ( get_option( 'job_manager_enable_types' ) ) { ?><span class="job-type <?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->slug ) : ''; ?>"><?php the_job_type(); ?></span><?php } ?>
								<?php if(workscout_newly_posted()) { echo '<span class="new_job">'.esc_html__('NEW','workscout').'</span>'; } ?>
								</h4>

								<div class="job-icons">
									<?php do_action( 'workscout_job_listing_meta_start' ); ?>
									<span class="ws-meta-company-name"><i class="fa fa-briefcase"></i> <?php the_company_name();?></span>
									<span class="ws-meta-job-location"><i class="fa fa-map-marker"></i> <?php ws_job_location( false ); ?></span>
									<?php 
									$rate_min = get_post_meta($id, '_rate_min', true ); 
									if ( $rate_min) { 
										$rate_max = get_post_meta($id, '_rate_max', true );  ?>
										<span class="ws-meta-rate">
											<i class="fa fa-money"></i> <?php echo get_workscout_currency_symbol(); echo esc_html( $rate_min ); if(!empty($rate_max)) { echo '- '.get_workscout_currency_symbol().$rate_max; } ?> <?php esc_html_e('/ hour','workscout'); ?>
										</span>
									<?php } ?>

									<?php 
									$salary_min = get_post_meta($id, '_salary_min', true ); 
									if ( $salary_min ) {
										$salary_max = get_post_meta($id, '_salary_max', true );  ?>
										<span class="ws-meta-salary">
											<i class="fa fa-money"></i>
											<?php echo get_workscout_currency_symbol(); echo esc_html( $salary_min ) ?> <?php if(!empty($salary_max)) { echo '- '.get_workscout_currency_symbol().$salary_max; } ?>
										</span>
									<?php } ?>
									<?php do_action( 'workscout_job_listing_meta_end' ); ?>
								</div>
							</div>
						</a>
						<?php 
						}
						$ibcontet =  ob_get_clean();
						$ibdata = $ibcontet.'<div class="infoBox-close"><i class="fa fa-times"></i></div>';
						$ibmergecontent = '<li><a href="'.esc_url(get_permalink()).'">'.$title.'</a></li>';
						$mappoint = array(
							'lat' =>  $lat,
							'lng' =>  $wp_query->post->geolocation_long,
							'id' => $i,
							'ibcontent' => $ibdata,
							'ibmergecontent' => $ibmergecontent,
							'ismerged' => 'no' 	
					);

					// check if such element exists in the array
					$matching_index = $this->find_matching_location($markers, $mappoint);
					if ($matching_index !== null) { // if it exists then change pointdata
					    $markers[$matching_index]['ibmergecontent'] = $markers[$matching_index]['ibmergecontent'] . $mappoint['ibmergecontent'];
					    $markers[$matching_index]['ismerged'] = "yes";
					} else { // otherwise add it to main array
					    $markers[] = $mappoint;
					    $i++;
					}
				}

			 endwhile;
	    
	    endif; 
    	wp_reset_postdata();

		wp_enqueue_script( 'google-maps-js-api' );
		wp_enqueue_script( $this->plugin_slug . '-script' );
		wp_localize_script( $this->plugin_slug . '-script', 'ws_big_map', $markers );

		$output = '';
		$output .= '<div id="map-container" class="'.esc_attr($class).'">';
		$output .= '	<div id="ws-map" style="height:'.esc_attr($height).'px;" >
					        <!-- map goes here -->
					    </div>
					    <ul id="mapnav-buttons" class="behind">
						    <li><a href="#" id="prevpoint" title="'.esc_attr__('Previous Point On Map','workscout').'">'.esc_html__('Prev','workscout').'</a></li>
						    <li><a href="#" id="nextpoint" title="'.esc_attr__('Next Point On Map','workscout').'">'.esc_html__('Next','workscout').'</a></li>
						</ul>
					</div>';

		return $output;
		;
	}


	private function find_matching_location($haystack, $needle) {

	    foreach ($haystack as $index => $a) {

	        if ($a['lat'] == $needle['lat']
	                && $a['lng'] == $needle['lng']
	              ) {
	            return $index;
	        }
	    }
	    return null;
	}

}
new WorkScoutMaps();
?>
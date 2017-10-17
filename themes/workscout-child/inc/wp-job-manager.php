<?php 


add_post_type_support( 'job_listing', 'excerpt' );
add_post_type_support( 'resume', 'excerpt' );

function workscout_job_manager_output_jobs_defaults( $defaults ) {
        $job_page = get_option('job_manager_jobs_page_id');
		if(!empty($job_page) && is_page($job_page)){
        	$defaults[ 'show_filters' ] = false;
        }
        return $defaults;
    }
add_filter( 'job_manager_output_jobs_defaults','workscout_job_manager_output_jobs_defaults');


/*
 * adding rate | hours | salary fields for jobs edit/submit
 */
add_filter( 'submit_job_form_fields', 'workscout_frontend_add_rate_field' );
function workscout_frontend_add_rate_field( $fields ) {
  $currency = get_workscout_currency_symbol();

  if(get_option('workscout_enable_filter_rate')) : 
	  $fields['job']['rate_min'] = array(
	    'label'       => esc_html__( 'Newsletter', 'workscout' ).' ('.$currency.')',
	    'type'        => 'checkbox',
	    'required'    => false,
	    'placeholder' => esc_html__( '' ),
	    'priority'    => 7
	  );  
	  $fields['job']['rate_max'] = array(
	    'label'       => esc_html__( 'PHOTO CAMPAIGN', 'workscout' ).' ('.$currency.')',
	    'type'        => 'checkbox',
	    'required'    => false,
	    'placeholder' => esc_html__( 't' ),
	    'priority'    => 8
	  );
  endif; 

  if(get_option('workscout_enable_filter_salary')) :
	  $fields['job']['salary_min'] = array(
	    'label'       => esc_html__( 'Video Campaign', 'workscout' ).' ('.$currency.')',
	    'type'        => 'checkbox',
	    'required'    => false,
	    'placeholder' => esc_html__( 'e.g. 20000','workscout' ),
	    'priority'    => 9
	  );  
	  $fields['job']['salary_max'] = array(
	    'label'       => esc_html__( 'Assets Availbale', 'workscout' ).' ('.$currency.')',
	    'type'        => 'checkbox',
	    'required'    => false,
	    'placeholder' => esc_html__( 'e.g. 50000', 'workscout' ),
	    'priority'    => 10
	  );
  endif; 

  $fields['job']['hours'] = array(
    'label'       => esc_html__( 'Hours per week', 'workscout' ),
    'type'        => 'checkbox',
    'required'    => false,
    'placeholder' => esc_html__( 'e.g. 40', 'workscout' ),
    'priority'    => 11
  );
  $fields['job']['apply_link'] = array(
    'label'       => esc_html__( 'External "Apply for Job" link', 'workscout' ),
    'type'        => 'text',
    'required'    => false,
    'placeholder' => esc_html__( 'http://', 'workscout' ),
    'priority'    => 12
  );    
  $fields['job']['header_image'] = array(
		'label'       => __( 'Header Image', 'workscout' ),
		'type'        => 'file',
		'required'    => false,
		'description' => __( 'The header image size should be at least 1750x425px', 'workscout' ),
		'priority'    => 13,
		'ajax'        => true,
		'multiple'    => false,
		'allowed_mime_types' => array(
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'gif'  => 'image/gif',
			'png'  => 'image/png'
		)
	);

  return $fields;
}


/**
 * Save the extra frontend fields
 *
 * @since WorkScout 1.0.2
 *
 * @return void
 */
function workscout_job_manager_update_job_data( $job_id, $values ) {
	update_post_meta( $job_id, '_rate_min', $values[ 'job' ][ 'rate_min' ] );
	update_post_meta( $job_id, '_rate_max', $values[ 'job' ][ 'rate_max' ] );
	update_post_meta( $job_id, '_salary_min', $values[ 'job' ][ 'salary_min' ] );
	update_post_meta( $job_id, '_salary_max', $values[ 'job' ][ 'salary_max' ] );
	update_post_meta( $job_id, '_hours', $values[ 'job' ][ 'hours' ] );
	update_post_meta( $job_id, '_apply_link', $values[ 'job' ][ 'apply_link' ] );
	update_post_meta( $job_id, 'pp_job_header_bg', $values[ 'job' ][ 'header_image' ] );
	//update_post_meta( $job_id, '_hide_expiration', $values[ 'job' ][ 'hide_expiration' ] );
	
}
add_action( 'job_manager_update_job_data', 'workscout_job_manager_update_job_data', 10, 2 );


/* Add rate rate | hours | salary fields for job listing*/
add_filter( 'job_manager_job_listing_data_fields', 'workscout_admin_add_rate_field' );

function workscout_admin_add_rate_field( $fields ) {
	$currency = get_workscout_currency_symbol();
	$fields['_hours'] = array(
	    'label'       => esc_html__( 'Hours per week', 'workscout' ),
	    'type'        => 'text',
	    'placeholder' => 'e.g. 40',
	    'description' => ''
  	);
  	if(get_option('workscout_enable_filter_rate')) : 
		$fields['_rate_min'] = array(
		    'label'       => esc_html__( 'Rate/h (minimum)', 'workscout' ),
		    'type'        => 'text',
		    'placeholder' => esc_html__( 'e.g. 20', 'workscout' ),
		    'description' => esc_html__('Put just a number','workscout'),
		);    
		$fields['_rate_max'] = array(
		    'label'       => esc_html__( 'Rate/h (maximum) ', 'workscout' ),
		    'type'        => 'text',
		    'placeholder' => esc_html__('e.g. 20','workscout'),
		    'description' => esc_html__('Put just a number - you can leave it empty and set only minimum rate value ','workscout'),
		); 
	endif; 
	if(get_option('workscout_enable_filter_salary')) :
		$fields['_salary_min'] = array(
		    'label'       => esc_html__( 'Salary min', 'workscout' ).' ('.$currency.')',
		    'type'        => 'text',
		    'placeholder' => esc_html__('e.g. 20.000','workscout'),
		    'description' => esc_html__('Put just a number','workscout'),
		);   
		$fields['_salary_max'] = array(
		    'label'       => esc_html__( 'Salary max', 'workscout' ).' ('.$currency.')',
		    'type'        => 'text',
		    'placeholder' => esc_html__('e.g. 50.000','workscout'),
		    'description' => esc_html__('Maximum of salary range you can offer - you can leave it empty and set only minimum salary ','workscout'),
	  	); 	
  	endif;
  	$fields['_apply_link'] = array(
	    'label'       => esc_html__( 'External "Apply for Job" link', 'workscout' ),
	    'type'        => 'text',
	    'placeholder' => esc_html__('http://','workscout'),
	    'description' => esc_html__('If the job applying is done on external page, here\'s the place to put link to that page - it will be used instead of standard Apply form','workscout'),
  	);   	
  	$fields['_hide_expiration'] = array(
	    'label'       => esc_html__( 'Hide "Expiration date"', 'workscout' ),
	    'type'        => 'checkbox',
	    'std' 		  => 0,
	    'priority'    => 12,
	    'description' => esc_html__('Hide the Listing Expiry Date  from job details','workscout'),
  	); 
 

  return $fields;
}



add_filter( 'job_manager_get_listings', 'workscout_filter_by_company', 10, 2 );
function workscout_filter_by_company( $query_args, $args ) {

	if ( isset( $_POST['form_data'] ) ) {
		parse_str( $_POST['form_data'], $form_data );
		if( isset( $form_data['company_field']) ) {
			$query_args['meta_query'][] = array(
			 	array(
	                'key'   => '_company_name',
	                'value' => $form_data['company_field'],
	                'compare' => '='
	            )
	        );
	        add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
		} else {
			add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
		}

	}
	return $query_args;

}


/**
 * This code gets your posted field and modifies the job search query
 */
add_filter( 'job_manager_get_listings', 'workscout_filter_by_salary_field_query_args', 10, 2 );

function workscout_filter_by_salary_field_query_args( $query_args, $args ) {
	if ( isset( $_POST['form_data'] ) ) {
		parse_str( $_POST['form_data'], $form_data );

		// If this is set, we are filtering by salary
		if( isset( $form_data['filter_by_salary_check']) ) {
			if ( ! empty( $form_data['filter_by_salary'] ) ) {
				$selected_range = sanitize_text_field( $form_data['filter_by_salary'] );
					
					$range = array_map( 'absint', explode( '-', $selected_range ) );
				 	$query_args['meta_query'][] = array(
					 	'relation' => 'OR',
					        array(
					            'relation' => 'OR',
					            array(
	                                'key' => '_salary_min',
	                                'value' => $range,
	                                'compare' => 'BETWEEN',
	                                'type' => 'NUMERIC',
	                            ),
	                            array(
	                                'key' => '_salary_max',
	                                'value' => $range,
	                                'compare' => 'BETWEEN',
	                                'type' => 'NUMERIC',
	                            ),
					 
					        ),
					        array(
					            'relation' => 'AND',
					            array(
	                                'key' => '_salary_min',
	                                'value' => $range[0],
	                                'compare' => '<=',
	                                'type' => 'NUMERIC',
	                            ),
	                            array(
	                                'key' => '_salary_max',
	                                'value' => $range[1],
	                                'compare' => '>=',
	                                'type' => 'NUMERIC',
	                            ),
					 
					        ),
			        );
			
				// This will show the 'reset' link
				add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
			}
		} else {
			add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
		}
	}
	return $query_args;

}


function workscout_array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

/**
 * This code gets your posted field and modifies the job search query
 */
add_filter( 'job_manager_get_listings', 'workscout_filter_by_rate_field_query_args', 10, 2 );

function workscout_filter_by_rate_field_query_args( $query_args, $args ) {
	if ( isset( $_POST['form_data'] ) ) {
		parse_str( $_POST['form_data'], $form_data );

		// If this is set, we are filtering by salary
		if( isset( $form_data['filter_by_rate_check'])) {
			if ( ! empty( $form_data['filter_by_rate'] ) ) {
				$selected_range = sanitize_text_field( $form_data['filter_by_rate'] );
			
					$range = array_map( 'absint', explode( '-', $selected_range ) );
				 	$query_args['meta_query'][] = array(
					 	'relation' => 'OR',
					        array(
					            'relation' => 'OR',
					            array(
	                                'key' => '_rate_min',
	                                'value' => $range,
	                                'compare' => 'BETWEEN',
	                                'type' => 'NUMERIC',
	                            ),
	                            array(
	                                'key' => '_rate_max',
	                                'value' => $range,
	                                'compare' => 'BETWEEN',
	                                'type' => 'NUMERIC',
	                            ),
					 
					        ),
					        array(
					            'relation' => 'AND',
					            array(
	                                'key' => '_rate_min',
	                                'value' => $range[0],
	                                'compare' => '<=',
	                                'type' => 'NUMERIC',
	                            ),
	                            array(
	                                'key' => '_rate_max',
	                                'value' => $range[1],
	                                'compare' => '>=',
	                                'type' => 'NUMERIC',
	                            ),
					 
					        ),
			        );
			

				// This will show the 'reset' link
				add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
			}
		}
	}
	return $query_args;

}




/**
 * This code gets your posted field and modifies the job search query
 */
add_filter( 'job_manager_get_listings', 'workscout_filter_by_radius_query_args', 10, 2 );

function workscout_filter_by_radius_query_args( $query_args, $args ) {
	if ( isset( $_POST['form_data'] ) ) {
		parse_str( $_POST['form_data'], $form_data );

		// If this is set, we are filtering by salary
		if( isset( $form_data['search_radius']) && !empty( $form_data['search_radius'] ) ) {
			if ( ! empty( $form_data['search_radius'] ) ) {
				$address = $form_data['search_location'];
				$radius = $form_data['search_radius'];
				$radius_type = $form_data['radius_type'];
				if(!empty($address)) {
					$latlng = workscout_geocode($address);
					$nearbyposts = workscout_get_nearby_jobs($latlng[0], $latlng[1], $radius, $radius_type ); 
					workscout_array_sort_by_column($nearbyposts,'distance');

					$ids = array_unique(array_column($nearbyposts, 'post_id'));
					if(!empty($ids)) {
						$query_args['post__in'] = $ids;
						unset( $query_args['meta_query'][0]); //this should remove location search - needs further testing
					}
				}

				// This will show the 'reset' link
				add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
			}
		} else {
			add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
		}
	}

	return $query_args;
}



function workscout_get_nearby_jobs($lat, $lng, $distance, $radius_type){
    global $wpdb;
    if($radius_type=='km') {
    	$ratio = 6371;
    } else {
    	$ratio = 3959;
    }

  	$post_ids = 
			$wpdb->get_results(
				$wpdb->prepare( "
			SELECT DISTINCT
			 		geolocation_lat.post_id,
			 		geolocation_lat.meta_key,
			 		geolocation_lat.meta_value as jobLat,
			        geolocation_long.meta_value as jobLong,
			        ( %d * acos( cos( radians( %f ) ) * cos( radians( geolocation_lat.meta_value ) ) * cos( radians( geolocation_long.meta_value ) - radians( %f ) ) + sin( radians( %f ) ) * sin( radians( geolocation_lat.meta_value ) ) ) ) AS distance 
		       
			 	FROM 
			 		$wpdb->postmeta AS geolocation_lat
			 		LEFT JOIN $wpdb->postmeta as geolocation_long ON geolocation_lat.post_id = geolocation_long.post_id
					WHERE geolocation_lat.meta_key = 'geolocation_lat' AND geolocation_long.meta_key = 'geolocation_long'
			 		HAVING distance < %d

		 	", 
		 	$ratio, 
		 	$lat, 
		 	$lng, 
		 	$lat, 
		 	$distance)
		,ARRAY_A);
    return $post_ids;
 
}


/* Resumes related code*/



if(get_option('workscout_enable_resume_filter_rate')) :
	/*
	 * adding rate field for jobs edit/submit
	 */
	add_filter( 'submit_resume_form_fields', 'workscout_frontend_add_resume_rate_field' );
	function workscout_frontend_add_resume_rate_field( $fields ) {
		$currency = get_workscout_currency_symbol();
		  $fields['resume_fields']['rate_min'] = array(
		    'label'       => esc_html__( 'Minimum rate/h', 'workscout' ).' ('.$currency.')',
		    'type'        => 'text',
		    'required'    => false,
		    'placeholder' => esc_html__('e.g. 20','workscout'),
		    'priority'    => 7
		  );   
		 		  
		  return $fields;
	}



	add_filter( 'resume_manager_resume_fields', 'workscout_admin_add_resume_rate_field' );

	function workscout_admin_add_resume_rate_field( $fields ) {
		$currency = get_workscout_currency_symbol();
		$fields['_rate_min'] = array(
		    'label'       => esc_html__( 'Rate/h (minimum)', 'workscout' ).' ('.$currency.')',
		    'type'        => 'text',
		    'placeholder' => esc_html__('e.g. 20','workscout'),
		    'description' => 'Put just a number'
		);    
	  return $fields;
	}


endif;



/**
 * This code gets your posted field and modifies the job search query
 */
add_filter( 'resume_manager_get_resumes', 'workscout_filter_resumes_by_radius_query_args', 10, 2 );

function workscout_filter_resumes_by_radius_query_args( $query_args, $args ) {
	if ( isset( $_POST['form_data'] ) ) {
		parse_str( $_POST['form_data'], $form_data );

		// If this is set, we are filtering by salary
		if( isset( $form_data['search_radius']) && !empty( $form_data['search_radius'] ) ) {
			if ( ! empty( $form_data['search_radius'] ) ) {
				$address = $form_data['search_location'];
				$radius = $form_data['search_radius'];
				$radius_type = $form_data['radius_type'];
				if(!empty($address)) {
					$latlng = workscout_geocode($address);
					$nearbyposts = workscout_get_nearby_jobs($latlng[0], $latlng[1], $radius, $radius_type ); 
					workscout_array_sort_by_column($nearbyposts,'distance');

					$ids = array_unique(array_column($nearbyposts, 'post_id'));
					if(!empty($ids)) {
						$query_args['post__in'] = $ids;
						unset( $query_args['meta_query'][0]); //this should remove location search - needs further testing
					}
				}

				// This will show the 'reset' link
				add_filter( 'resume_manager_get_resumes_custom_filter', '__return_true' );
			}
		} else {
			add_filter( 'resume_manager_get_resumes_custom_filter', '__return_true' );
		}
	}

	return $query_args;
}

/**
 * This code gets your posted field and modifies the job search query
 */
add_filter( 'resume_manager_get_resumes', 'workscout_resume_filter_by_rate_field_query_args', 10, 2 );

function workscout_resume_filter_by_rate_field_query_args( $query_args, $args ) {
	if ( isset( $_POST['form_data'] ) ) {
		parse_str( $_POST['form_data'], $form_data );

		// If this is set, we are filtering by salary
		if( isset( $form_data['filter_by_rate_check'])) {
			if ( ! empty( $form_data['filter_by_rate'] ) ) {
				$selected_range = sanitize_text_field( $form_data['filter_by_rate'] );
			
				 	$query_args['meta_query'][] = array(
						'key'     => '_rate_min',
						'value'   => array_map( 'absint', explode( '-', $selected_range ) ),
						'compare' => 'BETWEEN',
						'type'    => 'NUMERIC'
					);
			

				// This will show the 'reset' link
				add_filter( 'resume_manager_get_resumes_custom_filter', '__return_true' );
			}
		}
	}
	return $query_args;

}


/**
 * This code gets your posted field and modifies the resume search query
 */
add_filter( 'resume_manager_get_resumes', 'workscout_filter_by_skills_field_query_args', 10, 2 );

function workscout_filter_by_skills_field_query_args( $query_args, $args ) {
	if ( isset( $_POST['form_data'] ) ) {
		parse_str( $_POST['form_data'], $form_data );

		// If this is set, we are filtering by salary
		if( isset( $form_data['search_skills'])) {
			if ( ! empty( $form_data['search_skills'] ) ) {
					
					$field    = is_numeric( $form_data['search_skills'][0] ) ? 'term_id' : 'slug';
					$operator = 'all' === sizeof( $form_data['search_skills'] ) > 1 ? 'AND' : 'IN';
		
					$query_args['tax_query'][] = array(
						'taxonomy'         => 'resume_skill',
						'field'            => $field,
						'terms'            => array_values( $form_data['search_skills'] ),
						'include_children' => $operator !== 'AND' ,
						'operator'         => $operator
					);
				// This will show the 'reset' link
				add_filter( 'resume_manager_get_resumes_custom_filter', '__return_true' );
				
			}
		}
	}
	return $query_args;

}

/*
 * Custom Icon field for Job Categories taxonomy 
 **/

// Add term page
function workscout_job_listing_category_add_new_meta_field() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[fa_icon]"><?php esc_html_e( 'Category Icon', 'workscout' ); ?></label>
			<select class="workscout-icon-select" name="term_meta[fa_icon]" id="term_meta[fa_icon]" id="">
		
			<?php 
			 	$faicons = workscout_icons_list();
			   	foreach ($faicons as $key => $value) {
			   		echo '<option value="fa fa-'.$key.'">'.$value.'</option>';
			   	}
			 	$imicons = workscout_line_icons_list();
			   	foreach ($imicons as $key ) {
			   		echo '<option value="ln ln-'.$key.'">'.$key.'</option>';
			   	}
		   	?>  
			</select>
		<p class="description"><?php esc_html_e( 'Icon will be displayed in categories grid view','workscout' ); ?></p>
	</div>
	<div class="form-field">
		<label for="term_meta[upload_icon]"><?php esc_html_e( 'Custom image icon for category', 'workscout' ); ?></label>
		<input type="text" name="term_meta[upload_icon]" id="term_meta[upload_icon]" value="">
		<p class="description"><?php esc_html_e( 'This is alternative for font based icons','workscout' ); ?></p>
	</div>	
	<div class="form-field">
		<label for="term_meta[upload_icon]"><?php esc_html_e( 'Background image for category header', 'workscout' ); ?></label>
		<input type="text" name="term_meta[upload_header]" id="term_meta[upload_header]" value="">
		<p class="description"><?php esc_html_e( 'Similar to the single jobs you can add image to the category header. It should be 1920px wide','workscout' ); ?></p>
	</div>

	
		
<?php
}
add_action( 'job_listing_category_add_form_fields', 'workscout_job_listing_category_add_new_meta_field', 10, 2 );
add_action( 'resume_category_add_form_fields', 'workscout_job_listing_category_add_new_meta_field', 10, 2 );


// Edit term page
function workscout_job_listing_category_edit_meta_field($term) {
 
	// put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); 
	 ?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[fa_icon]"><?php esc_html_e( 'Category Icon', 'workscout' ); ?></label>
		
		<td>
			<select class="workscout-icon-select" name="term_meta[fa_icon]" id="term_meta[fa_icon]">
				
			<?php 

			 	$faicons = workscout_icons_list();
			 	
			   	foreach ($faicons as $key => $value) {

			   		echo '<option value="fa fa-'.$key.'" ';
			   		if(isset($term_meta['fa_icon']) && $term_meta['fa_icon'] == 'fa fa-'.$key) { echo ' selected="selected"';}
			   		echo '>'.$value.'</option>';
			   	}
		   		$imicons = workscout_line_icons_list();
		   		
			   	foreach ($imicons as $key ) {
			   		echo '<option value="ln ln-'.$key.'" ';
			   			if(isset($term_meta['fa_icon']) && $term_meta['fa_icon'] == 'ln ln-'.$key) { echo ' selected="selected"';}
			   		echo '>'.$key.'</option>';
			   	}
			   ?>

			</select>
			<p class="description"><?php esc_html_e( 'Icon will be displayed in categories grid view','workscout' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[upload_icon]"><?php esc_html_e( 'Custom image icon for category', 'workscout' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[upload_icon]" id="term_meta[upload_icon]" value="<?php echo esc_attr( $term_meta['upload_icon'] ) ? esc_attr( $term_meta['upload_icon'] ) : ''; ?>">
			<p class="description"><?php esc_html_e( 'This is alternative for font based icons','workscout' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[upload_header]"><?php esc_html_e( 'Background image for category header', 'workscout' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[upload_header]" id="term_meta[upload_header]" value="<?php echo esc_attr( $term_meta['upload_header'] ) ? esc_attr( $term_meta['upload_header'] ) : ''; ?>">
			<p class="description"><?php esc_html_e( 'Similar to the single jobs you can add image to the category header. Put here direct link to the image. It should be 1920px wide','workscout' ); ?></p>
		</td>
	</tr>
<?php
}
add_action( 'job_listing_category_edit_form_fields', 'workscout_job_listing_category_edit_meta_field', 10, 2 );
add_action( 'resume_category_edit_form_fields', 'workscout_job_listing_category_edit_meta_field', 10, 2 );


// Save extra taxonomy fields callback function.
function workscout_save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  
add_action( 'edited_job_listing_category', 'workscout_save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_job_listing_category', 'workscout_save_taxonomy_custom_meta', 10, 2 );
add_action( 'edited_resume_category', 'workscout_save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_resume_category', 'workscout_save_taxonomy_custom_meta', 10, 2 );

remove_shortcode('jobs');
remove_shortcode('resumes');

function workscout_manage_table_icons($val){
	switch ($val) {
		
		case 'resume-title':
			$icon = '<i class="fa fa-user"></i> ';
			break;
		case 'candidate-title':
		case 'job_title':
			$icon = '<i class="fa fa-file-text"></i> ';
			break;
		case 'filled':
			$icon = '<i class="fa fa-check-square-o"></i> ';
			break;
		case 'date':
			$icon = '<i class="fa fa-calendar"></i> ';
			break;
		case 'expires':
			$icon = '<i class="fa fa-calendar"></i> ';
			break;
		case 'candidate-location':
			$icon = '<i class="fa fa-map-marker"></i> ';
			break;
		
		default:
			$icon = '';
			break;
	}
	return $icon;
}

function workscout_manage_action_icons($val){
	switch ($val) {
		
		case 'view':
			$icon = '<i class="fa fa-check-circle-o"></i> ';
			break;	
		case 'email':
			$icon = '<i class="fa fa-envelope"></i> ';
			break;		
		case 'toggle_status':
			$icon = '<i class="fa fa-eye-slash"></i> ';
			break;
		case 'delete':
			$icon = '<i class="fa fa-remove"></i> ';
			break;
		case 'hide':
			$icon = '<i class="fa fa-eye-slash"></i> ';
			break;
		case 'edit':
			$icon = '<i class="fa fa-pencil"></i> ';
			break;
		case 'mark_filled':
			$icon = '<i class="fa  fa-check "></i> ';
			break;		
		case 'publish':
			$icon = '<i class="fa  fa-eye "></i> ';
			break;

		case 'mark_not_filled':
			$icon = '<i class="fa  fa-minus "></i> ';
			break;
		default:
			$icon = '';
			break;
	}
	return $icon;
}


/* sending user to sign up to Login page if exists */
add_filter( 'submit_job_form_login_url', 'workscout_custom_login_url' );
add_filter( 'job_manager_job_dashboard_login_url', 'workscout_custom_login_url' );
add_filter( 'submit_resume_form_login_url', 'workscout_custom_login_url' );
add_filter( 'resume_manager_candidate_dashboard_login_url', 'workscout_custom_login_url' );
add_filter( 'job_manager_alerts_login_url', 'workscout_custom_login_url' );
add_filter( 'job_manager_bookmark_form_login_url', 'workscout_custom_login_url' );
add_filter( 'job_manager_job_applications_login_required_message', 'workscout_custom_login_url' );

 
function workscout_custom_login_url() {
	$loginpage = ot_get_option('pp_login_page');

	$login_system = Kirki::get_option( 'workscout', 'pp_login_form_system' );
	
	switch ($login_system) {
		case 'custom':
			$loginpage 		= Kirki::get_option( 'workscout', 'pp_login_custom_login' );
			break;

		case 'woocommerce':
			$loginpage 		= get_option('woocommerce_myaccount_page_id'); 
			
			break;

		case 'um':
			$loginpage 	 	= um_get_option('core_login');
			break;					

		case 'workscout':
			$loginpage 		= Kirki::get_option( 'workscout', 'pp_login_workscout_page' );
			
			break;
		
		default:
			$loginpage = wp_login_url( get_permalink() );
			break;
	}
	return get_permalink($loginpage);
		
	
}
	
/*remove bookmarks link*/
if ( class_exists( 'WP_Job_Manager_Bookmarks' ) ) {
	global $job_manager_bookmarks;
	remove_action( 'single_job_listing_meta_after', array( $job_manager_bookmarks, 'bookmark_form' ) );
	remove_action( 'single_resume_start', array( $job_manager_bookmarks, 'bookmark_form' ) );

	add_action( 'workscout_bookmark_hook', array( $job_manager_bookmarks, 'bookmark_form' ) );
	add_action( 'workscout_bookmark_hook', array( $job_manager_bookmarks, 'bookmark_form' ) );
}

/* register with role */

add_action( 'register_form', 'workscout_register_form' );

function workscout_register_form() {

    global $wp_roles;
    echo '<label for="role">'.esc_html__('I’m an/a…','workscout').' <span class="required"> *</span></label>';
    //echo '<select name="role" class="input chosen-select">';
    //echo '<option value="brand">'.esc_html__("A BRAND/ DMO","workscout").'</option>';
    //echo '<option value="influencer">'.esc_html__("AN INFLUENCER","workscout").'</option>';
    //echo '</select>';
	echo '<select name="role" class="input chosen-select" required>';
        echo '<option value="" disabled selected style="display: none;">'.esc_html__("Choose Option......","workscout").'</option>';
        echo '<option value="candidate">Influencer</option>';
        echo '<option value="employer">Brand/ DMO</option>';
	echo '</select>';

    if ( ! empty( $_POST['role'] ) ) {
        update_user_meta( $user_id, 'role', trim( $_POST['role'] ) );
    }
	
}


//2. Add validation.
add_filter( 'registration_errors', 'workscout_registration_errors', 10, 3 );
function workscout_registration_errors( $errors, $sanitized_user_login, $user_email ) {

    if ( empty( $_POST['role'] ) || ! empty( $_POST['role'] ) && trim( $_POST['role'] ) == '' ) {
         $errors->add( 'role', esc_html__( '<strong>ERROR</strong>: You must include a role.', 'workscout' ) );
    }

    return $errors;
}

//3. Finally, save our extra registration user meta.
add_action( 'user_register', 'workscout_user_register' );
function workscout_user_register( $user_id ) {
	if(isset($_POST['role'])){
   		$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $_POST['role'] ) );
   	}
}


function workscout_get_rating_class($average) {
	if(!$average) {
			$class="no-stars";
	} else {
		switch ($average) {
			
			case $average >= 1 && $average < 1.5:
				$class="one-stars";
				break;
			case $average >= 1.5 && $average < 2:
				$class="one-and-half-stars";
				break;
			case $average >= 2 && $average < 2.5:
				$class="two-stars";
				break;
			case $average >= 2.5 && $average < 3:
				$class="two-and-half-stars";
				break;
			case $average >= 3 && $average < 3.5:
				$class="three-stars";
				break;
			case $average >= 3.5 && $average < 4:
				$class="three-and-half-stars";
				break;
			case $average >= 4 && $average < 4.5:
				$class="four-stars";
				break;
			case $average >= 4.5 && $average < 5:
				$class="four-and-half-stars";
				break;
			case $average >= 5:
				$class="five-stars";
				break;

			default:
				$class="no-stars";
				break;
		}
	}
	return $class;
	}

function get_workscout_currency_symbol( $currency = '' ) {
	if ( ! $currency ) {
		$currency = get_option('workscout_currency_setting');
	}

	switch ( $currency ) {
		case 'BHD' :
			$currency_symbol = '.د.ب';
			break;
		case 'AED' :
			$currency_symbol = 'د.إ';
			break;
		case 'AUD' :
		case 'ARS' :
		case 'CAD' :
		case 'CLP' :
		case 'COP' :
		case 'HKD' :
		case 'MXN' :
		case 'NZD' :
		case 'SGD' :
		case 'USD' :
			$currency_symbol = '&#36;';
			break;
		case 'BDT':
			$currency_symbol = '&#2547;&nbsp;';
			break;
		case 'LKR':
			$currency_symbol = '&#3515;&#3540;&nbsp;';
			break;
		case 'BGN' :
			$currency_symbol = '&#1083;&#1074;.';
			break;
		case 'BRL' :
			$currency_symbol = '&#82;&#36;';
			break;
		case 'CHF' :
			$currency_symbol = '&#67;&#72;&#70;';
			break;
		case 'CNY' :
		case 'JPY' :
		case 'RMB' :
			$currency_symbol = '&yen;';
			break;
		case 'CZK' :
			$currency_symbol = '&#75;&#269;';
			break;
		case 'DKK' :
			$currency_symbol = 'DKK';
			break;
		case 'DOP' :
			$currency_symbol = 'RD&#36;';
			break;
		case 'EGP' :
			$currency_symbol = 'EGP';
			break;
		case 'EUR' :
			$currency_symbol = '&euro;';
			break;
		case 'GBP' :
			$currency_symbol = '&pound;';
			break;
		case 'HRK' :
			$currency_symbol = 'Kn';
			break;
		case 'HUF' :
			$currency_symbol = '&#70;&#116;';
			break;
		case 'IDR' :
			$currency_symbol = 'Rp';
			break;
		case 'ILS' :
			$currency_symbol = '&#8362;';
			break;
		case 'INR' :
			$currency_symbol = 'Rs.';
			break;
		case 'ISK' :
			$currency_symbol = 'Kr.';
			break;
		case 'KIP' :
			$currency_symbol = '&#8365;';
			break;
		case 'KRW' :
			$currency_symbol = '&#8361;';
			break;
		case 'MYR' :
			$currency_symbol = '&#82;&#77;';
			break;
		case 'NGN' :
			$currency_symbol = '&#8358;';
			break;
		case 'NOK' :
			$currency_symbol = '&#107;&#114;';
			break;
		case 'NPR' :
			$currency_symbol = 'Rs.';
			break;
		case 'PHP' :
			$currency_symbol = '&#8369;';
			break;
		case 'PLN' :
			$currency_symbol = '&#122;&#322;';
			break;
		case 'PYG' :
			$currency_symbol = '&#8370;';
			break;
		case 'RON' :
			$currency_symbol = 'lei';
			break;
		case 'RUB' :
			$currency_symbol = '&#1088;&#1091;&#1073;.';
			break;
		case 'SEK' :
			$currency_symbol = '&#107;&#114;';
			break;
		case 'THB' :
			$currency_symbol = '&#3647;';
			break;
		case 'TRY' :
			$currency_symbol = '&#8378;';
			break;
		case 'TWD' :
			$currency_symbol = '&#78;&#84;&#36;';
			break;
		case 'UAH' :
			$currency_symbol = '&#8372;';
			break;
		case 'VND' :
			$currency_symbol = '&#8363;';
			break;
		case 'ZAR' :
			$currency_symbol = '&#82;';
			break;
		case 'ZMK' :
			$currency_symbol = 'ZK';
			break;
		default :
			$currency_symbol = '';
			break;
	}

	return apply_filters( 'woocommerce_currency_symbol', $currency_symbol, $currency );
}
/*
 * Adds Settings for Job Manager Options
 */
add_filter( 'job_manager_settings', 'workscout_job_manager_settings' );

function workscout_job_manager_settings($settings = array()){
	$settings['job_listings'][1][] = array(
		'name'    => 'workscout_currency_setting',
		'std'     => 'USD',
		'label'   => 'Currency Symbol',
		'desc'    => 'Select the currency symbol that will be used in salary/rate fields',
		'type'    => 'select',
		'options' => array(
			'none' => esc_html__( 'Disable Currency Symbol', 'workscout' ),
			'USD' => esc_html__( 'US Dollars', 'workscout' ),
			'AED' => esc_html__( 'United Arab Emirates Dirham', 'workscout' ),
			'ARS' => esc_html__( 'Argentine Peso', 'workscout' ),
			'AUD' => esc_html__( 'Australian Dollars', 'workscout' ),
			'BDT' => esc_html__( 'Bangladeshi Taka', 'workscout' ),
			'BHD' => esc_html__( 'Bahraini Dinar', 'workscout' ),
			'BRL' => esc_html__( 'Brazilian Real', 'workscout' ),
			'BGN' => esc_html__( 'Bulgarian Lev', 'workscout' ),
			'CAD' => esc_html__( 'Canadian Dollars', 'workscout' ),
			'CLP' => esc_html__( 'Chilean Peso', 'workscout' ),
			'CNY' => esc_html__( 'Chinese Yuan', 'workscout' ),
			'COP' => esc_html__( 'Colombian Peso', 'workscout' ),
			'CZK' => esc_html__( 'Czech Koruna', 'workscout' ),
			'DKK' => esc_html__( 'Danish Krone', 'workscout' ),
			'DOP' => esc_html__( 'Dominican Peso', 'workscout' ),
			'EUR' => esc_html__( 'Euros', 'workscout' ),
			'HKD' => esc_html__( 'Hong Kong Dollar', 'workscout' ),
			'HRK' => esc_html__( 'Croatia kuna', 'workscout' ),
			'HUF' => esc_html__( 'Hungarian Forint', 'workscout' ),
			'ISK' => esc_html__( 'Icelandic krona', 'workscout' ),
			'IDR' => esc_html__( 'Indonesia Rupiah', 'workscout' ),
			'INR' => esc_html__( 'Indian Rupee', 'workscout' ),
			'NPR' => esc_html__( 'Nepali Rupee', 'workscout' ),
			'ILS' => esc_html__( 'Israeli Shekel', 'workscout' ),
			'JPY' => esc_html__( 'Japanese Yen', 'workscout' ),
			'KIP' => esc_html__( 'Lao Kip', 'workscout' ),
			'KRW' => esc_html__( 'South Korean Won', 'workscout' ),
			'LKR' => esc_html__( 'Sri Lankan Rupee', 'workscout' ),
			'MYR' => esc_html__( 'Malaysian Ringgits', 'workscout' ),
			'MXN' => esc_html__( 'Mexican Peso', 'workscout' ),
			'NGN' => esc_html__( 'Nigerian Naira', 'workscout' ),
			'NOK' => esc_html__( 'Norwegian Krone', 'workscout' ),
			'NZD' => esc_html__( 'New Zealand Dollar', 'workscout' ),
			'PYG' => esc_html__( 'Paraguayan Guaraní', 'workscout' ),
			'PHP' => esc_html__( 'Philippine Pesos', 'workscout' ),
			'PLN' => esc_html__( 'Polish Zloty', 'workscout' ),
			'GBP' => esc_html__( 'Pounds Sterling', 'workscout' ),
			'RON' => esc_html__( 'Romanian Leu', 'workscout' ),
			'RUB' => esc_html__( 'Russian Ruble', 'workscout' ),
			'SGD' => esc_html__( 'Singapore Dollar', 'workscout' ),
			'ZAR' => esc_html__( 'South African rand', 'workscout' ),
			'SEK' => esc_html__( 'Swedish Krona', 'workscout' ),
			'CHF' => esc_html__( 'Swiss Franc', 'workscout' ),
			'TWD' => esc_html__( 'Taiwan New Dollars', 'workscout' ),
			'THB' => esc_html__( 'Thai Baht', 'workscout' ),
			'TRY' => esc_html__( 'Turkish Lira', 'workscout' ),
			'UAH' => esc_html__( 'Ukrainian Hryvnia', 'workscout' ),
			'USD' => esc_html__( 'US Dollars', 'workscout' ),
			'VND' => esc_html__( 'Vietnamese Dong', 'workscout' ),
			'EGP' => esc_html__( 'Egyptian Pound', 'workscout' ),
			'ZMK' => esc_html__( 'Zambian Kwacha', 'workscout' )
		)
	);
	$settings['job_listings'][1][] = array(
			'name' 		=> 'workscout_enable_filter_salary',
			'std' 		=> '',
			'label' 	=> esc_html__( 'Filter jobs by Salary', 'workscout' ),
			'cb_label'  => esc_html__( 'Enable filter option on Jobs page', 'workscout' ),
			'desc'		=> esc_html__( 'Enabling this option will show a salary range filter in sidebar on Jobs page.', 'workscout' ),
			'type'      => 'checkbox'
	);	
	$settings['job_listings'][1][] = array(
			'name' 		=> 'workscout_enable_filter_rate',
			'std' 		=> '',
			'label' 	=> esc_html__( 'Filter jobs by Rate', 'workscout' ),
			'cb_label'  => esc_html__( 'Enable filter option on Jobs page', 'workscout' ),
			'desc'		=> esc_html__( 'Enabling this option will show a rate range filter in sidebar on Jobs page.', 'workscout' ),
			'type'      => 'checkbox'
	);	

	$settings['job_listings'][1][] = array(
			'name' 		=> 'workscout_enable_location_sidebar',
			'std' 		=> '1',
			'label' 	=> esc_html__( 'Location field on Jobs page', 'workscout' ),
			'cb_label'  => esc_html__( 'Show location search field on Jobs page sidebar', 'workscout' ),
			'desc'		=> esc_html__( 'Uncheck to hide', 'workscout' ),
			'type'      => 'checkbox'
	);	
	$settings['job_listings'][1][] = array(
			'name' 		=> 'workscout_enable_job_types_sidebar',
			'std' 		=> '1',
			'label' 	=> esc_html__( 'Job Types field on Jobs page', 'workscout' ),
			'cb_label'  => esc_html__( 'Show Job Types field on Jobs page sidebar', 'workscout' ),
			'desc'		=> esc_html__( 'Uncheck to hide', 'workscout' ),
			'type'      => 'checkbox'
	);	
	if ( taxonomy_exists( "job_listing_tag" )) {
		$settings['job_listings'][1][] = array(
				'name' 		=> 'workscout_enable_job_tags_sidebar',
				'std' 		=> '1',
				'label' 	=> esc_html__( 'Job Tags filter on Jobs page', 'workscout' ),
				'cb_label'  => esc_html__( 'Show Job Tags filter on Jobs page sidebar', 'workscout' ),
				'desc'		=> esc_html__( 'Uncheck to hide', 'workscout' ),
				'type'      => 'checkbox'
		);
	}
	return $settings;
}

/*
 * Adds Settings for  Resumes Options
 */
add_filter( 'resume_manager_settings', 'workscout_resume_manager_settings' );

function workscout_resume_manager_settings($settings = array()){
	$settings['resume_listings'][1][] = array(
			'name' 		=> 'workscout_enable_resume_filter_rate',
			'std' 		=> '',
			'label' 	=> esc_html__( 'Filter Resumes by Rate', 'workscout' ),
			'cb_label'  => esc_html__( 'Enable filter option on Resumes page', 'workscout' ),
			'desc'		=> esc_html__( 'Enabling this option will show a salary range filter in sidebar on Resumes page.', 'workscout' ),
			'type'      => 'checkbox'
	);

	$settings['resume_listings'][1][] = array(
			'name' 		=> 'workscout_enable_resume_comments',
			'std'        => '0',
			'label' 	=> esc_html__( 'Enable comments on Resumes', 'workscout' ),
			'cb_label'  => esc_html__( 'Enable comments on Resumes', 'workscout' ),
			'desc'		=> esc_html__( 'Check to enable comments section on Resumes.', 'workscout' ),
			'type'      => 'checkbox',
			'attributes' => array()
	);

	return $settings;
}


if(!function_exists('workscout_newly_posted')) {
	function workscout_newly_posted() {
		global $post;
		$now = date('U'); $published = get_the_time('U');
		$new = false;
		// set to 48 hours in seconds 
		if( $now-$published  <= 2*24*60*60 ) $new = true;
		return $new;
	}
}


// Add comment support to the post type
add_filter( 'register_post_type_resume', 'register_post_type_resume_enable_comments' );

function register_post_type_resume_enable_comments( $post_type ) {
	$post_type['supports'][] = 'comments';
	return $post_type;
}

// Make comments open by default for new resumes
add_filter( 'submit_resume_form_save_resume_data', 'workscout_custom_submit_resume_form_save_resume_data' );

function workscout_custom_submit_resume_form_save_resume_data( $data ) {
	$data['comment_status'] = 'open';
	return $data;
}




function custom_job_manager_get_listings_result($result, $jobs) {
	$result['post_count'] = $jobs->found_posts;
	return $result;
}
add_filter( 'job_manager_get_listings_result', 'custom_job_manager_get_listings_result',10,2 );



 function workscout_get_company_link( $company_name ) {
		global $wp_rewrite;
		$slug = apply_filters( 'wp_job_manager_companies_company_slug', __( 'company', 'workscout' ) );
		$company_name = rawurlencode( $company_name );

		if ( $wp_rewrite->permalink_structure == '' ) {
			$url = home_url( 'index.php?'. $slug . '=' . $company_name );
		} else {
			$url = home_url( '/' . $slug . '/' . trailingslashit( $company_name ) );
		}

		return '<a href="'.esc_url( $url ).'">';
	}

/*
function has_active_job_package_capability_check( $allcaps, $cap, $args ) {
	// Only interested in has_active_job_package
	if ( empty( $cap[0] ) || $cap[0] !== 'has_active_job_package' || ! function_exists( 'wc_paid_listings_get_user_packages' ) ) {
		return $allcaps;
	}

	$user_id  = $args[1];
	$packages = wc_paid_listings_get_user_packages( $user_id, 'job_listing' );

	// Has active package
	if ( is_array( $packages ) && sizeof( $packages ) > 0 ) {
		$allcaps[ $cap[0] ] = true;
	}

	return $allcaps;
}

add_filter('job_manager_candidates_can_apply','block_applying');
function block_applying($can_apply ){
	if(current_user_can( 'has_active_job_package' )) {
		$can_apply = true;
	} else {
		$can_apply = false;
	}
	return $can_apply;

}*/


add_filter( 'job_manager_geolocation_endpoint', 'workscout_add_geolocation_key_to_endpoint' ); 
function workscout_add_geolocation_key_to_endpoint( $endpoint ) { 

	$api_key = Kirki::get_option( 'workscout','pp_maps_browser_api', '');
	if(!empty($api_key)) {
		$endpoint = add_query_arg( 'key', $api_key, $endpoint ); 
		$endpoint = str_ireplace('http:', 'https:', $endpoint);
	}
	return $endpoint; 
}

// function to geocode address, it will return false if unable to geocode address
function workscout_geocode($address){
 
    // url encode the address
    $address = urlencode($address);
    $api_key = Kirki::get_option( 'workscout','pp_maps_browser_api', '');
    // google map geocode api url
    $url = "https://maps.google.com/maps/api/geocode/json?address='.$address.'&key=".$api_key;
 
    // get the json response
    $resp_json = file_get_contents($url);
     
    // decode the json
    $resp = json_decode($resp_json, true);

    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
             
            return $data_arr;
             
        }else{
            return false;
        }
         
    }else{
        return false;
    }
}


function ws_job_location(  $map_link = true, $post = null  ) {
	if(!$post) { global $post; }
 	if ( get_option( 'job_manager_enable_regions_filter' ) ) {
		if ( is_singular( 'job_listing' ) &&  false != get_the_term_list( $post->ID, 'job_listing_region' )  ) {
			echo get_the_term_list( $post->ID, 'job_listing_region', '', ', ', '' );
		} else {
			
			$terms = wp_get_object_terms( $post->ID, 'job_listing_region', array( 'orderby' => 'term_order', 'order' => 'desc') );
			if ( ! empty( $terms ) ) {
				if ( ! is_wp_error( $terms ) ) 
					$resultstr = array();{
					if ( $map_link ) {
						foreach( $terms as $term ) {
							$resultstr[] = ' <a href="' . get_term_link( $term->slug, 'job_listing_region' ) . '">' . esc_html( $term->name ) . '</a>'; 
						}
					} else {
						foreach( $terms as $term ) {
							$resultstr[] = ' '. esc_html( $term->name ); 
						}
					}
					$result = implode(",",$resultstr);
					echo $result;
					
				}
			} else {
				$location = get_post_meta($post->ID, '_job_location', TRUE); 

				if ( $location ) {
					if ( $map_link ) {
						// If linking to google maps, we don't want anything but text here
						echo apply_filters( 'the_job_location_map_link', '<a class="google_map_link" href="' . esc_url( 'http://maps.google.com/maps?q=' . urlencode( strip_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ) . '" target="_blank">' . esc_html( strip_tags( $location ) ) . '</a>', $location, $post );
					} else {
						echo wp_kses_post( $location );
					}
				} else {
					echo wp_kses_post( apply_filters( 'the_job_location_anywhere_text', __( 'Anywhere', 'wp-job-manager' ) ) );
				}
			}
		}

	} else {
		$location = get_the_job_location( $post );

		if ( $location ) {
			if ( $map_link ) {
				// If linking to google maps, we don't want anything but text here
				echo apply_filters( 'the_job_location_map_link', '<a class="google_map_link" href="' . esc_url( 'http://maps.google.com/maps?q=' . urlencode( strip_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ) . '" target="_blank">' . esc_html( strip_tags( $location ) ) . '</a>', $location, $post );
			} else {
				echo wp_kses_post( $location );
			}
		} else {
			echo wp_kses_post( apply_filters( 'the_job_location_anywhere_text', __( 'Anywhere', 'wp-job-manager' ) ) );
		}
	}
}

?>

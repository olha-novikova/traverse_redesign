jQuery( document ).ready( function ( $ ) {

	var xhr = [];

	$( '.job_listings' ).on( 'update_results', function ( event, page, append, loading_previous ) {
		var data         = '';
		var target       = $( this );
		var form         = $( '.job_filters' );
		var showing      = ( 'h2.showing_jobs' );
		var results      = target.find( '.job_listings' );
		var per_page     = target.data( 'per_page' );
		var orderby      = target.data( 'orderby' );
		var order        = target.data( 'order' );
		var featured     = target.data( 'featured' );
		var filled       = target.data( 'filled' );
		var index        = $( 'div.job_listings' ).index(this);
		var spinner 	 = $('.listings-loader');

		if ( index < 0 ) {
			return;
		}

		if ( xhr[index] ) {
			xhr[index].abort();
		}

		if ( ! append ) {
			$( results ).addClass( 'loading' );
			$(spinner).fadeIn();
			//$( 'li.job_listing, li.no_job_listings_found', results ).css( 'visibility', 'hidden' );

			// Not appending. If page > 1, we should show a load previous button so the user can get to earlier-page listings if needed
			if ( page > 1 && true != target.data( 'show_pagination' ) ) {
				$( results ).before( '<a class="load_more_jobs load_previous" href="#"><strong>' + job_manager_ajax_filters.i18n_load_prev_listings + '</strong></a>' );
			} else {
				target.find( '.load_previous' ).remove();
			}

			target.find( '.load_more_jobs' ).data( 'page', page );
		}

		
		var filter_job_type = [];

		$( ':input[name="filter_job_type[]"]:checked, :input[name="filter_job_type[]"][type="hidden"], :input[name="filter_job_type"]', form ).each( function () {
			filter_job_type.push( $( this ).val() );
		
		} );

		var categories = form.find( ':input[name^="search_categories"]' ).map( function () {
		return $( this ).val();
		} ).get();
		var keywords   = '';
		var location   = '';
		var $keywords  = form.find( ':input[name="search_keywords"]' );
		var $location  = form.find( ':input[name="search_location"]' );
		// Workaround placeholder scripts
		if ( $keywords.val() !== $keywords.attr( 'placeholder' ) ) {
			keywords = $keywords.val();
		}

		if ( $location.val() !== $location.attr( 'placeholder' ) ) {
			location = $location.val();
		}

		data = {
			lang: job_manager_ajax_filters.lang,
			search_keywords: keywords,
			search_location: location,
			search_categories: categories,
			filter_job_type: filter_job_type,
			per_page: per_page,
			orderby: orderby,
			order: order,
			page: page,
			featured: featured,
			filled: filled,
			show_pagination: target.data( 'show_pagination' ),
			form_data: form.serialize()
		};
	

		xhr[index] = $.ajax( {
			type: 'POST',
			url: job_manager_ajax_filters.ajax_url.toString().replace( "%%endpoint%%", "get_listings" ),
			data: data,
			success: function ( result ) {
				
				if ( result ) {
					
					try {
						if ( result.showing ) {
							$( showing ).show().html(  result.showing );
							$('.sidebar .job_filters_links').html( result.showing_links )
							
						} else {
							$( showing ).hide();
							$('.sidebar .job_filters_links').hide();
						}
						$('#titlebar .count_jobs').html(result.post_count);


						if ( result.showing_all ) {
							$( showing ).addClass( 'wp-job-manager-showing-all' );
						} else {
							$( showing ).removeClass( 'wp-job-manager-showing-all' );
						}

						if ( result.html ) {
							if ( append && loading_previous ) {
								$( results ).prepend( result.html );
							} else if ( append ) {
								$( results ).append( result.html );
							} else {
								$( results ).html( result.html );
							}
							
						}

						if ( true == target.data( 'show_pagination' ) ) {
							target.find('.job-manager-pagination').remove();

							if ( result.pagination ) {
								target.append( result.pagination );
							}
						} else {
							if ( ! result.found_jobs || result.max_num_pages <= page ) {
								$( '.load_more_jobs:not(.load_previous)', target ).hide();
							} else if ( ! loading_previous ) {
								$( '.load_more_jobs', target ).show();
							}
							$( '.load_more_jobs i', target ).removeClass( 'fa fa-refresh fa-spin' ).addClass('fa fa-plus-circle');
							$( 'li.job_listing', results ).css( 'visibility', 'visible' );
						}

						$( results ).removeClass( 'loading' );
						$(spinner).fadeOut();
						target.triggerHandler( 'updated_results', result );

					} catch ( err ) {
						if ( window.console ) {
							console.log( err );
						}
					}
				}
			},
			error: function ( jqXHR, textStatus, error ) {
				if ( window.console && 'abort' !== textStatus ) {
					console.log( textStatus + ': ' + error );
				}
			},
			statusCode: {
				404: function() {
					if ( window.console ) {
						console.log( "Error 404: Ajax Endpoint cannot be reached. Go to Settings > Permalinks and save to resolve." );
					}
				}
			}
		} );
	} );


	/*salary slider*/
	var current_min_price = parseInt( job_manager_ajax_filters.salary_min, 10 ),
    current_max_price = parseInt( job_manager_ajax_filters.salary_max, 10 );
    
    $( "#salary-range" ).slider({
      range: true,
      min: parseInt(job_manager_ajax_filters.salary_min,10),
      max: parseInt(job_manager_ajax_filters.salary_max,10),
      values: [ current_min_price, current_max_price ],
      step: 1000,
      slide: function( event, ui ) {
        $( "input#salary_amount" ).val(  ui.values[ 0 ] + "-" + ui.values[ 1 ] );
        $( ".salary_amount .from" ).text(job_manager_ajax_filters.currency+ui.values[ 0 ]);
        $( ".salary_amount .to" ).text(job_manager_ajax_filters.currency+ui.values[ 1 ]);
      }
    });
    
    $( ".salary_amount .from" ).text(job_manager_ajax_filters.currency + $( "#salary-range" ).slider( "values", 0 ));
    $( ".salary_amount .to" ).text(job_manager_ajax_filters.currency + $( "#salary-range" ).slider( "values", 1 ));
    $( "input#salary_amount" ).val(  $( "#salary-range" ).slider( "values", 0 ) + "-" + $( "#salary-range" ).slider( "values", 1 ) );
    /* eof salary slider*/

    /* rate slider */
	var current_min_price = parseInt( job_manager_ajax_filters.rate_min, 10 ),
    current_max_price = parseInt( job_manager_ajax_filters.rate_max, 10 );
    
    $( "#rate-range" ).slider({
      range: true,
      min: parseInt(job_manager_ajax_filters.rate_min,10),
      max: parseInt(job_manager_ajax_filters.rate_max,10),
      values: [ current_min_price, current_max_price ],
      step: 10,
      slide: function( event, ui ) {
        $( "input#rate_amount" ).val(  ui.values[ 0 ] + "-" + ui.values[ 1 ] );
        $( ".rate_amount .from" ).text(job_manager_ajax_filters.currency + ui.values[ 0 ]);
        $( ".rate_amount .to" ).text(job_manager_ajax_filters.currency +ui.values[ 1 ]);
      }
    });
    
    $( ".rate_amount .from" ).text(job_manager_ajax_filters.currency + $( "#rate-range" ).slider( "values", 0 ));
    $( ".rate_amount .to" ).text(job_manager_ajax_filters.currency + $( "#rate-range" ).slider( "values", 1 ));
    $( "input#rate_amount" ).val(  $( "#rate-range" ).slider( "values", 0 ) + "-" + $( "#rate-range" ).slider( "values", 1 ) );
    /* eof rate slider */



	$( '#search_keywords, #search_location, #search_radius, .radius_type, .job_types :input, #search_categories, .job-manager-filter, .filter_by_check' ).change( function() {
		var target   = $('div.job_listings' );
	
		target.triggerHandler( 'update_results', [ 1, false ] );
		job_manager_store_state( target, 1 );
	} )

	.on( "keyup", function(e) {
		if ( e.which === 13 ) {
			$( this ).trigger( 'change' );
		}
	} );

	$('.filter_by_check').change(function(event) {
		
		$(this).parents('.widget').find('.widget_range_filter-inside').toggle();
	});

	$( "#salary-range,#rate-range" ).on( "slidestop", function( event, ui ) {
		var target   = $('div.job_listings' );
		target.triggerHandler( 'update_results', [ 1, false ] );
		job_manager_store_state( target, 1 );
	} );

	$( '.job_filters' ).on( 'click', '.reset', function () {
		var target = $('div.job_listings' );
		var form = $( this ).closest( 'form' );

		form.find( ':input[name="search_keywords"], :input[name="search_location"], .job-manager-filter' ).not(':input[type="hidden"]').val( '' ).trigger( 'chosen:updated' );
		form.find( ':input[name^="search_categories"]' ).not(':input[type="hidden"]').val( 0 ).trigger( 'chosen:updated' );
		$( ':input[name="filter_job_type[]"]', form ).not(':input[type="hidden"]').attr( 'checked', 'checked' );
		$('.search_keywords #search_keywords').val("");
		$('#search_radius').val("");
		$('.job_filters #search_keywords').val("");
		$('.filter_by_check').prop('checked', false);
		$('.widget_range_filter-inside').hide();
		$( "#salary-range,#rate-range" ).each(function(){
	      var options = $(this).slider( 'option' );
	      $(this).slider( 'values', [ options.min, options.max ] );

	    });  
	  
		target.triggerHandler( 'reset' );
		target.triggerHandler( 'update_results', [ 1, false ] );
		job_manager_store_state( target, 1 );
		return false;
	} );

	$( document.body ).on( 'click', '.load_more_jobs', function() {
		var target           = $( this ).closest( 'div.job_listings' );
		var page             = parseInt( $( this ).data( 'page' ) || 1 );
		var loading_previous = false;

		$(this).find('i').removeClass('fa fa-plus-circle').addClass( 'fa fa-refresh fa-spin' );

		if ( $(this).is('.load_previous') ) {
			page             = page - 1;
			loading_previous = true;
			if ( page === 1 ) {
				$(this).remove();
			} else {
				$( this ).data( 'page', page );
			}
		} else {
			page = page + 1;
			$( this ).data( 'page', page );
			job_manager_store_state( target, page );
		}

		target.triggerHandler( 'update_results', [ page, true, loading_previous ] );
		return false;
	} );

	$( 'div.job_listings' ).on( 'click', '.job-manager-pagination a', function() {
		var target = $( this ).closest( 'div.job_listings' );
		var page   = $( this ).data( 'page' );

		job_manager_store_state( target, page );

		target.triggerHandler( 'update_results', [ page, false ] );

		$( "body, html" ).animate({
            scrollTop: target.offset().top-40
        }, 600 );

		return false;
	} );

	if ( $.isFunction( $.fn.chosen ) ) {
		if ( job_manager_ajax_filters.is_rtl == 1 ) {
			$( 'select[name^="search_categories"]' ).addClass( 'chosen-rtl' );
		}
		$( 'select[name^="search_categories"]' ).chosen({ search_contains: true });
	}

	if ( window.history && window.history.pushState ) {
		$supports_html5_history = true;
	} else {
		$supports_html5_history = false;
	}

	var location = document.location.href.split('#')[0];

	function job_manager_store_state( target, page ) {
		if ( $supports_html5_history ) {
			var form  = $( '.job_filters' );
			var data  = $( form ).serialize();
			var index = $( 'div.job_listings' ).index( target );
			window.history.replaceState( { id: 'job_manager_state', page: page, data: data, index: index }, '', location + '#s=1' );
		}
	}

	// Inital job and form population
	$(window).on( "load", function( event ) {
		$( '.job_filters' ).each( function() {
			var target      = $('div.job_listings' );
			var form        = $( '.job_filters' );
			var inital_page = 1;
			var index       = $( 'div.job_listings' ).index( target );

	   		if ( window.history.state && window.location.hash ) {
	   			var state = window.history.state;
	   			if ( state.id && 'job_manager_state' === state.id && index == state.index ) {
					inital_page = state.page;
					form.deserialize( state.data );
					form.find( ':input[name^="search_categories"]' ).not(':input[type="hidden"]').trigger( 'chosen:updated' );
				}
	   		}

			target.triggerHandler( 'update_results', [ inital_page, false ] );
	   	});
	});
} );



jQuery( document ).ready( function ( $ ) {
	
	$( '.job_filters' ).on( 'click', '.filter_by_tag a', function(e) {
		e.preventDefault();
		var tag = $(this).text();
		var existing_tag = $('.filter_by_tag').find('input[value="' + tag + '"]');

		if ( existing_tag.size() > 0 ) {
			$(existing_tag).remove();
			$(this).removeClass('active');
		} else {
			$('.filter_by_tag').append('<input type="hidden" name="job_tag[]" value="' + tag + '" />');
			$(this).addClass('active');
		}

		$( '.job_listings' ).trigger( 'update_results', [ 1, false ] );

		return false;
	})

	$( '.job_listings' )

		.on( 'reset', function() {
			$('.filter_by_tag a.active').removeClass('active');
			$('.filter_by_tag input').remove();
			
		})

		.on( 'updated_results', function( event, results ) {

			if ( results.tag_filter ) {
				var $target = $('.job_filters');
	
				$target.find( '.filter_by_tag_cloud' ).html( results.tag_filter );
				$target.find( '.filter_by_tag' ).show();
				$target.find( '.filter_by_tag input' ).each(function(){
					var tag = $(this).val();
					$target.find('.filter_by_tag a').each(function(){
						if ( $(this).text() === tag ) {
							$(this).addClass('active');
						}
					});
				});
			} else {
				$('.job_filters').find( '.filter_by_tag' ).hide();
			}
		})

		.on( 'change', '#search_categories', function() {
			var target = $( this ).closest( 'div.job_listings' );
			$('.job_filters').find('.filter_by_tag input').remove();
			target.trigger( 'update_results', [ 1, false ] );
		});
});
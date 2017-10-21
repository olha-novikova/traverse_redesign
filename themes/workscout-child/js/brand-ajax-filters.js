/**
 * Created with JetBrains PhpStorm.
 * User: olga
 * Date: 18.10.17
 * Time: 12:11
 * To change this template use File | Settings | File Templates.
 */
jQuery( document ).ready( function ( $ ) {

    var xhr = [];

    $( '.influencer_output' ).on( 'update_results', function( event, page, append ) {
        var data     = '';
        var target   = $(this);
        var results  = target.find( '.influencers__list' );
        var form     = $( '.panel__search').find('.panel__search__input');
        var per_page = target.data( 'per_page' );
        var orderby  = target.data( 'orderby' );
        var order    = target.data( 'order' );
        var index    = $( 'div.resumes' ).index(this);

        if ( xhr[index] ) {
            xhr[index].abort();
        }

        if ( append ) {
            $( '.load_more_resumes', target ).addClass( 'loading' );
        } else {
            $( results).addClass( 'loading' );
            $( '.carousel__influencer', results ).css( 'visibility', 'hidden' );
        }

        data = {
            action: 			'resume_manager_get_influencers',
            search_keywords:    form.val(),
            show_pagination:    target.data( 'show_pagination' ),
            per_page: 			per_page,
            orderby: 			orderby,
            order: 			    order,
            page:               page
        };

        xhr[index] = $.ajax( {
            type: 		'POST',
            url:        ws.ajaxurl,
            data: 		data,
            success: 	function( response ) {
                if ( response ) {
                    try {

                        // Get the valid JSON only from the returned string
                        if ( response.indexOf("<!--WPJM-->") >= 0 )
                            response = response.split("<!--WPJM-->")[1]; // Strip off before WPJM

                        if ( response.indexOf("<!--WPJM_END-->") >= 0 )
                            response = response.split("<!--WPJM_END-->")[0]; // Strip off anything after WPJM_END

                        var result = $.parseJSON( response );

                        if ( result.html ) {
                            if ( append ) {
                                $(results).append( result.html );
                            } else {
                                $(results).html( result.html );
                            }
                        }

                        if ( true == target.data( 'show_pagination' ) ) {
                            target.find('.job-manager-pagination').remove();

                            if ( result.pagination ) {
                                target.append( result.pagination );
                            }
                        } else {
                            if ( ! result.found_resumes || result.max_num_pages === page ) {
                                $( '.load_more_resumes', target ).hide();
                            } else {
                                $( '.load_more_resumes', target ).show().data( 'page', page );
                            }
                            $( '.load_more_resumes', target ).removeClass( 'loading' );
                            $( 'li.resume', results ).css( 'visibility', 'visible' );
                        }

                        $( results ).removeClass( 'loading' );

                        target.triggerHandler( 'updated_results', result );

                    } catch(err) {
                        //console.log(err);
                    }
                }
            }
        } );
    } );

    $( '.panel__search' ).on( 'click', '.button_search', function() {
        var target  = $( 'div.influencer_output' );

        target.triggerHandler( 'update_results', [ 1, false ] );

        return false;
    } );

    $( '.panel__search' ).on( 'change', 'input.panel__search__input', function() {

        var target  = $( 'div.influencer_output' );

        target.triggerHandler( 'update_results', [ 1, false ] );

        return false;
    } );

    $( '.influencer_output' ).on( 'click', '.job-manager-pagination a', function() {

        var target = $( this ).closest( 'div.influencer_output' );
        var page   = $( this ).data( 'page' );

        target.triggerHandler( 'update_results', [ page, false ] );

        return false;
    } );

    $(".dropdown-item").click(function (e) {
        e.preventDefault();
        var current = $(this);
        var showString = $(this).parent().siblings(".dropdown-toggle");
        showString.text(current.text());
        showString.addClass("dropdown__label_active");

        var sort = current.data('sort');
        var sort_by = current.data('sort_by');

        var target  = $( 'div.influencer_output' );

        target.data('orderby',sort_by);
        target.data('order',sort);

        target.triggerHandler( 'update_results', [ 1, false ] );

    });



});

jQuery(document).ready(function($) {
	jQuery( "#job_deadline, #_application_deadline" ).each( function() {
		jQuery(this).datepicker( {
			minDate: 0,
			altFormat  : 'yy-mm-dd',
			"dateFormat": wp_job_manager_deadline_args.date_format,
		    monthNames: wp_job_manager_deadline_args.monthNames,
		    monthNamesShort: wp_job_manager_deadline_args.monthNamesShort,
		    dayNames: wp_job_manager_deadline_args.dayNames,
		    dayNamesShort: wp_job_manager_deadline_args.dayNamesShort,
		    dayNamesMin: wp_job_manager_deadline_args.dayNamesMin,
			firstDay: wp_job_manager_deadline_args.firstDay,
			gotoCurrent: true
		} );

		if ( jQuery(this).val() ) {
			var date = new Date( jQuery(this).val() );
			jQuery(this).datepicker( "setDate", date );
		}
	});
});

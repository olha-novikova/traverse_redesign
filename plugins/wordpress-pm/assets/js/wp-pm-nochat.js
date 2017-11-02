(function($) {
	"use strict";
	
	function check_new_messages() {
		setTimeout(function () {
			$.ajax({
				url: wp_pm_ajax.ajax_url,
				type: 'post',
				data: {
					'action': 'pm_grab_latest_conversation'
				},
				dataType: 'json',  
				success: function (response) {
					if (response.new_conversations.count!=null)
						var count = response.new_conversations.count;
					else 
						var count = 0;
					$(".icon__number_purple").text(count);
				},
				complete: check_new_messages
			});
		}, 2000);
	}
	
	if($(".icon__number_purple").length > 0)
		check_new_messages();
	
	$(document).on('click', '.openchat', function()
	{
		var reciever = $( this ).attr("data-reciever-id");
		var jobid = $( this ).attr("data-job-id");
		var jobname = $( this ).attr("data-job-name");
		
		swal.queue([{
			title: 'Send message',
			input: 'textarea',
			inputPlaceholder: 'Type your message here',
			confirmButtonText: 'Send',
			showLoaderOnConfirm: true,
			inputValidator: function (value) {
				return new Promise(function (resolve, reject) {
					if (reciever=="") { reject('Reciever ID is required!'); return false; }
					// if (jobid=="") { reject('Job ID is required!'); return false; }
					// if (jobname=="") { reject('Job Name is required!'); return false; }
					if (value)
					{
						$.ajax({
							url: wp_pm_ajax.ajax_url,
							type: 'post',
							data: {
								'action': 'pm_create_new_message',
								'reciever_id': reciever,
								'text': value,
								'jobid' : jobid,
								'jobname' : jobname
							},
							beforeSend: function(response) {
								swal.showLoading() 
							},
							success: function(response) {
								swal.insertQueueStep('Success! Your message has been sent');
								resolve()
							}
						})
					} else {
						reject('You need to write something!');
					}
					
				})
			}
		}]);
	});
})( jQuery );
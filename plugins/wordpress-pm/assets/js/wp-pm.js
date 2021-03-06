jQuery(document).ready(function( $ )
{
	"use strict";
	function getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	}
	
	function updateURLParameter(url, param, paramVal)
	{
		var TheAnchor = null;
		var newAdditionalURL = "";
		var tempArray = url.split("?");
		var baseURL = tempArray[0];
		var additionalURL = tempArray[1];
		var temp = "";

		if (additionalURL) 
		{
			var tmpAnchor = additionalURL.split("#");
			var TheParams = tmpAnchor[0];
				TheAnchor = tmpAnchor[1];
			if(TheAnchor)
				additionalURL = TheParams;

			tempArray = additionalURL.split("&");

			for (var i=0; i<tempArray.length; i++)
			{
				if(tempArray[i].split('=')[0] != param)
				{
					newAdditionalURL += temp + tempArray[i];
					temp = "&";
				}
			}        
		}
		else
		{
			var tmpAnchor = baseURL.split("#");
			var TheParams = tmpAnchor[0];
				TheAnchor  = tmpAnchor[1];

			if(TheParams)
				baseURL = TheParams;
		}

		if(TheAnchor)
			paramVal += "#" + TheAnchor;

		var rows_txt = temp + "" + param + "=" + paramVal;
		return baseURL + "?" + newAdditionalURL + rows_txt;
	}
	
	function grab_conversation_message(convId) 
	{
		$.ajax({
				url: wp_pm_ajax.ajax_url+"?action=grab_conversation_message",
				type: 'post',
				data: {
					'action': 'pm_grab_conversation_message',
					'convId': convId
				},
				dataType: 'json',
				beforeSend: function() {
					$('.chat__content__right .chat_listing').html('<div class="chat_loading">Loading...</div>');
				},
				success: function(response) {
					var html = "", 
						pname = "",
						dom = $('.chat__content__right .chat_listing');
					$.each(response.messages, function(index, element) {
						if (element.owner) pname = element.sender_name;
						else pname = element.reciever_name;
						var fromnow = moment(element.time_iso).fromNow();
						html+='<div class="chat_listing_single"><div class="chat__left_image"><div class="chat_person_image"><span class="person_image" style="background-image:url('+element.pic+');"></span></div><div class="chat__content_wrapper"><div class="chat__content_header"><h3 class="chat__content_title">'+pname+'</h3><span class="chat_content_time" data-livestamp="'+element.time_iso+'" title="'+element.time_iso+'">'+fromnow+'</span></div> <div class="chat_content_text"><p>'+element.message+'<span data-message-id="'+element.id+'">x</span></p></div></div> </div> </div>';
					});
					dom.html(html);
					dom.scrollTop($('.chat__content__right .chat_listing')[0].scrollHeight);
					$("#usrform textarea").focus();
				},
				error: function(xhr, ajaxOptions, thrownError) {
					
				}
			});
	}

	function create_new_message(reciever_id, text) {
		$.ajax({
			url: wp_pm_ajax.ajax_url+"?action=pm_create_new_message",
			type: 'post',
			data: {
                'action': 'pm_create_new_message',
                'reciever_id': reciever_id,
                'text': text
            },
			success: function(response) {
				swal('Success!', 'Your message has been sent', 'success');
				window.location.reload();
			}
		});
	}
	
	function message_seen(conv_id, sender_id, id) {
		$.ajax({
			url: wp_pm_ajax.ajax_url+"?action=pm_message_seen",
			type: 'post',
			data: {
                'action': 'pm_message_seen',
                'conv_id': conv_id,
                'sender_id': sender_id,
                'id': id
            },
			success: function(response) {
			}
		});
	}
	
	function grab_latest_conversation() {
		setTimeout(function () {
			$.ajax({
				url: wp_pm_ajax.ajax_url+"?action=grab_latest_conversation",
				type: 'post',
				data: {
					'action': 'pm_grab_latest_conversation'
				},
				dataType: 'json',  
				success: function (response) {
					var html = "", dom = $('#pm_conversations');
					var conversationID = (getUrlParameter('conversationID') != null) ? getUrlParameter('conversationID') : response.new_conversations.conversation[0].id ;
					$.each(response.new_conversations.conversation, function(index, element) {
						var message = element.message ;
						var active_class = (element.id == conversationID) ? "active" : "";
						var conv_name = (element.sender == userID) ? element.reciever_name : element.sender_name;
						var job_name = element.job_name;
						if (job_name.length > 0) conv_name = conv_name + " | " + job_name;
						var fromnow = moment(element.time_iso).fromNow();
						var convname = (element.seen!=1) ? '<strong>'+conv_name+'</strong>' : conv_name;
						if (message.length > 0) message = '<p>'+message+'</p>';
						else message = '<p class="chat__italic">attachment</p>';
						html+='<div class="chat_content_single '+active_class+'" data-msg-id="'+element.message_id+'" data-reciever-id="'+element.reciever+'" data-sender-id="'+element.sender+'" data-reciever-name="'+element.reciever_name+'" data-sender-name="'+element.sender_name+'" data-conversation-id="'+element.id+'" data-seen="'+element.seen+'" data-created_at="'+element.created_at+'" data-owner="'+element.owner+'" data-time="'+element.time+'"><div class="chat__left_image"><div class="chat_person_image"><span class="person_image" style="background-image:url('+element.pic+');"></span></div><div class="chat__content_wrapper"><h3 class="chat__content_title">'+convname+'</h3><div class="chat_content_text">'+message+'</div><small class="chat_content_time" data-livestamp="'+element.time_iso+'" title="'+element.time_iso+'">'+fromnow+'</small></div></div><div class="chat_right_logo"><span class="icon-list__element"><i class="icon icon_chat_msg"></i></span></div></div>';
					});
					dom.html(html);
					if (response.new_conversations.count!=null)
						var count = response.new_conversations.count;
					else 
						var count = 0;
					$(".icon__number_purple").text(count);
				},
				complete: grab_latest_conversation
			});
		}, 2000);
	}
	
	function auto_pull_messages() {
		setTimeout(function () {
			var msg = $( "#pm_conversations .active" ).attr("data-msg-id");
			var reciever = $( "#pm_conversations .active" ).attr("data-reciever-id");
			var sender = $( "#pm_conversations .active" ).attr("data-sender-id");
			var reciever_name = $( "#pm_conversations .active" ).attr("data-reciever-name");
			var sender_name = $( "#pm_conversations .active" ).attr("data-sender-name");
			var conversation_id = $( "#pm_conversations .active" ).attr("data-conversation-id");
			var created_at = $( "#pm_conversations .active" ).attr("data-created_at");
			var owner = $( "#pm_conversations .active" ).attr("data-owner");
			var seen = $( "#pm_conversations .active" ).attr("data-seen");
			var time = $( "#pm_conversations .active" ).attr("data-time");
			$.ajax({
				url: wp_pm_ajax.ajax_url+"?action=pm_auto_pull_messages",
				type: 'post',
				data: {
					'action': 'pm_auto_pull_messages',
					'id': msg,
					'conv_id': conversation_id,
					'reciever_name': reciever_name,
					'sender_name': sender_name,
					'sender_id': sender,
					'reciever_id': reciever,
					'owner': owner,
					'time': time,
					'seen': seen
				},
				dataType: 'json',  
				success: function (response) {
					var html = "", pname = "", dom = $('.chat_listing'), new_unseen = response.autopush_messages.new_unseen_messages;
					if (new_unseen.length > 0)
					{
						$.each(new_unseen, function(index, element) {
							if (($('[data-message-id='+element.id+']').length < 1))
							{
								if (element.owner) pname = element.sender_name;
								else pname = element.reciever_name;
								var fromnow = moment(element.time_iso).fromNow();
								html += '<div class="chat_listing_single"><div class="chat__left_image"><div class="chat_person_image"><span class="person_image" style="background-image:url('+element.pic+');"></span></div><div class="chat__content_wrapper"><div class="chat__content_header"><h3 class="chat__content_title">'+pname+'</h3><span class="chat_content_time" data-livestamp="'+element.time_iso+'" title="'+element.time_iso+'">'+fromnow+'</span></div> <div class="chat_content_text"><p>'+element.message+'<span data-message-id="'+element.id+'">x</span></p>';
								var attachments = jQuery.parseJSON( element.attachments.url );
								if (attachments.length > 0) 
								{
									html += '<div class="images"><div class="images_list">';
								
									$.each(attachments, function(index, attachment) {
										var string = attachment.type,
											substring = "image";
										if (string.indexOf(substring) !== -1)
											html += '<span><a href="'+attachment.url+'" data-fancybox="cl-group"><img src="'+attachment.url+'" title="'+attachment.name+'" alt="'+attachment.name+'"></a></span>';
										
									});
									$.each(attachments, function(index, attachment) {
										var string = attachment.type,
											substring = "image";
										if (string.indexOf(substring) === -1)
											html += '<div class="file"><a href="'+attachment.url+'" >'+attachment.name+'</a></div>';
										
									});
									
									html += '</div></div>';
								}
								
								html += '</div></div> </div> </div>';
							}
						});
						dom.append(html);
						var reciever_id = (reciever==userID) ? sender : reciever;
						
						if (seen==null || seen===false || seen==="false" ) message_seen(conversation_id, reciever_id, msg);
						$('.chat__content__right .chat_listing').scrollTop($('.chat__content__right .chat_listing')[0].scrollHeight);
					}
				},
				complete: auto_pull_messages
			});
		}, 2000);
	}
	
	function delete_message(id) {
		$.ajax({
			url: wp_pm_ajax.ajax_url,
			type: 'post',
			data: {
                'action': 'pm_delete_message',
				'id': id
            },
			success: function(response) {
				
				// console.log(response);
			}
		});
	}
	
	function delete_conversation(id) {
		$.ajax({
			url: wp_pm_ajax.ajax_url,
			type: 'post',
			data: {
                'action': 'pm_delete_conversation',
				'id': id
            },
			success: function(response) {
				// console.log(response);
			}
		});
	}
	
	function push_new_message(data) 
	{
		$.ajax({
			url: wp_pm_ajax.ajax_url,
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$("#usrform textarea").attr("disabled", true);
				$("#usrform input[type=submit]").attr("disabled", true);
			},
			success: function(response) {
				if (response.message)
				{
					var pname="", 
						element = response.message;
						
					// console.log(element);
					if (element.owner) pname = element.sender_name;
						else pname = element.reciever_name;
					var fromnow = moment(element.time_iso).fromNow();
					
					$('.chat__content__right .chat_listing').append('<div class="chat_listing_single"><div class="chat__left_image"><div class="chat_person_image"><span class="person_image" style="background-image:url('+element.pic+');"></span></div><div class="chat__content_wrapper"><div class="chat__content_header"><h3 class="chat__content_title">'+pname+'</h3><span class="chat_content_time" data-livestamp="'+element.time_iso+'" title="'+element.time_iso+'">'+fromnow+'</span></div> <div class="chat_content_text"><p>'+element.message+'<span data-message-id="'+element.id+'">x</span></p></div></div> </div> </div>');
					$('.chat__content__right .chat_listing').scrollTop($('.chat__content__right .chat_listing')[0].scrollHeight);
					
					$("#usrform textarea").removeAttr("disabled");
					$("#usrform input[type=submit]").removeAttr("disabled");
					$("#usrform textarea").val('');
					$("#usrform textarea").focus();
				}
			}
		});
	}
	
	function block_user(blocked_user) 
	{
		$.ajax({
			url: wp_pm_ajax.ajax_url+"?action=pm_block_user",
			type: 'post',
			data: {
                'action': 'pm_block_user',
				'blocked_user': blocked_user
            },
			success: function(response) {
				// console.log(response);
			}
		});
	}
	
	function unblock_user(blocked_user) 
	{
		$.ajax({
			url: wp_pm_ajax.ajax_url,
			type: 'post',
			data: {
                'action': 'pm_unblock_user',
				'blocked_user': blocked_user
            },
			success: function(response) {
				if (response.error != 'undefined' && response.error) {
					return true;
				} else {
					// console.log(response);
					return false;
				}
			}
		});
	}
	
	$(document).on('click', '.chat_content_single', function(){ 
		$( ".chat_content_single" ).removeClass("active");
		$( this ).addClass("active");
		var msg = $( this ).attr("data-msg-id");
		var reciever = $( this ).attr("data-reciever-id");
		var sender = $( this ).attr("data-sender-id");
		var reciever_name = $( this ).attr("data-reciever-name");
		var sender_name = $( this ).attr("data-sender-name");
		var conversation_id = $( this ).attr("data-conversation-id");
		var seen = $( this ).attr("data-seen");
		var reciever_id = (reciever==userID) ? sender : reciever;
		
		$(".chat__menu .delete_conv").attr("data-convid", conversation_id);
		$(".chat__menu .block_user").attr("data-userid", reciever_id);
		$("#usrform input[name=conv_id]").val(conversation_id);
		$("#usrform input[name=sender_id]").val(userID);
		$("#usrform input[name=reciever_id]").val(reciever_id);
		if (conversation_id!=null) 
		{
			var new_conv_url = updateURLParameter(window.location.href, 'conversationID', conversation_id);
			window.history.pushState('page2', 'Title', new_conv_url);
			grab_conversation_message(conversation_id);
			message_seen(conversation_id, reciever_id, msg);
		}
		$("#wp-pm-reciever-name").text(reciever_name);
	});
	
	$(document).on('click', '.chat_content_text p span', function()
	{
		var mid = $( this ).attr("data-message-id");
		// console.log(mid);
		$( this ).closest(".chat_listing_single").remove();
		swal({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then(function () {
			delete_message(mid);
			$( this ).remove();
			swal(
				'Deleted!',
				'Your message has been deleted.',
				'success'
			);
		});
		
	});
	
	$(document).on('click', '.chat__menu .block_user', function()
	{
		var userid = $( this ).attr("data-userid");
		
		swal({
			title: 'Are you sure?',
			text: "You want to block this user?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, block it!'
		}).then(function () {
			block_user(userid);
			$( this ).remove();
			swal(
				'Blocked!',
				'',
				'success'
			);
		});
		
	});
	
	$(document).on('click', '.chat__menu .delete_conv', function()
	{
		var convid = $( this ).attr("data-convid");
		
		swal({
			title: 'Are you sure?',
			text: "You want to delete this conversation?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then(function () {
			delete_conversation(convid);
			
			$('#pm_conversations .chat_content_single:first-child').click();
			$( "[data-conversation-id="+convid+"]" ).remove();
			swal(
				'Deleted!',
				'Your conversation has been deleted.',
				'success'
			);
		});
		
	});
	
	$("#usrform textarea").keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            $("#usrform input[type=submit]").trigger('click');
            return true;
        }
    });
	
	if ($('.chat__content__right .chat_listing').length > 0)
	{
		$('.chat__content__right .chat_listing').scrollTop($('.chat__content__right .chat_listing')[0].scrollHeight);
		
		grab_latest_conversation();
		auto_pull_messages();
		
		var previewNode = document.querySelector("#chat__file");
		previewNode.id = "";
		var previewTemplate = previewNode.parentNode.innerHTML;
		previewNode.parentNode.removeChild(previewNode); 
		var myDropzone = new Dropzone(document.body, { 
			url: wp_pm_ajax.ajax_url+"?action=pm_asset_upload",
			autoProcessQueue: false,
			uploadMultiple: true,
			showFiletypeIcon: true,
			createImageThumbnails: true,
			acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.ppt,.pptx,.pps,.ppsx,.odt,.xls,.xlsx",
			maxFilesize: 2,
			maxFiles: 10,
			previewTemplate: previewTemplate,
			previewsContainer: "#chat__files", 
			clickable: ".uploadmedia",
		});
		// document.querySelector(".icon_snippet").onclick = function() {
			// myDropzone.processQueue();
		// };
		myDropzone.on("sending", function(file, xhr, data) {
			var conv_id = document.querySelector("#usrform input[name=conv_id]").value;
			var reciever_id = document.querySelector("#usrform input[name=reciever_id]").value;
			var sender_id = document.querySelector("#usrform input[name=sender_id]").value;
			var message = document.querySelector("#usrform textarea").value;
            data.append("details", '{"conv_id":'+conv_id+',"reciever_id":'+reciever_id+',"sender_id":'+sender_id+',"message":"'+message+'"}');
        });
		myDropzone.on("success", function(file, response) {
			this.removeFile(file);
			var response = jQuery.parseJSON( response );
			var attachments = jQuery.parseJSON( response.attachments.url );
			var pname="", 
				html="", 
				element = response;
			
			if (element.owner) pname = element.sender_name;
				else pname = element.reciever_name;
				
			var fromnow = moment(element.time_iso).fromNow();
			
			if (($('[data-message-id='+element.id+']').length < 1))
			{
				html += '<div class="chat_listing_single"><div class="chat__left_image"><div class="chat_person_image"><span class="person_image" style="background-image:url('+element.pic+');"></span></div><div class="chat__content_wrapper"><div class="chat__content_header"><h3 class="chat__content_title">'+pname+'</h3><span class="chat_content_time" data-livestamp="'+element.time_iso+'" title="'+element.time_iso+'">'+fromnow+'</span></div> <div class="chat_content_text"><p>'+element.message+'<span data-message-id="'+element.id+'">x</span></p>';
				
				if (attachments.length > 0) 
				{
					html += '<div class="images"><div class="images_list">';
				
					$.each(attachments, function(index, attachment) {
						var string = attachment.type,
							substring = "image";
						if (string.indexOf(substring) !== -1)
							html += '<span><a href="'+attachment.url+'" data-fancybox="cl-group"><img src="'+attachment.url+'" title="'+attachment.name+'" alt="'+attachment.name+'"></a></span>';
						
					});
					$.each(attachments, function(index, attachment) {
						var string = attachment.type,
							substring = "image";
						if (string.indexOf(substring) === -1)
							html += '<div class="file"><a href="'+attachment.url+'" >'+attachment.name+'</a></div>';
						
					});
					
					html += '</div></div>';
				}
				
				html += '</div></div> </div> </div>';
			}		
			$('.chat__content__right .chat_listing').append(html);
			$('.chat__content__right .chat_listing').scrollTop($('.chat__content__right .chat_listing')[0].scrollHeight);
					
			$("#usrform textarea").val('');
			$("#usrform textarea").focus();
			
			
        });
		
		myDropzone.on("removedfile", function(file) {  });
		myDropzone.on("error", function(file, errorMessage) {
			if (!file.accepted) this.removeFile(file);
			swal('oops...', errorMessage, 'warning');
        });
		
		$( "#usrform" ).submit(function( event ) {
			event.preventDefault();
			var files_count = myDropzone.getAcceptedFiles().length;
			if (files_count > 0)
			{
				myDropzone.processQueue();
			} else {
				var text = $('#usrform textarea').val();
				if (text.length > 0)
					push_new_message($(this).serialize());
				else
					swal('oops...', "message can't be blank", 'error');
			}
		});
		
		$('a[data-fancybox="cl-group"]').fancybox({
			baseClass : 'fancybox-custom-layout',
			infobar   : false,
			thumbs    : {
				hideOnClose : false
			},
			touch : {
				vertical : 'auto'
			},
			buttons : [
				'close',
				'thumbs',
				'slideShow',
				'fullScreen',
			],

		});
	}
});
jQuery(document).ready(function($) {
  
  $("#iflychat_popup_chat").change(function() {
    if (($("#iflychat_popup_chat").val() == '3') || ($("#iflychat_popup_chat").val() == '4')) {
	  $("#iflychat_path_pages").show();
	}
	else {
	  $("#iflychat_path_pages").hide();
	}
  });
  $("#iflychat_popup_chat").change();
});
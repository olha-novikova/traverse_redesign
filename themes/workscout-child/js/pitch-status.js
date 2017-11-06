(function($){

    "use strict";

    $(document).ready(function(){

        $('.open-popup-hire').magnificPopup({
            type:'inline',
            midClick: true
        });

        var
            sendOnReview = $('.wp_job_manager_review_application');

        sendOnReview.click(function(e){

            e.preventDefault();
            var $this = $(this),
                targetForm = $this.closest('form.job-manager-application-review-form'),
                targetMessageText = $(targetForm).find('.application-review-msg'),
                action = 'send_on_review';

            targetForm.find( $( '.woocommerce-error' ) ).remove();

            if(typeof targetMessageText.val() != 'undefined' && targetMessageText.val().length !=0){

                var data = targetForm.serialize();

                data = data+'&action='+action;

                jQuery.ajax({
                    type: 'POST',
                    url:  ws.ajaxurl,
                    data: data,
                    dataType: 'json',
                    success: function( result ) {
                        window.location.href = window.location.href;
                    },
                    error:	function( ) {

                    }
                });
            }else{
                targetForm.prepend("<div class='woocommerce-error'>Please, add review message</div>");
            }

        });

    });


})(jQuery);
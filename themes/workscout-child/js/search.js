( function( $ ) {

    $(document).ready(function(){

        $('#search').focus(function(){
            if($(this).val() != '') {
                $('#aj_searchresults').show();
            }
            $(this).addClass('blured');
        }).blur(function(){
                if($('#search').hasClass('blured')){
                    $('#aj_searchresults').hide();
                }
            }).keyup(function() {
                var data = {
                    search:  $(this).val(),
                    action: 'aj_search'
                };

                $.ajax({
                    type: 'post',
                    url:  ws.ajaxurl,
                    data: data,
                    success: function (result) {
                        $('#aj_searchresults').show().html(result);
                    }
                })
            });

        $('#aj_searchresults').mouseout(function(){
            $('#search').addClass('blured');
            }).mouseover(function(){
                $('#search').removeClass('blured');
        });
    });

})( jQuery );
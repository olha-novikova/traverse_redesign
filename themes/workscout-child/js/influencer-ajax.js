jQuery(function($){
        $('#loadmore').click(function(e){
            e.preventDefault();
            var data = {
                'action': 'get_jobs',
                'query': true_posts,
                'page' : current_page
            };
            $.ajax({
                url: ws.ajaxurl,
                data:data,
                type:'POST',
                success:function(data){
                    if( data ) {
                        $('#jobs-table').append(data);
                        current_page++;
                        if (current_page == max_pages)
                            $("#loadmore").remove();
                    } else {
                        $('#loadmore').remove();
                    }
                }
            });
        });

});
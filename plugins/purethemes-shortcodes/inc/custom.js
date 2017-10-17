jQuery(document).ready(function ($) {

    var selection = false;
    var purethemes_shortcodesShortcodePanel = $('#purethemes-shortcodes-shortcode-panel-tmpl').html();
    $('body').append(purethemes_shortcodesShortcodePanel);
    $('.media-modal-backdrop, .media-modal-close').on('click', function(){
        purethemes_shortcodes_hideModal();
    })
    $(document).on('click', '#purethemes-shortcodes-insert', function(){
    
        if(typeof tinyMCE !== 'undefined'){
            if(tinyMCE.activeEditor !== null){
                selection = tinyMCE.activeEditor.selection.getContent();
            }else{
                selection = false;
            }
        } else{
            selection = false;
        }

        $('#purethemes-shortcodes-shortcode-panel').show();

    });

    $(document).on('click','#purethemes-shortcodes-insert-shortcode', function(){
        purethemes_shortcodes_insert_shortcode();
    })

    $(document.body).on('click', '.ptsc-duplicate', function () {
        $('.purethemes-shortcodes-shortcode-config-content tbody.multi:last').clone().insertAfter('.purethemes-shortcodes-shortcode-config-content tbody.multi:last');
    });


    $(document.body).on('click', '#purethemes-shortcodes-shortcode-config-nav li a', function () {
        $('#purethemes-shortcodes-shortcode-config-nav li').removeClass('current');
        $(this).parent().addClass('current');
        if(typeof tinyMCE !== 'undefined'){
            if(tinyMCE.activeEditor !== null){
                selection = tinyMCE.activeEditor.selection.getContent();
            }else{
                selection = false;
            }
        } else{
            selection = false;
        }
        var type = $(this).data('shortcode');
        var data = {
            action: 'form_generate',
            shortcode: type
        };
        $('.purethemes-shortcodes-shortcode-config-content').html('').addClass('loading');

        $.post(ajaxurl, data, function (response) {
            
            $('.purethemes-shortcodes-shortcode-config-content').html(response).removeClass('loading');
            if(selection.length > 0){
                $('.purethemes-shortcodes-shortcode-config-content .ptsc-content').html(selection);
            }
           
            
        });
        

        $('#purethemes-shortcodes-shortcode-config').on( 'click', '.ptsc-upload-images', function( event ){
            console.log('we are hgere');
            event.preventDefault();
            var frame;
            var imgIdInput = $('.ptsc-img-ids' ),
            
            to = $(this);
           

            // If the media frame already exists, reopen it.
            if ( frame ) {
              frame.open();
              return;
            }
            
            // Create a new media frame
            frame = wp.media({
              title: 'Select or Upload Media Of Your Chosen Persuasion',
              button: {
                text: 'Use this media'
              },
              multiple: 'add'  // Set to true to allow multiple files to be selected
            });

            
            // When an image is selected in the media frame...
            frame.on( 'select', function() {
              
              // Get media attachment details from the frame state
                var ids = [];
                var selection = frame.state().get('selection');
                selection.map( function( attachment ) {
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                      // Do something with attachment.id and/or attachment.url here
                });
         
              // Send the attachment id to our hidden input
              imgIdInput.val( ids.join(",") );

            });

           /* frame.on('close',function() {
                imgContainer = "";
            });*/

            // Finally, open the modal on click
            frame.open();

          });
    });



    function purethemes_shortcodes_insert_shortcode(){

        var tag = $('#purethemes-shortcodes-key').val();
        if(tag.length <= 0){return; }
            var output;
            if ($('tbody.multi').length > 0) {
                var wrapper = $('#wrapper_tag').val();
                output = "[" + wrapper + "]";

                $('tbody.multi').each(function() {
                    var row = $(this);
                    var content = $('.ptsc-content',this).val();
                    output += "[" + tag + "";
                    $('.ptsc', this).each(function () {
                        var name = $(this).attr('name'),
                        val = $(this).val();
                        output += " " + name + '="' + val + "\" ";
                    });
                    if ($('#content_flag').length > 0) {
                        output += "]" + content + "[/" + tag + "]";
                    } else {
                        output += "]";
                    }
                });
                output += "[/" + wrapper + "]";
            } else {
                output = "[" + tag + "";
                $('.ptsc').each(function () {
                    var name = $(this).attr('name'),
                    val = $(this).val();
                    output += " " + name + '="' + val + "\" ";
                });
                var content = $('.ptsc-content').val();
                if ($('#content_flag').length > 0) {
                    output += "]" + content + "[/" + tag + "]";
                } else {
                    output += "]";
                }
            }
        purethemes_shortcodes_hideModal();
        window.send_to_editor(output);
        

    }

    function purethemes_shortcodes_hideModal(){
        jQuery('#purethemes-shortcodes-shortcode-panel').hide();
        jQuery('#purethemes-shortcodes-elements-selector').show();
        jQuery('.purethemes-shortcodes-elements-selector').val(''); 
        jQuery('#purethemes-shortcodes-category-selector').val('');
    }



});

        //TODO BIND CHANGE LIVE
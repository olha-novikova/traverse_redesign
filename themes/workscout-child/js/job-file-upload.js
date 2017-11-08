/**
 * Created with JetBrains PhpStorm.
 * User: olga
 * Date: 08.11.17
 * Time: 12:47
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($) {

    var imagesPreview = function(fileInput, placeToInsertImageName) {
        var labelValue = placeToInsertImageName.find('.label-text');
        var oldValue = labelValue.html();
        labelValue.html('');

        if ( fileInput.files ) {

            var filesAmount = fileInput.files.length;
            var itemSet = "<ul>";
            for (var i = 0; i < filesAmount; i++) {

                var file = fileInput.files[i];
                var name = file.name;

                var item = '<li class = "file-name"><span>'+name+'</span></li>';
                itemSet += item;
            }

            itemSet += '<li class="search-choice-close"></li></ul>';
            labelValue.append(itemSet);
            $(fileInput).addClass('has-value');
        }else{
            $(fileInput).removeClass('has-value');
        }

        $('.search-choice-close').click(function(e){
            e.preventDefault();
            e.stopPropagation();
            $(fileInput).val('');
            $(this).closest('ul').remove();

        });
    };

    $(".panel__search__input").change( function() {

        imagesPreview( this, $(this).closest('.panel__search ').find('.panel__search__input'));

    });

});
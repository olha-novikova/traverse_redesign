jQuery(document).ready(function($) {
$('.form__input').focusout(function() {
    var text_val = $(this).val();   
    if (text_val === "") {
      $(this).removeClass('has-value');
    } else {
      $(this).addClass('has-value');
    }
    
  })

$('.form__side__bottom').click(function() {
	if (!$('.register').is(':visible')) {
		$('.login').hide();
		$('.register').show();
		$('.form__side__bottom .form__side__image_active').addClass('opacity1');
		$('.form__side__top .form__side__image_active').removeClass('opacity1');
	}	
});
$('.form__side__top').click(function() {
	if (!$('.login').is(':visible')) {
		$('.register').hide();
		$('.login').show();
		$('.form__side__top .form__side__image_active').addClass('opacity1');
		$('.form__side__bottom .form__side__image_active').removeClass('opacity1');
	}	
});

    var youtube = document.querySelectorAll( ".youtube" );
    
    for (var i = 0; i < youtube.length; i++) {
        
        var source = "https://img.youtube.com/vi/"+ youtube[i].dataset.embed +"/hqdefault.jpg";
        
        var image = new Image();
                image.src = source;
                image.addEventListener( "load", function() {
                    youtube[ i ].appendChild( image );
                }( i ) );
        
                youtube[i].addEventListener( "click", function() {

                    var iframe = document.createElement( "iframe" );

                            iframe.setAttribute( "frameborder", "0" );
                            iframe.setAttribute( "allowfullscreen", "" );
                            iframe.setAttribute( "src", "https://www.youtube.com/embed/"+ this.dataset.embed +"?rel=0&showinfo=0&autoplay=1" );
                            iframe.setAttribute( "width", "640px" );
                            iframe.setAttribute( "height", "450px" );
                            this.innerHTML = "";
                            this.appendChild( iframe );
                } );    
    };
    
} );
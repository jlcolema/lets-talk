
/*--------------------------------------
	General Functions
--------------------------------------*/

(function($){

    /*--------------------------------------
        On Page Load
    --------------------------------------*/
    
	$(window).on('load', function() {

		/* Title
		--------------------------------------*/

		// Notes...

		// Code...

		/* Nav Toggle
		--------------------------------------*/

		// Notes...

		// $('.navigation__toggle').click( function() {

		// 	$(this).parent().toggleClass('navigation--is-open');

		// });

		/* Smooth Scroll
		--------------------------------------*/

		/* Navigation */

		// Notes...

		// $('.menu-item a').smoothScroll({

		// 	offset: 0

		// });

		/* External Links
		--------------------------------------*/

		// If a URL has an external address, open in a new window/tab.

		// $('a').each(function() {

		// 	var external_url = new RegExp('/' + window.location.host + '/');

		// 	if (!external_url.test(this.href)) {

				// $(this).addClass('external');

		// 		$(this).click(function(event) {

		// 			event.preventDefault();
		// 			event.stopPropagation();

		// 			window.open(this.href, '_blank');

		// 		});

		// 	}

		// });

		/* Screen Size
		--------------------------------------*/

		// Add a div after the footer to display screen size.

		$(".footer").after('<div id="dev"></div>');

		$("#dev").text( $(window).width() + " W / " + $(window).height() + " H"),
		
		$(window).resize(function() {
		
			$("#dev").text( $(window).width() + " W / " + $(window).height() + " H")
		
		})


	});

})(window.jQuery);
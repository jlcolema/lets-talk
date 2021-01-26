
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

		/* Alert Toggle
		--------------------------------------*/

		// Notes...

		$('.alert__toggle').click( function() {

			$(this).parent().toggleClass('alert--is-open');

		});

		/* Subnavigation
		--------------------------------------*/

		// Notes...

		$('.subnavigation__link').on('click', function(e) {

			e.preventDefault();

		})

		$('.subnavigation__item').click( function() {

			// Remove `is-current` class from all items

			$(this).siblings().removeClass('subnavigation__item--is-current');

			// Add `is-current` class to the selected item

			$(this).addClass('subnavigation__item--is-current');

			// Sections
			
			$('.location__section').removeClass('location__section--is-visible');

			$('.main__wrap').find('.location__section').eq(

				$(this).index()

			).addClass('location__section--is-visible');

		});

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

		$('a').each(function() {

			var external_url = new RegExp('/' + window.location.host + '/');

			if (!external_url.test(this.href)) {

				// $(this).addClass('external');

				$(this).click(function(event) {

					event.preventDefault();
					event.stopPropagation();

					window.open(this.href, '_blank');

				});

			}

		});

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
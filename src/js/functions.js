
/*--------------------------------------
	General Functions
--------------------------------------*/

(function($){

    /*--------------------------------------
        On Page Ready
    --------------------------------------*/

	$(document).on('ready', function (){

		/* Title
		--------------------------------------*/

		// Notes...

		/* Smooth Scroll
		--------------------------------------*/

		// Notes...

		// $('.answers .top a').smoothScroll({

			// offset: -20

		// });

		/* External Links
		--------------------------------------*/

		// Notes...

		// $('a').each(function() {

		// 	var external_url = new RegExp('/' + window.location.host + '/');

		// 	if (!external_url.test(this.href)) {

		// 		$(this).addClass('external');

		// 		$(this).click(function(event) {

		// 			event.preventDefault();
		// 			event.stopPropagation();

		// 			window.open(this.href, '_blank');

		// 		});

		// 	}

		// });

	});

    /*--------------------------------------
        On Page Load
    --------------------------------------*/
    
	$(window).on('load', function() {

		/* Title
		--------------------------------------*/

		// Notes...

		/* Nav Toggle
		--------------------------------------*/

		// Notes...

		// $('.navigation__toggle').click( function() {

		// 	$(this).parent().toggleClass('navigation--is-open');

		// });

		// Hide toggle once an item is selected...

		// $('.menu-item').click( function() {

		// 	$('.navigation').removeClass('navigation--is-open');

		// });

		// Hide toggle if an area outside of the toggle is selected while it is open...

		// $(document).click( function(event) {

		// 	if ( ! $(event.target).closest('.navigation').length ) {

		// 		$('.navigation').removeClass('navigation--is-open');

		// 	}

		// });

		/* Services Toggle
		--------------------------------------*/

		// Notes...

		// $('.service__item').click( function() {

			// Section

			// $('.section__welcome').addClass('section__welcome--is-active');

			// Service Item

			// $(this).siblings().removeClass('service__item--is-active');

			// $(this).toggleClass('service__item--is-active');
			
			// Service Samples

			// $('.services__samples').find('.service__samples').removeClass('service__samples--is-visible');

			// $('.services__samples').find('.service__samples').eq(

				// $(this).index()

			// ).toggleClass('service__samples--is-visible');

		// });

		/* Form Toggle
		--------------------------------------*/

		// Notes...

		// $('.member__photo').click( function() {

			// $(this).parent().toggleClass('member__item--is-active');

			// $(this).addClass('service__item--is-active');

		// });

		/* Form Toggle
		--------------------------------------*/

		// Notes...

		// $('.contact__cta').click( function() {

		// 	$('.section__contact').addClass('section__contact--is-visible');

		// });

		/* Filter Toggle
		--------------------------------------*/

		// Notes...

		// $('.filter__list').click( function() {

		// 	$(this).toggleClass('filter__list--is-open');

		// });

		/* Carousel
		--------------------------------------*/

		// Notes...

		// $('.samples__list').slick({

		// 	slidesToShow: 1,
		// 	slidesToScroll: 1,
		// 	adaptiveHeight: true,
		// 	arrows: true,
		// 	fade: true,
		// 	lazyLoad: 'progressive',
		// 	asNavFor: '.thumbnails__list',
		// 	prevArrow: '.sample__previous',
		// 	nextArrow: '.sample__next'

		// });

		// $('.thumbnails__list').slick({

			// slidesToShow: 5,
			// slidesToScroll: 1,
			// asNavFor: '.samples__list',
			// arrows: false,
			// dots: false,
			// centerMode: false,
			// centerPadding: '20px',
			// focusOnSelect: true,

			// responsive: [
				
				// {

					// breakpoint: 1000,

					// settings: {

						// slidesToShow: 5

					// }

				// }, {

					// breakpoint: 800,

					// settings: {

						// slidesToShow: 4

					// }

				// }, {

					// breakpoint: 600,

					// settings: {

						// slidesToShow: 3

					// }

				// }
			
			// ]

		// });

		/* Infinite Slide
		--------------------------------------*/

		// Adds automatic scrolling to the Key Collaborators section.

		// $('.infinite-slide').infiniteslide({

			// 'speed': 25, // speed is in px/min.
			// 'direction': 'left', // choose up, down, left, or right.
			// 'pauseonhover': true, // if true, stop on mouseover.
		// 	'responsive': false, // width/height recalculation on window resize.
		// 	'clone': 1, // if child elements are too few.

		// });

		/* Smooth Scroll
		--------------------------------------*/

		/* Navigation */

		// Notes...

		// $('.menu-item a').smoothScroll({

		// 	offset: 0

		// });

		/* To Top */

		// Notes...

		// $('.secondary-logo__link').smoothScroll({

		// 	offset: 0

		// });

		/* Animations while in view
		--------------------------------------*/

		// Notes...

		// Details...

		/* Gradient Animations
		--------------------------------------*/

		// Notes...

		// var gradient = getComputedStyle(document.documentElement);

		// Color 01

		// var cg_01_from_x = gradient.getPropertyValue('--cg-01-from-x');
		// var cg_01_from_y = gradient.getPropertyValue('--cg-01-from-y');

		

		/* Move
		--------------------------------------*/

		// $('.c-move a, .c-hero__move a').smoothScroll({

			// offset: -20

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

		/* Slideshow
		--------------------------------------*/

		// Notes...

		// $(".flexslider").flexslider({

			// animation: "fade",
			// slideshowSpeed: 7000,
			// useCSS: true,
			// controlNav: false,
			// directionNav: true,
			// smoothHeight: true,

			// start: function(slider){

				// $("body").removeClass("loading");

			// }

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

    /*--------------------------------------
        On Window Resize
    --------------------------------------*/

	$(window).on('resize', function() {

		/* Title
		--------------------------------------*/

		// Notes...

	});

})(window.jQuery);
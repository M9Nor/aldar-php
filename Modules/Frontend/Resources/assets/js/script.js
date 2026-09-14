/*
Author       : Code-Theme
Template Name: Find Houses - HTML5 Template
Version      : 1.0
*/

"use strict";

jQuery(document).on('ready', function ($) {

	/*---------------------------------
	 //------ PRELOADER ------//
	 ----------------------------------*/
	$('#status').fadeOut();
	$('#preloader').delay(200).fadeOut('slow');

	/*---------------------------------
	 //------ ANIMATE HEADER ------//
	 ----------------------------------*/
	$(window).on('scroll', function () {
		var sticky = $(".sticky-header");
		var scroll = $(window).scrollTop();
		if (scroll < 265) {
			sticky.removeClass("sticky");
		} else {
			sticky.addClass("sticky");
		}
	});

	/*---------------------------------
	 //------ Rev Slider ------//
	 ----------------------------------*/
	var tpj = jQuery;
	var revapi18;
	if (tpj("#rev_slider_18_1").revolution === undefined) {
		revslider_showDoubleJqueryError("#rev_slider_18_1");
	} else {
		revapi18 = tpj("#rev_slider_18_1").show().revolution({
			sliderType: "carousel",
			jsFileLocation: "revolution/js/",
			sliderLayout: "fullwidth",
			dottedOverlay: "none",
			delay: 9000,
			navigation: {
				keyboardNavigation: "off",
				keyboard_direction: "horizontal",
				mouseScrollNavigation: "off",
				mouseScrollReverse: "default",
				onHoverStop: "on",
				thumbnails: {
					style: "gyges",
					enable: true,
					width: 50,
					height: 50,
					min_width: 50,
					wrapper_padding: 5,
					wrapper_color: "transparent",
					tmp: '<span class="tp-thumb-img-wrap">  <span class="tp-thumb-image"></span></span>',
					visibleAmount: 5,
					hide_onmobile: false,
					hide_over: 1240,
					hide_onleave: false,
					direction: "horizontal",
					span: false,
					position: "inner",
					space: 5,
					h_align: "center",
					v_align: "top",
					h_offset: 0,
					v_offset: 20
				},
				tabs: {
					style: "gyges",
					enable: true,
					width: 220,
					height: 80,
					min_width: 220,
					wrapper_padding: 0,
					wrapper_color: "transparent",
					tmp: '<div class="tp-tab-content">  <span class="tp-tab-date">{{param1}}</span>  <span class="tp-tab-title">{{title}}</span></div><div class="tp-tab-image"></div>',
					visibleAmount: 6,
					hide_onmobile: true,
					hide_under: 1240,
					hide_onleave: false,
					hide_delay: 200,
					direction: "vertical",
					span: true,
					position: "inner",
					space: 0,
					h_align: "left",
					v_align: "center",
					h_offset: 0,
					v_offset: 0
				}
			},
			carousel: {
				horizontal_align: "center",
				vertical_align: "center",
				fadeout: "off",
				maxVisibleItems: 5,
				infinity: "on",
				space: 0,
				stretch: "off",
				showLayersAllTime: "off",
				easing: "Power3.easeInOut",
				speed: "800"
			},
			responsiveLevels: [1240, 1024, 778, 480],
			visibilityLevels: [1240, 1024, 778, 480],
			gridwidth: [800, 700, 400, 300],
			gridheight: [600, 600, 500, 400],
			lazyType: "single",
			shadow: 0,
			spinner: "off",
			stopLoop: "off",
			stopAfterLoops: -1,
			stopAtSlide: -1,
			shuffle: "off",
			autoHeight: "off",
			hideThumbsOnMobile: "off",
			hideSliderAtLimit: 0,
			hideCaptionAtLimit: 0,
			hideAllCaptionAtLilmit: 0,
			debugMode: false,
			fallbacks: {
				simplifyAll: "off",
				nextSlideOnWindowFocus: "off",
				disableFocusListener: false,
			}
		});
	}

	/*----------------------------------
	//------ SMOOTHSCROLL ------//
	-----------------------------------*/
	smoothScroll.init({
		speed: 1000, // Integer. How fast to complete the scroll in milliseconds
		offset: 200, // Integer. How far to offset the scrolling anchor location in pixels

	});

	/*----------------------------------
	//------ LIGHTCASE ------//
	-----------------------------------*/
	$('a[data-rel^=lightcase]').lightcase();


	/*----------------------------------
	//------ ISOTOPE GALLERY ------//
	-----------------------------------*/
	/* activate jquery isotope */
	// $(window).on('load', function () {
	// 	var $container = $('.portfolio-items').isotope({
	// 		itemSelector: '.item',
	// 		masonry: {
	// 			columnWidth: '.col-xs-12'
	// 		}
	// 	});
	// });
	// bind filter button click
	var filters = $('.filters-group ul li');
	filters.on('click', function () {
		filters.removeClass('active');
		$(this).addClass('active');
		var filterValue = $(this).attr('data-filter');
		// use filterFn if matches value
		$('.portfolio-items').isotope({
			filter: filterValue
		});
	});

	/*----------------------------------
	//------ OWL CAROUSEL ------//
	-----------------------------------*/
	$('.style1').owlCarousel({
		loop: true,
		margin: 10,
		autoplay: true,
		autoplayTimeout: 5000,
		responsive: {
			0: {
				items: 1
			},
			400: {
				items: 1,
				margin: 20
			},
			500: {
				items: 1,
				margin: 20
			},
			768: {
				items: 2,
				margin: 20
			},
			991: {
				items: 2,
				margin: 20
			},
			1000: {
				items: 3,
				margin: 20
			}
		}
	});

	$('.style2').owlCarousel({
		loop: true,
		margin: 0,
		dots: false,
		autoWidth: false,
		autoplay: true,
		autoplayTimeout: 5000,
		responsive: {
			0: {
				items: 2,
				margin: 20
			},
			400: {
				items: 2,
				margin: 20
			},
			500: {
				items: 3,
				margin: 20
			},
			768: {
				items: 4,
				margin: 20
			},
			992: {
				items: 5,
				margin: 20
			},
			1000: {
				items: 6,
				margin: 20
			}
		}
	});

	$('.style3').owlCarousel({
		loop: true,
		margin: 10,
		autoplay: true,
		autoplayTimeout: 5000,
		responsive: {
			0: {
				items: 1
			},
			400: {
				items: 1,
				margin: 20
			},
			500: {
				items: 1,
				margin: 20
			},
			768: {
				items: 2,
				margin: 20
			},
			991: {
				items: 2,
				margin: 20
			},
			1000: {
				items: 5,
				margin: 20
			}
		}
	});

    $('.carousel4').owlCarousel({
            autoPlay: false,
            navigation: true,
            slideSpeed: 600,
            items: 3,
            itemsDesktop: [1239, 3],
            itemsTablet: [991, 2],
            itemsMobile: [767, 1]
        });

	/*----------------------------------
	//------ TOP LOCATION ------//
	-----------------------------------*/
	if ($('#tp-carousel').length) {
		$('#tp-carousel').owlCarousel({
			loop: true,
			margin: 2,
			dots: false,
			responsiveClass: true,
			responsive: {
				0: {
					items: 1,
					nav: true
				},
				600: {
					items: 2,
					nav: true
				},
				1000: {
					items: 5,
					nav: true,
					loop: false
				}
			}
		})
	}

	/*----------------------------------
	//------ JQUERY SCROOLTOP ------//
	-----------------------------------*/
	var go = $(".go-up");
	$(window).on('scroll', function () {
		var scrolltop = $(this).scrollTop();
		if (scrolltop >= 50) {
			go.fadeIn();
		} else {
			go.fadeOut();
		}
	});

	/*----------------------------------
	//----- JQUERY COUNTER UP -----//
	-----------------------------------*/
	$('.counter').counterUp({
		delay: 10,
		time: 5000,
		offset: 100,
		beginAt: 0,
		formatter: function (n) {
			return n.replace(/,/g, '.');
		}
	});

	/*----------------------------------
	//------ MAGNIFIC POPUP ------//
	-----------------------------------*/
	$(document).ready(function () {
		$('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
			disableOn: 700,
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,
			fixedContentPos: false
		});
	});

	/*----------------------------------------------
	//------ FILTER TOGGLE (ON GOOGLE MAPS) ------//
	----------------------------------------------*/
	$('.filter-toggle').on('click', function () {
		$(this).parent().find('form').stop(true, true).slideToggle();
	});

    // Passive event listeners
    jQuery.event.special.touchstart = {
        setup: function( _, ns, handle ) {
            this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
        }
    };
    jQuery.event.special.touchmove = {
        setup: function( _, ns, handle ) {
            this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
        }
    };
    var lazyLoadInstance = new LazyLoad({
    // Your custom settings go here
    });
    var tpj = jQuery;

    var revapi474;
    tpj(document).ready(function() {
        if (tpj("#homepage_main_slider").revolution == undefined) {
            revslider_showDoubleJqueryError("#homepage_main_slider");
        } else {
            revapi474 = tpj("#homepage_main_slider").show().revolution({
                sliderType: "standard",
                jsFileLocation: "//revolution.themepunch.com/wp-content/plugins/revslider/public/assets/js/",
                sliderLayout: "fullscreen",
                gridwidth: 1920,
                gridheight: 1280,
                dottedOverlay: "none",
                delay: 10000,
                navigation: {
                    keyboardNavigation: "off",
                    keyboard_direction: "horizontal",
                    mouseScrollNavigation: "off",
                    mouseScrollReverse: "default",
                    onHoverStop: "off",
                    touch: {
                        touchenabled: "on",
                        touchOnDesktop: "off",
                        swipe_threshold: 75,
                        swipe_min_touches: 1,
                        swipe_direction: "horizontal",
                        drag_block_vertical: false
                    },
                    arrows: {
                        style: "uranus",
                        enable: true,
                        hide_onmobile: false,
                        hide_onleave: false,
                        tmp: '',
                        left: {
                            h_align: "right",
                            v_align: "bottom",
                            h_offset: 125,
                            v_offset: 17
                        },
                        right: {
                            h_align: "right",
                            v_align: "bottom",
                            h_offset: 65,
                            v_offset: 19
                        }
                    },
                    bullets: {
                        enable: true,
                        hide_onmobile: false,
                        style: "hermes",
                        hide_onleave: false,
                        direction: "horizontal",
                        h_align: "left",
                        v_align: "bottom",
                        h_offset: 7,
                        v_offset: 50,
                        space: 5,
                        tmp: ''
                    }
                },
                responsiveLevels: [1240, 1024, 778, 480],
                visibilityLevels: [1240, 1024, 778, 480],
                gridwidth: [1240, 1024, 778, 480],
                gridheight: [700, 700, 700, 700],
                lazyType: "smart",
                parallax: {
                    type: "scroll",
                    origo: "slidercenter",
                    speed: 400,
                    speedbg: 0,
                    speedls: 0,
                    levels: [5, 10, 15, 20, 25, 30, 35, 40, 45, 46, 47, 48, 49, 50, 51, 55],
                },
                shadow: 0,
                spinner: "spinner5",
                stopLoop: "off",
                stopAfterLoops: -1,
                stopAtSlide: -1,
                shuffle: "off",
                autoHeight: "off",
                fullScreenOffset: "60",
                hideThumbsOnMobile: "off",
                hideSliderAtLimit: 0,
                hideCaptionAtLimit: 0,
                hideAllCaptionAtLilmit: 0,
                debugMode: false,
                fallbacks: {
                    simplifyAll: "off",
                    nextSlideOnWindowFocus: "off",
                    disableFocusListener: false,
                }
            });
        }; /* END OF revapi call */
    }); /*ready*/

    $('.style_custom').owlCarousel({
        rtl: $('html').attr('dir') == 'rtl',
        loop: true,
        margin: 10,
        autoplay: true,
        autoplayTimeout: 5000,
        lazyLoad: true,
        responsive: {
            0: {
                items: 1
            },
            400: {
                items: 1,
                margin: 20
            },
            500: {
                items: 1,
                margin: 20
            },
            768: {
                items: 2,
                margin: 20
            },
            991: {
                items: 2,
                margin: 20
            },
            1000: {
                items: 3,
                margin: 20
            }
        }
    });
    $('.style_custom_2').owlCarousel({
        rtl: $('html').attr('dir') == 'rtl',
        loop: true,
        margin: 0,
        dots: false,
        autoWidth: false,
        autoplay: true,
        autoplayTimeout: 5000,
        lazyLoad: true,
        responsive: {
            0: {
                items: 2,
                margin: 20
            },
            400: {
                items: 2,
                margin: 20
            },
            500: {
                items: 3,
                margin: 20
            },
            768: {
                items: 4,
                margin: 20
            },
            992: {
                items: 5,
                margin: 20
            },
            1000: {
                items: 6,
                margin: 20
            }
        }
    });
    $('.slick-slider-custom').slick({
        rtl: $('html').attr('dir') == 'rtl',
        infinite: false,
        slidesToShow: 3,
        slidesToScroll: 1,
        dots: true,
        arrows: false,
        adaptiveHeight: true,
        focusOnSelect: false,
        responsive: [
            {
            breakpoint: 1292,
            settings: {
                dots: true,
                arrows: false
            }
            },
            {
            breakpoint: 993,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
                dots: true,
                arrows: false
            }
            },
            {
            breakpoint: 769,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                arrows: false
            }
            }
    ]
    });
    $('.marquee').marquee({
        speed: 10,
        delayBeforeStart: 0,
        direction: $('html').attr('dir') == 'rtl' ? 'right' : 'left',
        pauseOnHover: true
    });
    $('.close-marquee').click(function() {
        $('.marquee-container').slideUp(function() {
            $('.marquee-container').remove();
        });
    });

    $(window).resize(function () {
        if ($(window).width() <= 720) {
            $('#choose_color').removeClass('color-switcher position');
            $('.contact-form-container').addClass('container');
            $('.contact-form-row').addClass('row');
            $('.contact-form-col').addClass('col-lg-3 col-md-6');
            $('.contact-form-error-col').addClass('col-12');
        }
        else
        {
            $('#choose_color').addClass('color-switcher');
            $('.contact-form-container').removeClass('container');
            $('.contact-form-row').removeClass('row');
            $('.contact-form-col').removeClass('col-lg-3 col-md-6');
            $('.contact-form-error-col').removeClass('col-12');
        }
    });
    $(window).trigger('resize');
    $(document).ready( function() {
        if($('.session_success_message').length > 0)
        {
            swal({
                title: $('.session_success_message').attr('data-title'),
                text: $('.session_success_message').attr('data-text'),
                type: 'custom',
                showConfirmButton: false,
                customClass: 'swal-custom-class',
                timer: 7000,
            })
        }
        $(".change-currency").click(function(){
            var curr = $(this).data().name;
            $( "#currency-form" ).find("input[name=currency]").val( curr );
            $( "#currency-form" ).submit();
        });
    });
}(jQuery));

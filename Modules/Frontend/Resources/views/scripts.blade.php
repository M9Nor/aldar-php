{{-- @if(Request()->route()->getName() != 'ListingController@filter' && Request()->route()->getName() != 'ListingController@getContentBySlug' ) --}}
@if(!isset($view_name))
<script src="{{ Module::asset('frontend:js/jquery.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.sticky/1.0.4/jquery.sticky.min.js" integrity="sha512-QABeEm/oYtKZVyaO8mQQjePTPplrV8qoT7PrwHDJCBLqZl5UmuPi3APEcWwtTNOiH24psax69XPQtEo5dAkGcA==" crossorigin="anonymous"></script>
{{-- <script src="{{ mix('js/jquery.sticky.min.js') }}"></script> --}}
<script src="{{ Module::asset('frontend:js/jquery-ui.js') }}"></script>
<script src="{{ Module::asset('frontend:js/tether.min.js') }}"></script>
{{-- <script src="{{ Module::asset('frontend:js/moment.js') }}"></script> --}}
{{-- <script src="{{ Module::asset('frontend:js/transition.min.js') }}"></script> --}}
<script src="{{ Module::asset('frontend:js/bootstrap.min.js') }}"></script>
@else
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.sticky/1.0.4/jquery.sticky.min.js" integrity="sha512-QABeEm/oYtKZVyaO8mQQjePTPplrV8qoT7PrwHDJCBLqZl5UmuPi3APEcWwtTNOiH24psax69XPQtEo5dAkGcA==" crossorigin="anonymous"></script>
<script src="{{ Module::asset('frontend:js/jquery.min.js') }}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ Module::asset('frontend:bootstrapselect/bootstrap-select.js') }}"></script>
<script src="{{ Module::asset('frontend:js/jquery-ui.js') }}"></script>
<script src="{{ Module::asset('frontend:js/tether.min.js') }}"></script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js"></script>
<script src="{{ Module::asset('frontend:js/mmenu.min.js') }}"></script>
<script src="{{ Module::asset('frontend:js/mmenu.js') }}"></script>
<script src="{{ Module::asset('frontend:js/slick.min.js') }}"></script>
<script src="{{ Module::asset('frontend:js/slick2.js') }}"></script>
<script src="{{ Module::asset('frontend:js/fitvids.js') }}"></script>
<script src="{{ Module::asset('frontend:js/jquery.waypoints.min.js') }}"></script>
<script src="{{ Module::asset('frontend:js/jquery.counterup.min.js') }}"></script>

{{-- <script src="{{ Module::asset('frontend:js/imagesloaded.pkgd.min.js') }}"></script> --}}
<script src="{{ Module::asset('frontend:js/isotope.pkgd.min.js') }}"></script>
<script src="{{ Module::asset('frontend:js/smooth-scroll.min.js') }}"></script>
<script src="{{ Module::asset('frontend:js/lightcase.js') }}"></script>
<script src="{{ Module::asset('frontend:js/owl.carousel.js') }}"></script>
<script src="{{ Module::asset('frontend:js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ Module::asset('frontend:js/ajaxchimp.min.js') }}"></script>
<script src="{{ Module::asset('frontend:js/newsletter.js') }}"></script>
<script src="{{ Module::asset('frontend:js/jquery.form.js') }}"></script>
<script src="{{ Module::asset('frontend:js/jquery.validate.min.js') }}"></script>
<script src="{{ Module::asset('frontend:js/forms-2.js') }}"></script>
<script src="{{ Module::asset('frontend:js/color-switcher.js') }}"></script>

<script src="{{ Module::asset('frontend:revolution/js/jquery.themepunch.tools.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/jquery.themepunch.revolution.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.actions.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.carousel.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.kenburn.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.layeranimation.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.migration.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.navigation.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.parallax.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.slideanims.min.js') }}"></script>
<script src="{{ Module::asset('frontend:revolution/js/extensions/revolution.extension.video.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
    var tpj = jQuery;

    var revapi474;
    tpj(document).ready(function() {
        if (tpj("#homepage_main_slider2").revolution == undefined) {
            revslider_showDoubleJqueryError("#homepage_main_slider2");
        } else {
            revapi328 = tpj("#homepage_main_slider2").show().revolution({
                sliderType: "standard",
                jsFileLocation: "//revolution.themepunch.com/wp-content/plugins/revslider/public/assets/js/",
                sliderLayout: "fullscreen",
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
                lazyType: "none",
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


    $('.style_custom_3').owlCarousel({
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
    $('.style_custom_4').owlCarousel({
        rtl: $('html').attr('dir') == 'rtl',
        loop: true,
        margin: 10,
        autoplay: true,
        autoplayTimeout: 5000,
        lazyLoad: true,
        responsive: {
            0: {
                items: 2
            },
            400: {
                items: 2,
                margin: 20
            },
            500: {
                items: 2,
                margin: 20
            },
            768: {
                items: 2,
                margin: 20
            },
            991: {
                items: 3,
                margin: 20
            },
            1000: {
                items: 5,
                margin: 20
            }
        }
    });
</script>



<script type='text/javascript' src='//cdn.jsdelivr.net/jquery.marquee/1.4.0/jquery.marquee.min.js'></script>
@if($langDirection == 'rtl')
<script type="text/javascript" src="{{ asset('js/jquery.ui.slider-rtl.min.js') }}"></script>
@endif

<script src="{{ Module::asset('frontend:dropzone/dist/min/dropzone.min.js') }}"></script>
{{-- <script src="{{ Module::asset('frontend:dropzone/dist/min/dropzone-amd-module.min.js') }}"></script> --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>


@if(in_array('Notification', array_keys(Module::allEnabled())))
    {{-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js" defer></script>
    @include('notification::firebase_scripts') --}}
@endif
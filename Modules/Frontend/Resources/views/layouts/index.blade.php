@extends('frontend::layouts.master')

    @php
        $seodescription    = '';
        $seokeywords       = '';
        if(isset($contents['seodescription'])){
            if(!empty($contents['seodescription']->translateOrFirst()->description))
            {
                $seodescription = $contents['seodescription']->translateOrFirst()->description;
            }
            else
            {
                $seodescription = $contents['seodescription']->val;
            }
        }
        if(isset($contents['seokeywords'])){
            if(!empty($contents['seokeywords']->translateOrFirst()->description))
            {
                $seokeywords = $contents['seokeywords']->translateOrFirst()->description;
            }
            else
            {
                $seokeywords = $contents['seokeywords']->val;
            }
        }
        $image      = \Module::asset('frontend:assets/header_logo.svg');
    @endphp
    
    @include('frontend::seo.meta', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.meta_title"),
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.meta_title"),
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.meta_title"),
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.meta_title"),
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])

@section('content')
    
    @include('frontend::index.slider')
    @includeWhen(isset($stories[app()->getLocale()]),'frontend::index.stories')
    @include('frontend::index.who_we_are')
    @includeWhen(isset($SliderProject[app()->getLocale()]),'frontend::index.featured_projects')
    @includeWhen(isset($playlists[app()->getLocale()]),'frontend::index.playlists')
    @includeWhen(isset($recentProjects[app()->getLocale()]),'frontend::index.recent_projects')
    @includeWhen(isset($testimonials[app()->getLocale()]),'frontend::index.testimonials')
    @includeWhen(isset($contents['years_of_experience']) || isset($contents['project_count']) || isset($contents['happy_clients']) || isset($contents['sales']), 'frontend::index.stats')
    @includeWhen(isset($articles[app()->getLocale()]),'frontend::index.articles')
    @include('frontend::index.contact')

    @php
        // $c_area     = Illuminate\Support\Facades\Cookie::get('c_area');
        // Cookie::queue('c_area','a');
        // $test       = session('aaaaaa');
        $c_area       = session()->get('aaaaaa');
        
        // dd($test);
        
    @endphp
@endsection

@section('custom')
    <script>
        
    </script>
@endsection

@push('scripts')



    <script>
        // change area by city
        $(".city-select").change(function() {
            // console.log($(this).val());
            if($(this).val() === 'turkey')
            {
                $('select.region-select').empty();
                        $('select.region-select').append($("<option ></option>").val('').text("{{__('frontend::main.filter_section.dropdown.select_area')}}"));
                         $('select.region-select').append($("<option ></option>").val('all').text("{{__('frontend::main.filter_section.dropdown.all_area')}}"));
                var id = '';
            }
            else
            {
                var id = $(this).children(":selected").data().id;
            }

            var url = '{{ route("ListingController@regions", ":id") }}';

            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'GET',
                success: (response) => {
                    if (response.success){
                        $('select.region-select').empty();
                        $('select.region-select').append($("<option selected></option>").val('').text("{{__('frontend::main.filter_section.dropdown.select_area')}}"));
                         $('select.region-select').append($("<option></option>").val('all').text("{{__('frontend::main.filter_section.dropdown.all_area')}}"));
                        // $('.region-select .list').empty();
                        if (response.data.length == 0)
                            return

                        $.each(response.data, function(key, value) {
                            $('select.region-select').append($("<option></option>").val(value.native_name).data('id',value.id).text(value.name));
                            // $('.region-select .list').append(`<li data-value="${value.native_name}" class="option">${value.name}</li>`);
                        });
                    }
                },
                error: (errors) => {
                    console.log(error)
                }
            })
        });
        $(".city-select").trigger('change');
        // stories cusmize
        var _isRtl = $('html').attr('dir') == 'rtl';
        $(function () {
            if($('.customized-slider').length) {
                $('.customized-slider').owlCarousel({
                    margin: 30,
                    dots: true,
                    rtl: _isRtl,
                    autoplayHoverPause: false,
                    autoplay: false,
                    // singleItem: true,
                    smartSpeed: 1200,
                    nav: true,
                    navText: [
                        '<i class="fa fa-arrow-' + (_isRtl ? 'right' : 'left') + '"></i>',
                        '<i class="fa fa-arrow-' + (_isRtl ? 'left' : 'right') + '"></i>'
                    ],
                    navClass: ["owl-prev", "owl-next"],
                    responsive: {
                        0: {
                            items: 2,
                            center: false
                        },
                        480:{
                            items:2,
                            center: false
                        },
                        600: {
                            items: 3,
                            center: false
                        },
                        768: {
                            items: 4
                        },
                        992: {
                            items: 5
                        },
                        1200: {
                            items: 6
                        },
                        1280: {
                            items: 6
                        }
                    }
                })
            }
        })
        // featured projects
        $('.slick-lancers.f-projects').slick({
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
            rtl: _isRtl,
            arrows: false,
            adaptiveHeight: true,
            responsive: [
            {
                breakpoint: 1292,
                settings: {
                    dots: true,
                    arrows: false,
                    items: 3,
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
            }]
        });
        $('.slick-lancers.slick-lancers-2').slick({
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
            rtl: _isRtl,
            arrows: false,
            adaptiveHeight: true,
            responsive: [
            {
                breakpoint: 1292,
                settings: {
                    dots: true,
                    arrows: false,
                    items: 3,
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
            }]
        });
        // testimonials
        $('.slick-lancers.slick-lancers-3').slick({
            infinite: false,
            slidesToShow: 2,
            slidesToScroll: 1,
            dots: true,
            rtl: _isRtl,
            arrows: false,
            adaptiveHeight: true,
            responsive: [
            {
                breakpoint: 1292,
                settings: {
                    dots: true,
                    arrows: false,
                    items: 2,
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
            }]
        });
        var c_area      = "{{$c_area}}";
        // console.log(c_area);
        // console.log(4444444444444);
        $( document ).ready(function() {
            let city_val    = $('select[name=city] option').filter(':selected').val();
            var c_area      = "{{$c_area}}";
            // console.log(c_area);
            // console.log(city_val);
            if(city_val && c_area){
                if(city_val === 'turkey')
                {
                    $('select.region-select').empty();
                            $('select.region-select').append($("<option ></option>").val('').text("{{__('frontend::main.filter_section.dropdown.select_area')}}"));
                            $('select.region-select').append($("<option ></option>").val('all').text("{{__('frontend::main.filter_section.dropdown.all_area')}}"));
                    var id = '';
                }
                else
                {
                    var id = $('select[name=city] option').filter(':selected').data().id;
                }

                var url = '{{ route("ListingController@regions", ":id") }}';

                url = url.replace(':id', id);
                // console.log(url);
                // console.log(id);

                // if(c_area == 'all'){
                //     $('select.region-select').empty();
                //         $('select.region-select').append($("<option ></option>").val('').text("{{__('frontend::main.filter_section.dropdown.select_area')}}"));
                //          $('select.region-select').append($("<option ></option>").val('all').text("{{__('frontend::main.filter_section.dropdown.all_area')}}").selected(true));
                // }

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: (response) => {
                        if (response.success){
                            $('select.region-select').empty();
                            $('select.region-select').append($("<option selected></option>").val('').text("{{__('frontend::main.filter_section.dropdown.select_area')}}"));
                            $('select.region-select').append($("<option></option>").val('all').text("{{__('frontend::main.filter_section.dropdown.all_area')}}"));
                            // $('.region-select .list').empty();
                            // console.log(response.data.length);
                            // console.log(response.data);
                            if (response.data.length == 0)
                                return

                            $.each(response.data, function(key, value) {
                                // console.log(value.native_name);
                                // console.log(c_area);
                                if(value.native_name == c_area){
                                    $('select.region-select').append($("<option></option>").val(value.native_name).data('id',value.id).text(value.name).selected(true));
                                }else{
                                    $('select.region-select').append($("<option></option>").val(value.native_name).data('id',value.id).text(value.name));
                                }
                                // $('.region-select .list').append(`<li data-value="${value.native_name}" class="option">${value.name}</li>`);
                            });
                        }
                    },
                    error: (errors) => {
                        console.log(error)
                    }
                })
            }
        })
        if (performance.navigation.type == 2) {
            location.reload();
        }

    </script>
@endpush


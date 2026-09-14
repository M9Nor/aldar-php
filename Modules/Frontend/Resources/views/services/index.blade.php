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
            'sub_title' 	=> __("frontend::main.services"),
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.services"),
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.services"),
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.services"),
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])

@section('content')

    <div class="inner-pages">
        <section class="headings">
            <div class="text-heading text-center">
                <div class="container">
                    <h1>{{trans('frontend::main.services')}}</h1>
                    <h2><a href="{{ route('index') }}">{{trans('frontend::main.home')}} </a> &nbsp;/&nbsp; {{trans('frontend::main.services')}}</h2>
                </div>
            </div>
        </section>
    </div>

    <section class=" blogs">
        <div class="custom-container service-section">
            <div class="row">
                <div class="col-md-8 col-12">
                    <div class="row">
                        @php
                            $defaultLangImage = route('image', ['size' => '110x110', 'path' => 'defaults/base.png']);
                        @endphp
                        @foreach ($services as $service)
                            <div class="col-md-6 col-12">
                                @include('frontend::includes.service_single', ['service' => $service,'defaultLangImage' => $defaultLangImage])
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mbp_pagination">
                                <nav class="page_navigation">
                                    {{$services->links('frontend::includes.pagination')}}
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <aside class="col-lg-4 col-md-12 car">
                    @include('frontend::includes.side')
                </aside>
            </div>
        </div>
    </section>

@endsection


@push('scripts')
    <script>
        // side features
        $('.slick-lancers.slick-lancers-4').slick({
            infinite: false,
            slidesToShow: 1,
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
                    items: 1,
                }
            }, 
            {
                breakpoint: 993,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
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
    </script>
@endpush
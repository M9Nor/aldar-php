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
            'sub_title' 	=> __("frontend::main.faqs"),
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.faqs"),
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.faqs"),
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.faqs"),
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])

@section('content')

    <section class="filter-index">
        <div class="custom-container">
            <div class="row">
                <div class="col-md-8 col-12">
                    @foreach ($faqs as $i => $faq)
                        <div class="single homes-content details mb-30 mb-4" id="{{$faq->slug}}">
                            <h5 class="mb-4 font-weight-bold">{{$faq->translateOrFirst()->title}}</h5>
                            <div class="row">
                                <div class="col-sm-12 icon_box_area justify-content-around my-2">
                                    <article class="faq">
                                        <div id="accordion_{{$i}}" role="tablist" aria-multiselectable="true">
                                            @foreach ($faq->contents as $v => $content)
                                                <div class="panel panel-default">
                                                    <h4 class="panel-heading">
                                                        <a data-toggle="collapse" data-parent="#accordion_{{$i}}" href="#tab-{{$i}}-{{$v}}">{{ $content->translateOrFirst()->title }}</a>
                                                    </h4>
                                                    <div id="tab-{{$i}}-{{$v}}" class="panel-collapse collapse px-2">
                                                        {!! $content->translateOrFirst()->description !!}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </article>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="container p-0">
                        <div class="article-body mt-4">
                            <div class="row ">
                                <div class="col-md-12">
                                    <h2 class="services-title">{{__("frontend::main.services")}}</h2>
                                </div>
                                @php
                                    $defaultLangImage = route('image', ['size' => '110x110', 'path' => 'defaults/base.png']);
                                @endphp
                                @foreach ($services as $service)
                                    <div class="col-md-6 col-12">
                                        @include('frontend::includes.service_single', ['service' => $service,'defaultLangImage' => $defaultLangImage])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <aside class="col-lg-4 col-md-4 car">
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
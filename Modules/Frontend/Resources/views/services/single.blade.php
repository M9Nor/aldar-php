@extends('frontend::layouts.master')

    @include('frontend::seo.meta', [
        'options' => [
            'title'     	=> null,
            'sub_title' 	=> $model->translateOrFirst()->title,
            'description' 	=> $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
            'keywords' 	    => $model->translateOrFirst()->keywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'     	=> null,
            'sub_title' 	=> $model->translateOrFirst()->title,
            'description' 	=> $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'     	=> null,
            'sub_title' 	=> $model->translateOrFirst()->title,
            'description' 	=> $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
            'keywords' 	    => $model->translateOrFirst()->keywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'     	=> null,
            'sub_title' 	=> $model->translateOrFirst()->title,
            'description' 	=> $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
        ],
    ])
@push('styles')
    <style>
        .project-description table{
            float: unset !important;
        }
        
    </style>
@endpush

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
    
    <section class="blog-section p-0">
        <div class="custom-container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-xs-12 mt-4">
                    <div class="article-body">
                        <div class="blog-details mt-3">
                            <div class="body">
                                <h1>{{$model->translateOrFirst()->title}}</h1>
                                <div class="row p-0">
                                    <div class="col-9 mb-4">
                                        <h2>{!! $model->translateOrFirst()->brief !!} </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="mb-3">
                                    {!!$model->translateOrFirst()->description!!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <aside class="col-lg-4 col-md-12 car mt-lg-4 mt-md-4" style="margin-top: 20px">
                    @include('frontend::includes.side')
                </aside>
            </div>
        </div>
        <div class="mt-4">
            @include('frontend::index.contact_2')
        </div>
    </section>

@endsection


@push('scripts')
    <script>
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
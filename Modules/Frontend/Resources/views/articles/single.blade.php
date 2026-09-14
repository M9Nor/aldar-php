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
                    <h1>{{trans('frontend::main.articles')}}</h1>
                    <h2><a href="{{ route('index') }}">{{trans('frontend::main.home')}} </a> &nbsp;/&nbsp; {{trans('frontend::main.articles')}}</h2>
                </div>
            </div>
        </section>
    </div>
    
    <section class="blog-section p-0">
        <div class="custom-container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-xs-12 mt-4">
                    <div class="article-body">
                        @php
                            $default = route('image', ['size' => '1000x750', 'path' => 'defaults/base.png']);
                        @endphp
                        <div>
                            <img class="lazy" src="{{$default}}" alt="{{$model->translateOrFirst()->title}}" data-src="{{$model->getTranslatedImage('1000x750')}}">
                        </div>

                        <div class="blog-details mt-3">
                            <div class="body">
                                <h1>{{$model->translateOrFirst()->title}}</h1>
                                <div class="row p-0">
                                    <div class="col-md-9 col-12">
                                        <h2>{!! $model->translateOrFirst()->brief !!} </h2>
                                    </div>
                                    <div class="col-md-3 col-12 ltr-text-right rtl-text-left article-aa">
                                        <span class="custom-date">{{\Modules\Cms\Entities\Traits\Helpers::parseDate($model->created_at, 'dS F Y')}}</span>
                                    </div>
                                </div>
                                <div class="shars">
                                    <div class="addthis_inline_share_toolbox"></div>
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
            @if($relatedArticles->isNotEmpty())
                <div class="col-md-12 p-0 mt-5 similar-articles">
                    <h4>{{__('frontend::main.similar_properties')}}</h4>
                </div>
                <div class="row mt-4 mb-4">
                    @php
                        $defaultArticleImage = route('image', ['size' => '1000x750', 'path' => 'defaults/base.png']);
                    @endphp
                    @foreach ($relatedArticles as $article)
                        @php
                            if(!isset($route)) {
                                $route = 'ListingController@articles';
                            }
                            if($route == 'ListingController@articles') {
                                $variables  = ['slug' => $article->slug];
                            }
                        @endphp
                        <div class="col-lg-4 col-md-12 col-xs-12">
                            <div class="news-item">
                                <a href="{{route($route , $variables)}}" class="news-img-link">
                                    <div class="news-item-img">
                                        <img class="img-responsive lazy" data-src="{{ $article->getTranslatedImage('1000x750') }}" src="{{ $defaultArticleImage }}" alt="{{ $article->translateOrFirst()->title }}">
                                    </div>
                                </a>
                                <div class="news-item-text">
                                    <span>{{\Modules\Cms\Entities\Traits\Helpers::parseDate($article->created_at, 'dS F Y')}}</span>
                                    <a href="{{route($route , $variables)}}"><h3>{{ $article->translateOrFirst()->title }}</h3></a>
                                    <div class="news-item-descr big-news">
                                        <p>{{\Illuminate\Support\Str::limit($article->translateOrFirst()->brief, 160)}}</p>
                                    </div>
                                    <div class="news-item-bottom">
                                        <a href="{{route($route , $variables)}}" class="news-link">{{__("frontend::main.read_more")}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="mt-4">
            @include('frontend::index.contact_2')
        </div>
    </section>

@endsection


@push('scripts')
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-618a885965747152"></script>
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
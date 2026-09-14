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
            'sub_title' 	=> __("frontend::main.articles"),
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.articles"),
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.articles"),
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> __("frontend::main.articles"),
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])

@section('content')

    <section class=" blogs">
        <div class="custom-container blog-section">
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="row">
                        @php
                            $defaultArticleImage = route('image', ['size' => '1000x750', 'path' => 'defaults/base.png']);
                        @endphp
                        @foreach ($articles as $article)
                            @php
                                if(!isset($route)) {
                                    $route = 'ListingController@articles';
                                }
                                if($route == 'ListingController@articles') {
                                    $variables  = ['slug' => $article->slug];
                                }
                            @endphp
                            <div class="col-lg-6 col-md-12 col-xs-12 mb-4">
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
                    <div class="row">
                        <div class="col-12">
                            <div class="mbp_pagination">
                                <nav class="page_navigation">
                                    {{$articles->links('frontend::includes.pagination')}}
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
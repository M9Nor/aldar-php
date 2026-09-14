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
            'sub_title' 	=> '',
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> '',
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> '',
            'description' 	=> $seodescription,
            'image'         => $image,
            'keywords' 	    => $seokeywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'     	=> __("frontend::main.site_name"),
            'sub_title' 	=> '',
            'description' 	=> $seodescription,
            'image'         => $image,
        ],
    ])

@section('content')

    <section class="search-index">
        <div class="custom-container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-xs-12">
                    <section class="headings-2 p-0">
                        <div class="block-heading">
                            <div class="row">
                                <div class="col-sm-8 col-12">
                                    <h4>
                                        <span class="heading-icon">
                                            <i class="fa fa-th-list"></i>
                                        </span>
                                        <span><a href="{{route('index')}}">{{__('frontend::main.home')}}</a></span> /
                                        <span>{{__('frontend::listing.search.search_results_of') . (isset($params['q']) ? ' '.$params['q']: '')}}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="blog-info details mb-30">
                        <h5 class="mb-4">{{__('frontend::listing.search.search_results_of') . (isset($params['q']) ? ' '.$params['q']: '')}}</h5>
                        <div class="row">
                            <ul class="col-12 nav nav-pills nav-fill mb-4 filter-nav p-0" id="myTab" data-url="{{route('ListingController@search')}}">
                                <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'all') active @endif" id="all-tab" data-toggle="tab" href="?q={{$params['q']}}&type=all"  role="tab" aria-controls="listing" aria-selected="true">{{__('frontend::listing.search.all_results_tab')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'projects') active @endif" id="listing-tab" data-toggle="tab" href="?q={{$params['q']}}&type=projects" role="tab" aria-controls="listing" aria-selected="false">{{__('frontend::listing.search.projects_tab')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'opportunity') active @endif" id="listing-tab" data-toggle="tab" href="?q={{$params['q']}}&type=opportunity" role="tab" aria-controls="listing" aria-selected="false">{{__('frontend::main.opportunities')}}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'filters') active @endif" id="listing-tab" data-toggle="tab" href="?q={{$params['q']}}&type=filters" role="tab" aria-controls="listing" aria-selected="false">{{__('frontend::listing.search.filters_tab')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'articles') active @endif" id="review-tab2" data-toggle="tab" href="?q={{$params['q']}}&type=articles" role="tab" aria-controls="review" aria-selected="false">{{__('frontend::listing.search.articles_tab')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'pages') active @endif " id="review-tab4" data-toggle="tab" href="?q={{$params['q']}}&type=pages" role="tab" aria-controls="review" aria-selected="false">{{__('frontend::listing.search.pages_tab')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'tags') active @endif "id="review-tab5" data-toggle="tab" href="?q={{$params['q']}}&type=tags" role="tab" aria-controls="review" aria-selected="false">{{__('frontend::listing.search.tags_tab')}}</a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'playlist_videos') active @endif " id="review-tab4" data-toggle="tab" href="?q={{$params['q']}}&type=playlist_videos" role="tab" aria-controls="review" aria-selected="false">{{__('frontend::listing.search.videos_tab')}}</a>
                                </li> --}}
                                {{-- <li class="nav-item">
                                    <a class="nav-link search-tab @if(isset($params['type']) && $params['type'] == 'testimonials') active @endif "id="review-tab5" data-toggle="tab" href="?q={{$params['q']}}&type=testimonials" role="tab" aria-controls="review" aria-selected="false">{{__('frontend::listing.search.testimonials_tab')}}</a>
                                </li> --}}
                            </ul>
                            <div class="col-12">
                                <div class="tab-content filter-tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="properties" role="tabpanel"
                                        aria-labelledby="properties-tab">
                                        <div class="row">
                                            @switch($params['type'])
                                                @case('all')
                                                @case('projects')
                                                
                                                @case('articles')
                                                @case('playlist_videos')
                                                    @forelse($paginatedData as $item)
                                                        <div class="col-md-6 col-xs-12 mb-4">
                                                            @include('frontend::includes.general-item', ['item' => $item,'default' => route('image', ['size' => '500x375', 'path' => 'defaults/attachments.png']) ])
                                                        </div>
                                                    @empty
    
                                                    @endforelse
    
                                                    @break
                                                @case('pages')

                                                @case('filters')
                                                    <div class="col-12 filters">
                                                        <div class="list-group">
                                                            @forelse($paginatedData as $item)
                                                                <a href="{{route('ListingController@getContentBySlug', ['slug' => $item->slug])}}" class="list-group-item list-group-item-action">{{$item->translateOrFirst()->title}}</a>
                                                            @empty
    
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                    @break
                                                @case('testimonials')
                                                    <div class="col-12 testimonials">
                                                        <div class="row">
                                                            @forelse($paginatedData as $item)
                                                                <div class="col-sm-6">
                                                                    <div class="test-1 text-center">
                                                                        <h3>{{ $item->translateOrFirst()->title }}</h3>
                                                                        <img class="my-3 lazy" data-src="{{ $item->getTranslatedImage('270x270') }}" alt="{{ $item->translateOrFirst()->title }}">
                                                                        <p dir="ltr">
                                                                            <span dir="auto">{!! $item->translateOrFirst()->brief !!}</span>
                                                                        </p>
                                                                        @if (!is_null($item->link))
                                                                            <a class="link link-secondary popup-video popup-youtube text-center" href="{{ $item->link }}">
                                                                                {{__('frontend::main.watch_video')}}
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @empty
    
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                    @break
                                                @case('tags')
                                                    <div class="col-12 recent-post">
                                                        <div class="tags flex-wrap">
                                                            @foreach ($popularTags[app()->getLocale()] as $tag)
                                                                <span><a href="{{ route('ListingController@search', ['q' => $tag->translateOrFirst()->text]) }}" class="btn btn-outline-primary">{{$tag->translateOrFirst()->text}}</a></span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @break
                                                 @case('opportunity')
                                                @forelse($paginatedData as $item)
                                                        <div class="col-md-6 col-xs-12 mb-4 portfolio ">
                                                            @include('frontend::includes.project_single_opp', ['project' => $item,'defaultLangImage' => route('image', ['size' => '500x375', 'path' => 'defaults/attachments.png']) ])
                                                        </div>
                                                    @empty
    
                                                    @endforelse
                                                 @break
                                                @default
    
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mbp_pagination">
                                    <nav class="page_navigation">
                                        {{$paginatedData->appends($params)->links('frontend::includes.pagination')}}
                                    </nav>
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

<script>
    $('#myTab .nav-link').click(function(e) {
        e.preventDefault(); // Prevent the browser from handling the link normally, this stops the page from jumping around. Remove this line if you do want it to jump to the anchor as normal.
        var linkHref = $(this).attr('href'); // Grab the URL from the link
        var url = $('#myTab').data('url'); // Grab the URL from the link
        var base_url = $('meta[name="app-url"]').attr("content")
        window.location.href = url + linkHref;
    });
</script>

@endpush
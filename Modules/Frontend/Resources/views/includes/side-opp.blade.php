<div class="side-tags">
    @include('frontend::includes.side_contact_with_agent')
</div>

<div class="side-projects mt-4">
    @if(isset($featuredProjects_opp) && $featuredProjects_opp->isNotEmpty())
        <div class="recent-post py-1">
            @include('frontend::includes.side_opp_projects', ['featuredProjectsOpp' => $featuredProjects_opp,'title' => __('frontend::main.recent_opp')])
        </div>
    @endif
</div>


    @if(isset($model))
        @if($model->tags->isNotEmpty())
            <div class="side-tags mt-4">
                <div class="recent-post">
                    <h5 class="font-weight-bold mb-4 widget-boxed-header">{{__('cms::includes.aside.tags')}}</h5>
                    <div class="tags flex-wrap">
                        @foreach ($model->tags as $tag)
                            <span><a href="{{ route('ListingController@search', ['q' => $tag->translateOrFirst()->text]) }}" class="btn btn-outline-primary mb-2">{{$tag->translateOrFirst()->text}}</a></span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        @if(isset($popularTags[app()->getLocale()]) && $popularTags[app()->getLocale()]->isNotEmpty())
            <div class="side-tags mt-4">
                <div class="recent-post">
                    <h5 class="font-weight-bold mb-4 widget-boxed-header">{{__('frontend::main.popular_tags')}}</h5>
                    <div class="tags flex-wrap">
                        @foreach ($popularTags[app()->getLocale()] as $tag)
                            <span><a href="{{ route('ListingController@search', ['q' => $tag->translateOrFirst()->text]) }}" class="btn btn-outline-primary mb-2">{{$tag->translateOrFirst()->text}}</a></span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif
@if(isset($firstBanners[app()->getLocale()]))
    <div class="side-tags mt-4">
        @include('frontend::includes.side-ad', ['firstBanners' => $firstBanners])
    </div>
@endif
@if(isset($secondBanners[app()->getLocale()]))
    <div class="side-tags mt-4">
        @include('frontend::includes.side-ad2', ['secondBanners' => $secondBanners])
    </div>
@endif
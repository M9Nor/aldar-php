<div class="side-tags modal-body header-search">
    <h5 class="font-weight-bold mb-4 widget-boxed-header">{{__('frontend::main.header.search')}}</h5>
    <form class="nav-search box-border" method="get" action="{{route('ListingController@search')}}">
        <i class="fa fa-search"></i>
        <input required style="{{app()->getLocale() == 'en' ? 'direction:ltr' : 'direction:rtl'}}" type="text" class="form-control form-control-sm" name="q" aria-label="{{__('frontend::main.header.search')}}" value="{{isset($params['q']) ?$params['q'] : ''}}" placeholder="{{__('frontend::main.header.search')}}" autocomplete="off">
        <button style="width: 100%" type="submit" class="btn btn-primary mt-4">{{__('frontend::main.header.search')}}</button>
    </form>
</div>
<div class="side-tags mt-4">
    @include('frontend::includes.side_contact_with_agent')
</div>
<div class="side-projects mt-4">
    @if(isset($featuredProjects[app()->getLocale()]) && $featuredProjects[app()->getLocale()]->isNotEmpty())
        <div class="recent-post py-1">
            @include('frontend::includes.side_projects', ['projects' => $featuredProjects[app()->getLocale()],'title' => __('frontend::listing.side.featured_properties')])
        </div>
    @endif
</div>
<div class="side-projects mt-4">
    @if(isset($recentProjects[app()->getLocale()]) && $recentProjects[app()->getLocale()]->isNotEmpty())
        <div class="recent-post py-1">
            @include('frontend::includes.side_projects', ['projects' => $recentProjects[app()->getLocale()],'title' => __('frontend::main.recent')])
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
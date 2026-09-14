<section class="featured-projects">
    <div class="custom-container">
        <div class="row">
            <div class="col-12">
                <div style="text-align: center" class="title">
                    <h2>{{__("frontend::main.featured_projects")}}</h2>
                </div>
                <div style="text-align: center" class="desc">
                    {{__("frontend::main.featured_projects_desc")}}
                </div>
                <div style="text-align: center" class="tags">
                    @if(isset($popularTags[app()->getLocale()]) && $popularTags[app()->getLocale()]->isNotEmpty())
                        <div class="recent-post">
                            <div class="tags flex-wrap">
                                @foreach ($popularTags[app()->getLocale()] as $tag)
                                    <span><a href="{{ route('ListingController@search', ['q' => $tag->translateOrFirst()->text]) }}" class="btn btn-outline-primary">{{$tag->translateOrFirst()->text}}</a></span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-12">
                <div class="portfolio col-xl-12 p-0 mt-5">
                    <div class="slick-lancers f-projects">
                        @php
                            $defaultLangImage = route('image', ['size' => '1000x750', 'path' => 'defaults/base.png']);
                        @endphp
                        @php
                            $defaultLangImageIcon = route('image', ['size' => '25x25', 'path' => 'defaults/base.png']);
                        @endphp
                        @foreach($SliderProject[app()->getLocale()] as $featuredProject)
                            @include('frontend::includes.project_single', ['project' => $featuredProject,'defaultLangImage' => $defaultLangImage,'defaultLangImageIcon' => $defaultLangImageIcon])
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-12" style="margin-top: 30px;text-align:center">
                <a class="btn btn-primary btn-lg" href="{{ route('ListingController@filter', [
                    'property_classification' => 'apartments',
                    'contract' => 'for-sale',
                    'city' => 'turkey'
                    ]) }}">
                    {{__("frontend::main.show_more")}}
                </a>
            </div>
        </div>
    </div>
</section>
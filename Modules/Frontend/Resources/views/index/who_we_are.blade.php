<section class="who-we-are">
    <div class="custom-container">
        <div class="row">
            <div class="col-md-5 col-12">
                @foreach($mainPages->whereIn('slug',['who-we-are']) as $mainPage)
                    <div class="title">
                        <h2>{{$mainPage->translateOrFirst()->title}}</h2>
                    </div>
                    <div class="we-desc">
                        {!!nl2br($mainPage->translateOrFirst()->brief)!!} 
                    </div>
                    <a class="btn btn-primary btn-lg mb-4" href="{{route('ListingController@getContentBySlug', ['slug' => $mainPage->slug])}}">
                        {{__("frontend::main.show_more")}}
                    </a>
                @endforeach
            </div>
            <div class="col-md-7 col-12">
                <div class="row">
                    @php
                        $defaultLangImage = route('image', ['size' => '110x110', 'path' => 'defaults/base.png']);
                    @endphp
                    @foreach ($services[app()->getLocale()]->take(4) as $service)
                        <div class="col-md-6 col-12">
                            @include('frontend::includes.service_single', ['service' => $service,'defaultLangImage' => $defaultLangImage])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
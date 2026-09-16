<div class="serivce-slingle">
    <div class="first">
        <h3>
            <a href="{{route('ListingController@getContentBySlug', ['slug' => $service->slug])}}">
                {{$service->translateOrFirst()->title}}
            </a>
        </h3>
        <p>{{ Str::limit((string) $service->translateOrFirst()->brief, 100) }}</p>
    </div>
    <div class="second">
        <img class="lazy" data-src="{{$service->getIconImage($service,'original', 'services')}}" src="{{$defaultLangImage}}" alt="{{$service->translateOrFirst()->title}}">
    </div>
</div>
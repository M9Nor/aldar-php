@php
    $description = Str::limit($item->translateOrFirst()->details, 100);
    $addressDetails = null;
    $createdAt = null;
    $views = null;
    $category = null;

    if($item instanceof \Modules\Backend\Entities\Project)
    {
        $category = $item->allCategories->where('type','property_classifications')->where('pivot.options', 'top_type')->first();
        if(is_null($category))
        {
            $category = $item->allCategories->where('type','property_classifications')->first();
        }
        $lowestPrice            = !is_null($priceRange = $item->prices->where('is_sold','no')->first()) ? $priceRange->lowest_price : 0;
        $route = route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'properties'), 'slug' => $item->slug]);
        $addressDetails = (!is_null($city = $item->city) ?  $city->translateOrFirst()->name : '') . ' / ' .  (!is_null($area = $item->area) ?  $area->translateOrFirst()->name : '');
        $image = $item->getTranslatedImage('500x375');
    }
    else
    {
        $createdAt = \Modules\Cms\Entities\Traits\Helpers::parseDate($item->created_at, 'dS F Y');
        $views = round($item->views);

        switch ($item->type) {
            case 'articles':
                $route = route('ListingController@articles', ['slug' => $item->slug]);
                $image = $item->getTranslatedImage('500x375');
                $default = $item->getTranslatedImage('500x375');
                $category = $item->categories->first();
                break;
            case 'playlist_videos':
                $route = route('ListingController@channel', ['slug' => $item->slug]);
                $image = $item->getTranslatedImage('500x375');
                $default = $item->getTranslatedImage('500x375');
                $category = $item->categories->first();
                break;

            default:
                $route = '';
                $image = $item->getTranslatedImage('500x375');
                $default = $item->getTranslatedImage('500x375');
                break;
        }
    }
@endphp

<div class="blog-section">
    <div class="news-wrap">
        <div class="news-item">
            <a href="{{$route}}" class="news-img-link">
                <div class="news-item-img">
                    <img class="img-responsive lazy" src="{{$default}}" data-src="{{ $image }}" alt="{{$item->translateOrFirst()->title}}">
                </div>
            </a>
            <div class="news-item-text">
                <a href="{{$route}}"><h3>{{$item->translateOrFirst()->title}}</h3></a>
                @if(!is_null($addressDetails))
                    <div class="dates">
                        {{$addressDetails}}
                        {{-- <span class="date">
                            {{$addressDetails}}
                        </span> --}}
                        {{-- <ul class="action-list pl-0">
                            <li class="action-item pl-2"><i class="fa fa-heart"></i> <span>306</span></li>
                            <li class="action-item"><i class="fa fa-comment"></i> <span>34</span></li>
                            <li class="action-item"><i class="fa fa-share-alt"></i> <span>122</span></li>
                        </ul> --}}
                    </div>
                @endif
                <div class="news-item-descr big-news">
                    <p>{{\Illuminate\Support\Str::limit($item->translateOrFirst()->brief, 160)}}</p>
                </div>
                <div class="news-item-bottom">
                    <a href="{{$route}}" class="news-link">
                        {{__("frontend::main.read_more")}}
                    </a>
                    @if(!is_null($category))
                        <div class="admin">
                            <p>{{ $category->translateOrFirst()->title }}</p>
                            {{-- <img src="images/testimonials/ts-6.jpg" alt=""> --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
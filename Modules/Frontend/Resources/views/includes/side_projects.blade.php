<div class="widget-boxed-header mb-1">
    <h4>{{$title}}</h4>
</div>
<div class="widget-boxed-body">
    <div class="slick-lancers slick-lancers-4">
        @php
            $defaultLangImage = route('image', ['size' => '500x375', 'path' => 'defaults/base.png']);
        @endphp
        @foreach ($projects->take(3) as $project)
            @php
                $lowestPrice            = !is_null($priceRange = $project->prices->where('is_sold','no')->first()) ? $priceRange->lowest_price : 0;
                $category               = $project->allCategories->where('type','property_classifications')->where('pivot.options', 'top_type')->first();
                if(is_null($category))
                {
                    $category           = $project->allCategories->where('type','property_classifications')->first();
                }
                $projectLink = route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'properties'), 'slug' => $project->slug]);
            @endphp
            <div class="agents-grid mr-0">
                <div class="listing-item compact">
                    <a href="{{$projectLink}}" class="listing-img-container">
                        <div class="listing-badges">
                            @if(!is_null($lowestPrice) || $lowestPrice !== 0) <span class="featured">{{$lowestPrice}}</span>@endif
                          @if(!is_null($category))  <span>{{ $category->translateOrFirst()->title }}</span> @endif
                        </div>

                 
                   
                        <img class="lazy" src="{{$defaultLangImage}}" data-src="{{ $project->getTranslatedImage('500x375') }}" 
                        alt="{{ $project->translateOrFirst()->title }}">
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
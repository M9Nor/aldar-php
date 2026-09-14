@php
    $category               = $project->allCategories->where('type','property_classifications')->where('pivot.options', 'top_type')->first();
    if(is_null($category))
    {
        $category           = $project->allCategories->where('type','property_classifications')->first();
    }
    $selectedCurrency       = Cookie::get('default-currency') ?: "TRY";
    // $lowestPrice            = !is_null($priceRange = $project->priceByLowestPrice) ? $priceRange->lowest_price : 0;
    // $lowestPrice            = !is_null($priceRange = $project->prices->sortBy('lowest_price')->first()) ? $priceRange->lowest_price : 0;
    if($project->prices->isNotEmpty()){
        $lowestPrice            = !empty($priceRange = $project->prices->sortBy('lowest_price')[0]) ? $priceRange->lowest_price : 0;
    }else{
        $lowestPrice            = 0;
    }
    // if($project->id == 173){
    //     dd($project->prices->sortBy('lowest_price'));
    // }
    // dd($lowestPrice);
    $projectLink            = route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'properties'), 'slug' => $project->slug]);
    $addressDetails         = (!is_null($city = $project->city) ?  $city->translateOrFirst()->name : '') . ' / ' .  (!is_null($area = $project->area) ?  $area->translateOrFirst()->name : '');
    $projectDescription     = Str::limit($project->translateOrFirst()->details, 100);
    $projectTitle           = Str::limit($project->translateOrFirst()->title, 110);
@endphp

<div class="agents-grid">
    <div class="landscapes">
        <div class="project-single">
            <div class="project-inner project-head">
                <div class="homes">
                     <a href="{{$projectLink}}" class="homes-img">
                        @if(!is_null($category))<div class="homes-tag button alt featured">{{ $category->translateOrFirst()->title }}</div>@endif
                        <img src="{{ $defaultLangImage }}" data-src="{{ $project->getTranslatedImage('1000x750') }}" alt="@if(!is_null($category)){{ $category->translateOrFirst()->title }}@endif" class="img-responsive lazy">
                    </a>
                </div>
            </div>
            <div class="homes-content">
                <h3>
                    <a href="{{$projectLink}}">
                        {{$projectTitle}}
                    </a>
                </h3>
                <p class="homes-location mb-3">
                    <i class="fa fa-map-marker"></i> {!!$addressDetails!!}
                </p>
                <div class="home-price2 mb-3">
                    <div for="">{{__("frontend::main.start_from")}}</div>
                    <span style="font-weight: bold">{{$lowestPrice}}</span>
                    <a class="recent-see-more" href="{{$projectLink}}">{{__("frontend::main.project_details")}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
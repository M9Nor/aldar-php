@php
    $category               = $project->allCategories->where('type','property_classifications')->where('pivot.options', 'top_type')->first();
    if(is_null($category))
    {
        $category           = $project->allCategories->where('type','property_classifications')->first();
    }
    $lowestPrice            = !is_null($priceRange = $project->prices->where('is_sold','no')->first()) ? $priceRange->lowest_price : 0;

    $projectLink            = route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'properties'), 'slug' => $project->slug]);
    $addressDetails         = (!is_null($city = $project->city) ?  $city->translateOrFirst()->name : '') . ' / ' .  (!is_null($area = $project->area) ?  $area->translateOrFirst()->name : '');
    $projectDescription     = Str::limit((string) $project->translateOrFirst()->details, 100);
    $projectTitle           = Str::limit($project->translateOrFirst()->title, 110);
@endphp

<div class="agents-grid">
    <div class="landscapes">
        <div class="project-single">
            <div class="project-inner project-head">
                <div class="homes">
                    <a href="{{$projectLink}}" class="homes-img">
                       @if(!is_null($category)) <div class="homes-tag button alt featured">{{ $category->translateOrFirst()->title }}</div> @endif
                        <div class="homes-price">{!!$addressDetails!!}</div>
                        <img src="{{ $defaultLangImage }}" data-src="{{ $project->getTranslatedImage('1000x750') }}" alt="@if(!is_null($category)){{ $category->translateOrFirst()->title }}@endif" class="img-responsive lazy">
                    </a>
                </div>
            </div>
            <!-- homes content -->
            <div class="homes-content">
                <!-- homes address -->
                <h3>
                    <a href="{{$projectLink}}">
                        {{$projectTitle}}
                    </a>
                    </h3>
                <p class="homes-address mb-3">
                    {{$projectDescription}}
                </p>
                <div class="home-price mb-3">
                    <div for="">{{__("frontend::main.start_from")}}</div>
                    <span style="font-weight: bold">{{$lowestPrice}}</span>
                    {{-- <a class="ready-for-delivered" href="javascript:;">{{$project->delivery_date}}</a>--}}
                    @if($project->delivery_date !== null)
                        <a class="ready-for-delivered" href="javascript:;">{{$project->delivery_date}}</a>
                    @else
                      {{--   <a class="underway" href="javascript:;">{{__("frontend::main.underway")}}</a>--}}
                    @endif 
                </div>

                <!-- homes List -->
                <ul class="homes-list clearfix">
                    @foreach ($project->propertyFeatures as $propertyFeature)
                        @if ($propertyFeature->pivot->options == 'top_feature')
                            <li style="width: 100%">
                                <img style="display: inline-block" class="lazy" width="25" alt="{{$propertyFeature->translateOrFirst()->title}}"
                                    data-src="{{$propertyFeature->getTranslatedImage('150x150')}}" src="{{$defaultLangImageIcon}}"> 
                                <span>
                                    {{$propertyFeature->translateOrFirst()->title}}
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
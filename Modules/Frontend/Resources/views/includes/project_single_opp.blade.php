@php
    $category               = $project->allCategories->where('type','opportunity_classifications')->where('pivot.options', 'top_type')->first();
    if(is_null($category))
    {
        $category           = $project->allCategories->where('type','opportunity_classifications')->first();
    }
    $lowestPrice            = !is_null($priceRange = $project->prices->first()) ? $priceRange->lowest_price : 0;
    $projectLink            = route('OpportunityController@single', ['type' => (!is_null($category) ? $category->slug : 'opportunities'), 'slug' => $project->slug]);
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
                       <!--  @if(!is_null($category))<div class="homes-tag button alt featured">{{ $category->translateOrFirst()->title }}</div>@endif -->
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
                    <div for="">{{__("frontend::main.price")}}</div>
                    
                    <span style="font-weight: bold">{{$lowestPrice}}</span>
                    @if(is_null($project->roi))<a class="recent-see-more" href="{{$projectLink}}">{{__("frontend::main.project_details")}}</a>
                     @else<span class="roi-box2">{{__("frontend::main.roi")}} {{$project->roi}}%</span>
                     @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style type="text/css">
    .roi-box2{
    float: left;
    color: #18be85;
    font-size: 1rem;
    border: 1px solid #18be85;
    padding: 5px 15px;
    font-weight: 700;
    border-radius: 5px;
    margin: -10px 0px 0px 0px;
}
</style>
@endpush
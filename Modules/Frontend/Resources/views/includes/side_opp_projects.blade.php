<div class="widget-boxed-header mb-1">
    <h4>{{$title}}</h4>
</div>
<div class="widget-boxed-body">
    <div class="slick-lancers slick-lancers-4">
        @php
            $defaultLangImage = route('image', ['size' => '500x375', 'path' => 'defaults/base.png']);
        @endphp
        @foreach ($featuredProjectsOpp->take(3) as $project)
            @php
                $lowestPrice = !is_null($priceRange = $project->prices->first()) ? $priceRange->lowest_price : 0;
                $category               = $project->allCategories->where('type','opportunity_classifications')->where('pivot.options', 'top_type')->first();
             
                $projectLink = route('OpportunityController@single', ['type' => (!is_null($category) ? $category->slug : 'properties'), 'slug' => $project->slug]);
            @endphp
            <div class="agents-grid mr-0">
                <div class="listing-item compact">
                    <a href="{{$projectLink}}" class="listing-img-container">
                     <div class="list-badges">
                    @if(is_null($project->roi))
                        @else<span class="roi-right">{{__("frontend::main.roi")}} {{$project->roi}}%</span>
                    @endif
                     </div>
                   

                   <div class="list-badges-b">
                     <span class="title"> {!! $project->translateOrFirst()->title !!} </span>
                    @if(!is_null($lowestPrice) || $lowestPrice !== 0) <span class="price">{{__("frontend::main.price")}} {{$lowestPrice}}</span>@endif
                </div>
                        <img class="lazy" src="{{$defaultLangImage}}" data-src="{{ $project->getTranslatedImage('500x375') }}" 
                        alt="{{ $project->translateOrFirst()->title }}">

                         

                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('styles')
<style type="text/css">

.list-badges  {
    position: absolute;
    top: 15px;
    width: fit-content;
    display: block;
    overflow: hidden;
    right: 15px;
        border: 1px solid #18be85;
     background: #18be85;
    border-radius: 5px;
    padding: 5px 15px;
}

.list-badges-b {
    position: absolute;
    bottom: 15px;
    width: fit-content;
    display: flex;
    overflow: hidden;
    right: 15px;
    padding: 5px 15px;
    flex-wrap: wrap;
    flex-direction: column;
    z-index: 10000;
}
.list-badges-b .title {
    color: #fff;
    font-size: 1.2rem;
    font-weight: 500;
}
.list-badges-b .price {
    color: #fff;
    font-size: 1.5rem;
    padding-top: 5px;
}
.roi-right{
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
}
</style>
@endpush
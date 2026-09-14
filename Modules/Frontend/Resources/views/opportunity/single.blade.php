@extends('frontend::layouts.master')

    @include('frontend::seo.meta', [
        'options' => [
            'title'         => null,
            'sub_title'     => $model->translateOrFirst()->title,
            'description'   => $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
            'keywords'      => $model->translateOrFirst()->trans_keywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'         => null,
            'sub_title'     => $model->translateOrFirst()->title,
            'description'   => $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'         => null,
            'sub_title'     => $model->translateOrFirst()->title,
            'description'   => $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
            'keywords'      => $model->translateOrFirst()->trans_keywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'         => null,
            'sub_title'     => $model->translateOrFirst()->title,
            'description'   => $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
        ],
    ])
@push('styles')
    <style>
       .adress {
    display: flex;
    flex-wrap: wrap;
    flex-direction: column;
    margin-bottom: 30px;
}
        .adress .city {
    color: #9c9c9c !important;
}

  .adress .in-p{
    color: #000;
  }
        .project-description table{
            float: unset !important;
        }
 #main-slider  .navigation-thumbs img, .property-image-slider.sync1 img {
    height: auto !important;
    object-fit: cover;
}
.owl-carousel .owl-item img {
    height: 500px!important;
    object-fit: cover;
    
}
 #main-slider .no-js .owl-carousel, .owl-carousel.owl-loaded {
    display: block;
}
.detials .desc_price{
    font-size: 1.25rem;
    color: #000;
    font-family: Montserrat,sans-serif;
    font-weight: 600;
    }
    .detials .price{
       font-size: 2.25rem;
    color: #4285f4;
    font-family: Montserrat,sans-serif;
    font-weight: 600;
    padding: 0px 15px;
    }
   .boxdetials {
    background: #fff;
    color: #767779;
    height: max-content !important;
    display: flex;
    min-height: 120px;
    /* padding: 20px; */
    /* margin: auto; */
    width: 100%;
    border-radius: 10px;
    transition: 0.2s;
    font-size: 1.23rem;
}
   .boxdetials dl {
    padding: 29px;
}
.boxdetials dt {
    font-size: 18px;
    font-weight: 400;
    margin: auto;
    text-align: center;
    color: #4285f4;
}
.boxdetials dd {
    margin-inline-start: 0px !important;
    font-size: 18px;
    /* font-weight: 400; */
    /* margin: auto; */
    text-align: center;
    color: #cfd0d0;
}
.smooth-div a {
    color: #767779;
    padding: 0;
}
.smooth-div {
    background: #ebf1fb;
}
.roi-box{
    float: right;
    color: #18be85;
    font-size: 1rem;
    border: 1px solid #18be85;
    padding: 10px;
    font-weight: 700;
    border-radius: 5px;
}
.border:after {
    content: '';
    border-left: 2px solid #e0e0e0;
    position: absolute;
    top: 25%;
    bottom: 25%;
}
    </style>
@endpush

@section('content')
    @php
        $category = $model->allCategories->where('type','opportunity_classifications')->where('pivot.options', 'top_type')->first();
        if(is_null($category))
        {
            $category = $model->allCategories->where('type','opportunity_classifications')->first();
        }
        $lowestPrice = !is_null($priceRange = $model->prices->first()) ? $priceRange->lowest_price : 0;
        $modelLink = route('OpportunityController@single', ['type' => (!is_null($category) ? $category->slug : 'opportunity_classifications'), 'slug' => $model->slug]);
        $addressDetails = (!is_null($city = $model->city) ?  $city->translateOrFirst()->name : '') . ' / ' .  (!is_null($area = $model->area) ?  $area->translateOrFirst()->name : '');
        $modelDescription = Str::limit($model->translateOrFirst()->details, 100);
    @endphp
    <div class="inner-pages">
        <section class="single-proper blog details single-project">
            <div class="custom-container">
                <div class="row">
                    <div class="col-6">
                        <section class="headings-2 pt-0">
                            <div class="pro-wrapper">
                                <div class="detail-wrapper-body">
                                    <div class="listing-title-bar">
                                        
                                        <h3>
                                            {!! $model->translateOrFirst()->title !!} 
                                        </h3>
                                        <div class="mt-0 d-flex">
                                            <a href="javascript:;" class="listing-address">
                                                <i class="fa fa-map-marker pr-2 pl-2 ti-location-pin mrg-r-5"></i>{{$addressDetails}}
                                            </a>
                                                <div>
                                           @if(!is_null($category))   <span class="mrg-l-5 category-tag mr-2 ml-2">{{ $category->translateOrFirst()->title }}</span>@endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-6">
                        <div class="detail-wrapper mr-2">
                            <div class="detail-wrapper-body">
                                <div class="listing-title-bar">
                                    <div class="addthis_inline_share_toolbox"></div>
                                 <!--   <h4>{{__("frontend::main.start_from")}} {{$lowestPrice}}</h4>-->
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-12" id="main-slider">
                         @if($model->attachments->whereIn('input_name', ['featured_images', 'image_internal', 'image_external', 'ser_and_fac'])->isNotEmpty())
                            <div id="listingDetailsSlider" class="mb-30">
                                <div class="slider property-image-slider sync1 customized-slider owl-carousel {{ $langDirection == 'rtl' ? 'owl-rtl' : '' }}" data-iterations="{{count($model->attachments->whereIn('input_name', ['image_internal', 'image_external', 'ser_and_fac']))}}">
                                    @php
                                        $default        = route('image', ['size' => '1920x1079', 'path' => 'defaults/attachments.png']);
                                        $default_thumb  = route('image', ['size' => '192x128', 'path' => 'defaults/attachments.png']);
                                    @endphp
                                    @foreach ($model->attachments->whereIn('input_name', ['featured_images','image_internal', 'image_external', 'ser_and_fac']) as  $attachment)
                                        <img class="lazy" data-input-name="{{$attachment->input_name}}" data-index="{{$loop->index}}" src="{{$default}}" data-src="{{$attachment->getUid('1920x1079')}}" alt="{{$attachment->title . (!is_null($attachment->description) ? ', ' . $attachment->description : '') }}">
                                    @endforeach
                                </div>
                                <div class="navigation-thumbs property-image-slider sync2 owl-carousel {{ $langDirection == 'rtl' ? 'owl-rtl' : '' }}">
                                    @foreach ($model->attachments->whereIn('input_name', ['featured_images', 'image_internal', 'image_external', 'ser_and_fac']) as  $attachment)
                                        <img class="lazy" data-input-name="{{$attachment->input_name}}" data-index="{{$loop->index}}" src="{{$default_thumb}}" data-src="{{$attachment->getUid("192x128")}}" alt="{{$attachment->title . (!is_null($attachment->description) ? ', ' . $attachment->description : '') }}">
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-lg-12 text-center images-btn mt-1">
                                        @foreach ($model->attachments->whereIn('input_name', ['featured_images', 'image_internal', 'image_external', 'ser_and_fac'])->pluck('input_name')->unique() as $item)
                                            <button class="btn btn-thm mx-2 mt-1" onclick="jumptToSlider('{{$item}}');">{{__('frontend::properties.' . $item . '.label')}}</button>
                                        @endforeach
                                    </div>
                                </div> 
                            </div>
                        @endif 
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-12 blog-pots">
                      @if($model->attachments->whereIn('input_name', ['featured_images', 'image_internal', 'image_external', 'ser_and_fac'])->isNotEmpty())
                            {{--  <div id="listingDetailsSlider" class="mb-30">
                                <div class="slider property-image-slider sync1 customized-slider owl-carousel {{ $langDirection == 'rtl' ? 'owl-rtl' : '' }}" data-iterations="{{count($model->attachments->whereIn('input_name', ['image_internal', 'image_external', 'ser_and_fac']))}}">
                                    @php
                                        $default        = route('image', ['size' => '1920x1079', 'path' => 'defaults/attachments.png']);
                                        $default_thumb  = route('image', ['size' => '192x128', 'path' => 'defaults/attachments.png']);
                                    @endphp
                                    @foreach ($model->attachments->whereIn('input_name', ['image_internal', 'image_external', 'ser_and_fac']) as  $attachment)
                                        <img class="lazy" data-input-name="{{$attachment->input_name}}" data-index="{{$loop->index}}" src="{{$default}}" data-src="{{$attachment->getUid('1920x1079')}}" alt="{{$attachment->title . (!is_null($attachment->description) ? ', ' . $attachment->description : '') }}">
                                    @endforeach
                                </div>
                                <div class="navigation-thumbs property-image-slider sync2 owl-carousel {{ $langDirection == 'rtl' ? 'owl-rtl' : '' }}">
                                    @foreach ($model->attachments->whereIn('input_name', ['image_internal', 'image_external', 'ser_and_fac']) as  $attachment)
                                        <img class="lazy" data-input-name="{{$attachment->input_name}}" data-index="{{$loop->index}}" src="{{$default_thumb}}" data-src="{{$attachment->getUid("192x128")}}" alt="{{$attachment->title . (!is_null($attachment->description) ? ', ' . $attachment->description : '') }}">
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-lg-12 text-center images-btn mt-1">
                                        @foreach ($model->attachments->whereIn('input_name', ['featured_images', 'image_internal', 'image_external', 'ser_and_fac'])->pluck('input_name')->unique() as $item)
                                            <button class="btn btn-thm mx-2 mt-1" onclick="jumptToSlider('{{$item}}');">{{__('frontend::properties.' . $item . '.label')}}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>--}}
                        @endif 
                        <div class="smooth-div">
                          <!--  <a class="additional_details active" data-scroll href="#additional_details">{{__('frontend::properties.details')}}</a>
                            <a class="payment_methods" data-scroll href="#payment_methods">{{__('frontend::properties.payment_methods.title')}}</a>
                            @if (!is_null($model->video) || !is_null($model->video2))
                                <a class="property_video" data-scroll href="#property_video">{{__('frontend::properties.property_video')}}</a>
                            @endif
                            <a class="informations_about_property" data-scroll href="#informations_about_property">{{__('frontend::properties.informations_about_property')}}</a>
                            <a class="features" data-scroll href="#features">{{__('frontend::properties.property_features')}}</a>  -->

                            <div class="row">
                                <div class="col-md-6">
                                    @if(!empty($model->roi))
                                   <div class="detials"> <span class="desc_price">{{__("frontend::main.price")}} </span> <span class="price"> {{$lowestPrice}}</span> </div>
                                   <div class="mt-3"> <span class="roi-box box-price">{{__("frontend::main.roi")}} {{$model->roi}}%</span> </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="boxdetials" >
                                       @if (count($allCategories = $model->allCategories->where('type', 'property_status')) != 0)
                                    <dl class="col-lg-6 dl-horizontal mb-0 border">
                                        <dt>{{ __('frontend::properties.property_status') }}</dt>
                                        <dd>
                                            @foreach ($allCategories->where('type', 'property_status') as $property_status)
                                             @if ($loop->last)
                                                <a href="{{ route('OpportunityController@filter', [
                                                    'opportunity_classification' => 'schools',
                                                    'contract' => 'for-sale',
                                                    'city' => 'istanbul',
                                                    'property_status' => $property_status->slug
                                                ]) }}" target="_blank">{!! $property_status->translateOrFirst()->title . ($loop->last ? '</a>' : '</a> - ') !!}
                                                @endif
                                            @endforeach
                                        </dd>
                                    </dl>
                                @endif
                                @if(count($allCategories = $model->allCategories) != 0)
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.available_offers') }}</dt>
                                        <dd>
                                            @foreach ($allCategories->where('type', 'contracts') as $contract)
                                             @if ($loop->last)
                                                <a href="{{ route('OpportunityController@filter', [
                                                    'opportunity_classification' => 'schools',
                                                    'contract' => $contract->slug,
                                                    'city' => 'istanbul'
                                                ]) }}" target="_blank">{{ $contract->translateOrFirst()->title . ($loop->last ? '' : ' - ') }}</a>
                                                @endif
                                            @endforeach
                                        </dd>
                                    </dl>
                                @endif
                            </div>  

                                </div>
                            </div>
                        </div>
                                    <div class="single homes-content details mb-30">
                            <h5 class="mb-4">{{__('frontend::main.summary') }}</h5>
                            @php
                                $about_desc = $model->translateOrFirst()->about;
                            @endphp
                            {!! $about_desc !!}
                        </div>

                  
                        @if($model->allCategories->isNotEmpty())
                            @php
                                $default = route('image', ['size' => '85x85', 'path' => 'defaults/attachments.png']);
                            @endphp
                            <div class="single homes-content details mb-30 property-features">
                                <h5 id="features">{{__('frontend::properties.property_features')}}</h5>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <ul class="homes-list clearfix">
                                            @foreach($model->allCategories->where('type', 'property_features') as $propertyFeature)
                                                <li class="p-0">
                                                    <a href="javascript:;" class="d-flex align-items-center">
                                                        <img class="lazy" width="40" src="{{$default}}" data-src="{{$propertyFeature->getTranslatedImage('85x85')}}">
                                                        <p class="m-0 {{app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2'}}">{{$propertyFeature->translateOrFirst()->title}}</p>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                    <!-- <div class="single homes-content details mb-30">
                            <h5 class="mb-4">{{__('frontend::properties.project_summary') }}</h5>
                            <dl class="row">
                                @if(!is_null($city = $model->city))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.city') }}</dt>
                                        <dd><a href="{{ route('OpportunityController@filter', [
                                            'opportunity_classification' => 'apartments',
                                            'contract' => 'for-sale',
                                            'city' => $city->native_name
                                        ]) }}" target="_blank">{{ $city->translateOrFirst()->name }}</a></dd>
                                    </dl>
                                @endif
                                @if(!is_null($area = $model->area))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.region') }}</dt>
                                        <dd><a href="{{ route('OpportunityController@filter', [
                                            'opportunity_classification' => 'apartments',
                                            'contract' => 'for-sale',
                                            'city' => 'istanbul',
                                            'area' => $area->native_name
                                        ]) }}" target="_blank">{{ $area->translateOrFirst()->name }}</a></dd>
                                    </dl>
                                @endif
                                @if(!is_null($category))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.property_type') }}</dt>
                                        <dd><a href="{{ route('OpportunityController@filter', [
                                            'opportunity_classification' => $category->slug,
                                            'contract' => 'for-sale',
                                            'city' => 'istanbul'
                                        ]) }}" target="_blank">type</a></dd>
                                    </dl>
                                @endif
                                @if($model->delivery_date)
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.delivery_date') }}</dt>
                                        <dd>{{ $model->delivery_date}}</dd>
                                    </dl>
                                @endif
                                @if(count($allCategories = $model->allCategories) != 0)
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.available_offers') }}</dt>
                                        <dd>
                                            @foreach ($allCategories->where('type', 'contracts') as $contract)
                                                <a href="{{ route('OpportunityController@filter', [
                                                    'opportunity_classification' => 'apartments',
                                                    'contract' => $contract->slug,
                                                    'city' => 'istanbul'
                                                ]) }}" target="_blank">{{ $contract->translateOrFirst()->title . ($loop->last ? '' : ' - ') }}</a>
                                            @endforeach
                                        </dd>
                                    </dl>
                                @endif
                                @if (count($allCategories = $model->allCategories) != 0)
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.property_status') }}</dt>
                                        <dd>
                                            @foreach ($allCategories->where('type', 'property_status') as $property_status)
                                                <a href="{{ route('OpportunityController@filter', [
                                                    'opportunity_classification' => 'apartments',
                                                    'contract' => 'for-sale',
                                                    'city' => 'istanbul',
                                                    'property_status' => $property_status->slug
                                                ]) }}" target="_blank">{!! $property_status->translateOrFirst()->title . ($loop->last ? '</a>' : '</a> - ') !!}
                                            @endforeach
                                        </dd>
                                    </dl>
                                @endif
                            </dl>
                            <h5 class="mt-5">{{ __('frontend::properties.distance_to.title') }}</h5>
                            <div class="row">
                                @if(!is_null($model->airport))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.distance_to.airport') }}</dt>
                                        <dd>{{ __('frontend::properties.distance', [ 'distance' => $model->airport]) }}</dd>
                                    </dl>
                                @endif
                                @if(!is_null($model->sea))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.distance_to.beach') }}</dt>
                                        <dd>{{ __('frontend::properties.distance', [ 'distance' => $model->sea]) }}</dd>
                                    </dl>
                                @endif
                                @if(!is_null($model->city_distance))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.distance_to.city_center') }}</dt>
                                        <dd>{{ __('frontend::properties.distance', [ 'distance' => $model->city_distance]) }}</dd>
                                    </dl>
                                @endif
                                @if(!is_null($model->school))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.distance_to.closest_school') }}</dt>
                                        <dd>{{ __('frontend::properties.distance', [ 'distance' => $model->school]) }}</dd>
                                    </dl>
                                @endif
                                @if(!is_null($model->university))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.distance_to.closest_university') }}</dt>
                                        <dd>{{ __('frontend::properties.distance', [ 'distance' => $model->university]) }}</dd>
                                    </dl>
                                @endif
                                @if(!is_null($model->hospital))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.distance_to.closest_hospital') }}</dt>
                                        <dd>{{ __('frontend::properties.distance', [ 'distance' => $model->hospital]) }}</dd>
                                    </dl>
                                @endif
                                @if(!is_null($model->mall))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.distance_to.closest_mall') }}</dt>
                                        <dd>{{ __('frontend::properties.distance', [ 'distance' => $model->mall]) }}</dd>
                                    </dl>
                                @endif
                                @if(!is_null($model->mosque))
                                    <dl class="col-lg-6 dl-horizontal mb-0">
                                        <dt>{{ __('frontend::properties.distance_to.closest_mosque') }}</dt>
                                        <dd>{{ __('frontend::properties.distance', [ 'distance' => $model->mosque]) }}</dd>
                                    </dl>
                                @endif
                            </div>
                        </div>
                    -->
            
                        <!--<div class="single homes-content details mb-30" id="payment_methods">
                            <h5 class="mb-4">{{__('frontend::properties.payment_methods.title') }}</h5>
                            <div style="overflow-x:auto;">
                                <table class="table table-hover text-center customized-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-capitalize">{{__('frontend::properties.payment_methods.payment_method')}}</th>
                                            <th class="text-capitalize">{{__('frontend::properties.payment_methods.frist_payment')}}</th>
                                            <th class="text-capitalize">{{__('frontend::properties.payment_methods.number_of_installments')}}</th>
                                            <th class="text-capitalize">{{__('frontend::properties.payment_methods.notes')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($model->payments as $payment)
                                            <tr>
                                                <td><span class="mr-1">{{!is_null($payCategory = $payment->payCategory) ? $payCategory->translateOrFirst()->title : ''}}</span></td>
                                                <td>{{$payment->first_pay}}</td>
                                                <td>{{!is_null($numberCategory = $payment->numberCategory) ? ($numberCategory->translateOrFirst()->title == '0' ? '-' : $numberCategory->translateOrFirst()->title) : ''}}</td>
                                                <td class="as-price"><span dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">{{$payment['notes_' . app()->getLocale()]}}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    -->
                       <!-- <div class="single homes-content details mb-30">
                            <h5 class="mb-4">{{__('frontend::properties.prices') }}</h5>
                            <div style="overflow-x:auto;">
                                <table class="table table-hover text-center customized-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-capitalize">{{__('frontend::properties.rooms')}}</th>
                                            <th class="text-capitalize">{{__('frontend::properties.spaces')}}</th>
                                            <th class="text-capitalize">{{__('frontend::properties.cat')}}</th>
                                            <th class="text-capitalize header-top">{{__('frontend::properties.prices')}}
                                                <div class="dropdown dropdown-currencies hidden-sm-down">
                                                    <form action="{{ route('FrontendController@setCurrency') }}" id="currency-form" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <input name="currency" type="hidden">
                                                        @php
                                                            $defaultLangImage = route('image', ['size' => '30x30', 'path' => 'defaults/attachments.png']);
                                                        @endphp
                                                        <button class="btn-dropdown dropdown-toggle" type="button" id="dropdownlang" data-toggle="dropdown" aria-haspopup="true">
                                                            {{ $selectedCurrency->currency_symbol }}
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownlang">
                                                            @foreach($currencies as $locale => $currency)
                                                                <li data-name="{{$currency->currency_code}}" class="change-currency text-{{ $langDirection == 'rtl' ? 'right' : 'left' }}">
                                                                    <img class="lazy" src="{{$defaultLangImage}}" width="25px" data-src="{{ $currency->getIconImage($currency,'150x150') }}" alt="{{ $currency->currency_code }}">
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </form>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($model->prices as $price)
                                            <tr>
                                                <td><span class="mr-1">{{$price->room_number + 0  . '+' . $price->salons_number}}</span></td>
                                                <td>{!!__('frontend::properties.space_number', ['number' => $price->lowest_area]) . ' - ' . __('frontend::properties.space_number', ['number' => $price->highest_area])!!}</td>
                                                <td>
                                                    @if(!empty($price->category))
                                                        {{$price->category->title}}
                                                    @endif
                                                </td>
                                                <td class="as-price">
                                                    <span dir="rtl">
                                                        {{-- {{$price->price_range}} --}}
                                                        {{$price->lowest_price . ' - ' . $price->highest_price}}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        -->
                        @if(!empty($model->video))
                            @php
                                $default = route('image', ['size' => '698x500', 'path' => 'defaults/attachments.png']);
                            @endphp
                            <div class="property wprt-image-video w50 pro property-v2" id="property_video">
                                <h5>{{__('frontend::properties.property_video')}}</h5>
                                <img class="lazy" src="{{$default}}" alt="image" 
                                data-src="{{!empty($model->translateOrFirst()->project_image1) ? $model->getTranslatedVideoImage1('698x500') : $model->getTranslatedImage('698x500')}}">
                                <a alt="{!! $model->translateOrFirst()->title !!} " class="icon-wrap popup-video popup-youtube" href="{{$model->video}}">
                                    <i class="fa fa-play"></i>
                                </a>
                                <div class="iq-waves">
                                    <div class="waves wave-1"></div>
                                    <div class="waves wave-2"></div>
                                    <div class="waves wave-3"></div>
                                </div>
                            </div>
                        @endif
                      
                     <!--   

                       @if(!empty($model->video2))
                            @php
                                $default = route('image', ['size' => '698x500', 'path' => 'defaults/attachments.png']);
                            @endphp
                            <div class="property wprt-image-video w50 pro property-v" id="property_video">
                                <h5>{{__('frontend::properties.property_video')}}</h5>
                                <img class="lazy" src="{{$default}}" alt="image" 
                                data-src="{{!empty($model->translateOrFirst()->project_image2) ? $model->getTranslatedVideoImage3('698x500') : $model->getTranslatedImage('698x500')}}">
                                <a alt="{!! $model->translateOrFirst()->title !!} " class="icon-wrap popup-video popup-youtube" href="{{$model->video2}}">
                                    <i class="fa fa-play"></i>
                                </a>
                                <div class="iq-waves">
                                    <div class="waves wave-1"></div>
                                    <div class="waves wave-2"></div>
                                    <div class="waves wave-3"></div>
                                </div>
                            </div>
                        @endif
                        @if(!empty($model->translateOrFirst()->description))
                            <div class="single homes-content details mb-30 project-description" id="informations_about_property">
                                <h5 class="mb-4">{{__('frontend::properties.informations_about_property') }}</h5>
                                @php
                                    $description_desc = $model->translateOrFirst()->description;
                                @endphp
                                {!! $description_desc !!}
                            </div>
                        @endif
                    -->
                     <!--    @if($model->allCategories->isNotEmpty())
                            @php
                                $default = route('image', ['size' => '85x85', 'path' => 'defaults/attachments.png']);
                            @endphp
                            <div class="single homes-content details mb-30 property-features">
                                <h5 id="features">{{__('frontend::properties.property_features')}}</h5>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <ul class="homes-list clearfix">
                                            @foreach($model->allCategories->where('type', 'property_features') as $propertyFeature)
                                                <li class="p-0">
                                                    <a href="javascript:;" class="d-flex align-items-center">
                                                        <img class="lazy" width="40" src="{{$default}}" data-src="{{$propertyFeature->getTranslatedImage('85x85')}}">
                                                        <p class="m-0 {{app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2'}}">{{$propertyFeature->translateOrFirst()->title}}</p>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <h5 class="mt-5" id="features">{{__('frontend::properties.property_services')}}</h5>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <ul class="homes-list clearfix">
                                            @foreach($model->allCategories->where('type', 'facilities') as $propertyFacility)
                                                <li class="p-0">
                                                    <a href="javascript:;" class="d-flex align-items-center">
                                                        <img class="lazy" width="40" src="{{$default}}" data-src="{{$propertyFacility->getTranslatedImage('85x85')}}">
                                                        <p class="m-0 {{app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2'}}">{{$propertyFacility->translateOrFirst()->title}}</p>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        -->
                       <!--  @if(!is_null($area = $model->area) && (!is_null($description =  $area->translateOrFirst()->description) || isset($peopleData) || isset($educationStatus) || isset($maritalCondition) || isset($ageDistribution) || isset($priceChangeRent) || isset($priceChangeSale)))
                            <div class="single homes-content details mb-30">
                                @if(!is_null($description =  $area->translateOrFirst()->description))
                                    <h5>{{__('frontend::properties.property_info')}}</h5>
                                    {!! $description !!}
                                @endif
                                @if(isset($peopleData) || isset($educationStatus) || isset($maritalCondition) || isset($ageDistribution) || isset($priceChangeRent) || isset($priceChangeSale))
                                    <h5 class="mb-4">{{__('cms::areas.stats.title')}}</h5>
                                    <div class="row">
                                        @if(isset($peopleData))
                                            @php
                                                $totalPopulation = $peopleData->where('key', 'demographic_data.people_data.total_population')->first();
                                                if(!is_null($totalPopulation))
                                                {
                                                    $totalPopulationValue = $totalPopulation->value;
                                                }
                                                else
                                                {
                                                    $totalPopulationValue = '-';
                                                }
                                                $growthRate = $peopleData->where('key', 'demographic_data.people_data.growth_rate')->first();
                                                if(!is_null($growthRate))
                                                {
                                                    $growthRateValue = $growthRate->value;
                                                }
                                                else
                                                {
                                                    $growthRateValue = '-';
                                                }
                                            @endphp
                                            <div class="col-lg-6">
                                                <div class="text-center">
                                                    <small><strong>{{__('cms::areas.custom_fields.demographic_data.people_data.total_population.label')}}:</strong> {{$totalPopulationValue/1000}} {{__('cms::areas.custom_fields.demographic_data.people_data.total_population.thousand')}}</small><br>
                                                    <small><strong>{{__('cms::areas.custom_fields.demographic_data.people_data.growth_rate.label')}}:</strong> {{$growthRateValue}}% / {{__('cms::areas.custom_fields.demographic_data.people_data.growth_rate.year')}}</small>
                                                </div>
                                                <div class="total-population-wrapper mt-3">
                                                    <div class="total-population-icon">
                                                        <div class="icon">
                                                            <img class="lazy loaded" data-src="https://kq9v7r75.rocketcdn.com/static/icons/detail/total-population.svg" src="https://kq9v7r75.rocketcdn.com/static/icons/detail/total-population.svg" data-loaded="true">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <h6 class="text-center my-4">{{__('cms::areas.custom_fields.demographic_data.people_data.ecomomic_and_social_evaluation.label')}}</h6>
                                                <div class="socio-economic-ranking-wrapper">
                                                    <div class="socio-economic-icon-wrapper">
                                                        <div class="socio-economic-icon">
                                                            <div class="radial-progress-container">
                                                                @php
                                                                    $total = 339.292;
                                                                    $ranks = [
                                                                        'A+'    => 100,
                                                                        'A'     => 90,
                                                                        'A-'    => 80,
                                                                        'B+'    => 70,
                                                                        'B'     => 60,
                                                                        'B-'    => 50,
                                                                        'C+'    => 40,
                                                                        'C'     => 30,
                                                                        'C-'    => 20,
                                                                    ];
                                                                    $current = $peopleData->where('key', 'demographic_data.people_data.ecomomic_and_social_evaluation')->first();
                                                                    if(!is_null($current) && isset($ranks[$current->value]))
                                                                    {
                                                                        $rankValue = round(($ranks[$current->value] * 339.292) / 100, 3);
                                                                        $rank = $current->value;
                                                                    }
                                                                    else
                                                                    {
                                                                        $rankValue = 339.292;
                                                                        $rank = '-';
                                                                    }
                                                                @endphp
                                                                <svg class="socio-economic-radial-progress" width="108" height="108" viewBox="0 0 120 120">
                                                                    <circle class="progress-meter" cx="60" cy="60" r="54" stroke-width="12"></circle>
                                                                    <circle class="progress-value" cx="60" cy="60" r="54" stroke-width="12" style="stroke-dasharray: 339.292; stroke-dashoffset: {{339.292 - $rankValue}};"></circle>
                                                                </svg>
                                                                <div class="radial-progress-point">
                                                                    <div dir="ltr">{{$rank}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($educationStatus))
                                            <div class="col-lg-6">
                                                <h6 class="text-center chart-title">{{__('cms::areas.custom_fields.demographic_data.education_status.title')}}</h6>
                                                @include('frontend::properties.charts.education-state')
                                            </div>
                                        @endif
                                        @if(isset($maritalCondition))
                                            <div class="col-lg-6">
                                                <h6 class="text-center chart-title">{{__('cms::areas.custom_fields.demographic_data.marital_condition.title')}}</h6>
                                                @include('frontend::properties.charts.marital-condition')
                                            </div>
                                        @endif
                                        @if(isset($ageDistribution))
                                            <div class="col-lg-3">
                                            </div>
                                            <div class="col-lg-6">
                                                <h6 class="text-center chart-title">{{__('cms::areas.custom_fields.demographic_data.people_data.title')}}</h6>
                                                @include('frontend::properties.charts.age-distribution')
                                            </div>
                                        @endif
                                        @if(isset($priceChangeRent))
                                            <div class="col-lg-12">
                                                <h6 class="text-center mt-5">{{__('cms::areas.custom_fields.price_change.rent.title')}}</h6>
                                                @include('frontend::properties.charts.price-change', ['title' => 'priceChangeRent', 'priceChange' => $priceChangeRent])
                                            </div>
                                        @endif
                                        @if(isset($priceChangeSale))
                                            <div class="col-lg-12">
                                                <h6 class="text-center mt-5">{{__('cms::areas.custom_fields.price_change.sale.title')}}</h6>
                                                @include('frontend::properties.charts.price-change', ['title' => 'priceChangeSale', 'priceChange' => $priceChangeSale])
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if($model->attachments->where('input_name', 'image_layouts')->isNotEmpty())
                            <div class="single homes-content details mb-30 second-images">
                                <h5 class="mb-4">{{__('frontend::properties.image_layouts.label') }}</h5>
                                <div class="slider plan-slider sync1 customized-slider owl-carousel {{ $langDirection == 'rtl' ? 'owl-rtl' : '' }}" data-iterations="{{count($model->attachments->where('input_name', 'image_layouts'))}}">
                                    @php
                                        $default    = route('image', ['size' => '1920x1280', 'path' => 'defaults/attachments.png']);
                                        $default2   = route('image', ['size' => '192x128', 'path' => 'defaults/attachments.png']);
                                    @endphp
                                    @foreach ($model->attachments->where('input_name', 'image_layouts') as  $attachment)
                                        <img class="lazy" data-input-name="{{$attachment->input_name}}" data-index="{{$loop->index}}" 
                                        src="{{$default}}" data-src="{{$attachment->getUid('1920x1280')}}" 
                                        alt="{{$attachment->title . (!is_null($attachment->description) ? ', ' . $attachment->description : '') }}">
                                    @endforeach
                                </div>
                                <div class="navigation-thumbs plan-slider sync2 owl-carousel {{ $langDirection == 'rtl' ? 'owl-rtl' : '' }}">
                                    @foreach ($model->attachments->where('input_name', 'image_layouts') as  $attachment)
                                        <img class="lazy" data-input-name="{{$attachment->input_name}}" data-index="{{$loop->index}}" 
                                        src="{{$default2}}" data-src="{{$attachment->getUid("192x128")}}" 
                                        alt="{{$attachment->title . (!is_null($attachment->description) ? ', ' . $attachment->description : '') }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif -->

                           
                            <div class="single homes-content details mb-30 property-features">
                                <h5 id="features">{{__('frontend::main.address')}}</h5>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-md-6">
                                            <div class="row">
                                        <div class="col-md-6 adress">
                                                <span class="city">{{__('frontend::properties.city')}}</span>
                                                <span>{{__('frontend::main.Istanbul')}} </span>
                                                </div>
                                               <div class="col-md-6 adress">
                                              
                                                <span class="city">{{__('frontend::properties.region')}}</span>
                                                <span> {{__('frontend::main.basaksehir')}}</span>

                                                </div> 
                                                <div class="col-md-12 adress">
                                                    <span class="city">{{__('frontend::main.address')}}</span>
                                                      @if(isset($contents['address']))
                                    @php
                                        $address = '';
                                        if(isset($contents['address']) && !is_null($contents['address']))
                                        {
                                            if(!empty($contents['address']->translateOrFirst()->description))
                                            {
                                                $address = $contents['address']->translateOrFirst()->description;
                                            }
                                            else
                                            {
                                                $address = $contents['address']->value;
                                            }
                                        }
                                    @endphp
                                    <div class="info">
                                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                                        <p class="in-p">{{$address}}</p>
                                    </div>
                                @endif
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                        @if(isset($contents['map']))
                            @php
                                $map = '';
                                if(isset($contents['map']) && !is_null($contents['map']))
                                {
                                    if(!empty($contents['map']->translateOrFirst()->description))
                                    {
                                        $map = $contents['map']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $map = $contents['map']->value;
                                    }
                                }
                            @endphp
                            <iframe src="{{$map}}" style="border:0;width:100%;height: 250px;" allowfullscreen="" loading="lazy"></iframe>
                        @endif
                    </div>

                                    </div>
                                </div>
                            </div>
                    </div>
                    <aside class="col-lg-4 col-md-12 car">
                        @include('frontend::includes.side-opp')
                    </aside>
                </div>


            </div>
        </section>

@if(isset($recentProjectsOpp))
        <section class="recent">
    <div class="custom-container">
        <div class="row">
            <div class="col-12">
                <div class="title">
                    <h2>{{__("frontend::main.similar_opp")}}</h2>
                </div>
                <div class="desc">
                    {{__("frontend::main.recent_desc")}}
                </div>
            </div>
            <div class="col-12">
                <div class="portfolio col-xl-12 p-0 mt-5">
                    <div class="slick-lancers slick-lancers-2">
                        @php
                            $defaultLangImage = route('image', ['size' => '1000x750', 'path' => 'defaults/base.png']);
                        @endphp
                        @foreach($recentProjectsOpp as $project)
                            @include('frontend::includes.project_single_opp', ['project' => $project,'defaultLangImage' => $defaultLangImage])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

 @include('frontend::index.contact')
    </div>
@endsection


@push('scripts')
    <script>
        // side features
        $('.slick-lancers.slick-lancers-4').slick({
            infinite: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            rtl: _isRtl,
            arrows: false,
            adaptiveHeight: true,
            responsive: [
            {
                breakpoint: 1292,
                settings: {
                    dots: true,
                    arrows: false,
                    items: 1,
                }
            }, 
            {
                breakpoint: 993,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: true,
                    arrows: false
                }
            }, 
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: true,
                    arrows: false
                }
            }]
        });
    </script>
    <script>
        $(".additional_details").click(function() {
            $('.additional_details').addClass('active');
            $('.payment_methods').removeClass('active');
            $('.property_video').removeClass('active');
            $('.informations_about_property').removeClass('active');
            $('.features').removeClass('active');
        });
        $(".payment_methods").click(function() {
            $('.payment_methods').addClass('active');
            $('.additional_details').removeClass('active');
            $('.property_video').removeClass('active');
            $('.informations_about_property').removeClass('active');
            $('.features').removeClass('active');
        });
        $(".property_video").click(function() {
            $('.property_video').addClass('active');
            $('.payment_methods').removeClass('active');
            $('.additional_details').removeClass('active');
            $('.informations_about_property').removeClass('active');
            $('.features').removeClass('active');
        });
        $(".informations_about_property").click(function() {
            $('.informations_about_property').addClass('active');
            $('.payment_methods').removeClass('active');
            $('.property_video').removeClass('active');
            $('.additional_details').removeClass('active');
            $('.features').removeClass('active');
        });
        $(".features").click(function() {
            $('.features').addClass('active');
            $('.payment_methods').removeClass('active');
            $('.property_video').removeClass('active');
            $('.informations_about_property').removeClass('active');
            $('.additional_details').removeClass('active');
        });
        
       $('.slick-lancers.slick-lancers-2').slick({
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
            rtl: _isRtl,
            arrows: false,
            adaptiveHeight: true,
            responsive: [
            {
                breakpoint: 1292,
                settings: {
                    dots: true,
                    arrows: false,
                    items: 3,
                }
            }, 
            {
                breakpoint: 993,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    dots: true,
                    arrows: false
                }
            }, 
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: true,
                    arrows: false
                }
            }]
        });
    </script>
    <script>
        const jumptToSlider = (type) => {
            console.log($(`[data-input-name="${type}"]`).attr('data-index'))
            $(".slider").trigger('to.owl.carousel', $(`[data-input-name="${type}"]`).attr('data-index'))
        }
        var _isRtl = $('html').attr('dir') == 'rtl';
        $(document).ready(function () {
            var sync1 = $(".slider.property-image-slider");
            var sync2 = $(".navigation-thumbs.property-image-slider");
            var thumbnailItemClass = '.owl-item';
            var slides = sync1.owlCarousel({
                rtl: _isRtl,
                video: true,
                startPosition: 12,
                items: 1,
                loop: true,
                margin: 10,
                autoplay: true,
                lazyLoad: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: false,
                nav: true,
                navText: [
                    '<i class="fa fa-arrow-' + (_isRtl ? 'right' : 'left') + '"></i>',
                    '<i class="fa fa-arrow-' + (_isRtl ? 'left' : 'right') + '"></i>'
                ],
                dots: false
            }).on('changed.owl.carousel', syncPosition);
            function syncPosition(el) {
                $owl_slider = $(this).data('owl.carousel');
                var loop = $owl_slider.options.loop;
                if (loop) {
                    var count = el.item.count - 1;
                    var current = Math.round(el.item.index - (el.item.count / 2) - .5);
                    if (current < 0) {
                        current = count;
                    }
                    if (current > count) {
                        current = 0;
                    }
                } else {
                    var current = el.item.index;
                }
                var owl_thumbnail = sync2.data('owl.carousel');
                var itemClass = "." + owl_thumbnail.options.itemClass;
                var thumbnailCurrentItem = sync2
                    .find(itemClass)
                    .removeClass("synced")
                    .eq(current);
                thumbnailCurrentItem.addClass('synced');
                if (!thumbnailCurrentItem.hasClass('active')) {
                    var duration = 300;
                    sync2.trigger('to.owl.carousel', [current, duration, true]);
                }
            }
            var thumbs = sync2.owlCarousel({
                rtl: $('html').attr('dir') == 'rtl',
                startPosition: 12,
                items: 8,
                loop: false,
                margin: 10,
                autoplay: false,
                lazyLoad: true,
                nav: false,
                dots: false,
                onInitialized: function (e) {
                    var thumbnailCurrentItem = $(e.target).find(thumbnailItemClass).eq(this._current);
                    thumbnailCurrentItem.addClass('synced');
                },
            })
            .on('click', thumbnailItemClass, function (e) {
                e.preventDefault();
                var duration = 300;
                var itemIndex = $(e.target).parents(thumbnailItemClass).index();
                sync1.trigger('to.owl.carousel', [itemIndex, duration, true]);
            }).on("changed.owl.carousel", function (el) {
                var number = el.item.index;
                $owl_slider = sync1.data('owl.carousel');
                $owl_slider.to(number, 100, true);
            });
            var sync3 = $(".slider.plan-slider");
            var sync4 = $(".navigation-thumbs.plan-slider");
            var thumbnailItemClass = '.owl-item';
            var slides = sync3.owlCarousel({
                rtl: _isRtl,
                video: true,
                startPosition: 12,
                items: 1,
                loop: true,
                margin: 10,
                autoplay: true,
                lazyLoad: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: false,
                nav: true,
                navText: [
                    '<i class="fa fa-arrow-' + (_isRtl ? 'right' : 'left') + '"></i>',
                    '<i class="fa fa-arrow-' + (_isRtl ? 'left' : 'right') + '"></i>'
                ],
                dots: false
            }).on('changed.owl.carousel', syncPosition2);
            function syncPosition2(el) {
                $owl_slider = $(this).data('owl.carousel');
                var loop = $owl_slider.options.loop;
                if (loop) {
                    var count = el.item.count - 1;
                    var current = Math.round(el.item.index - (el.item.count / 2) - .5);
                    if (current < 0) {
                        current = count;
                    }
                    if (current > count) {
                        current = 0;
                    }
                } else {
                    var current = el.item.index;
                }
                var owl_thumbnail = sync4.data('owl.carousel');
                var itemClass = "." + owl_thumbnail.options.itemClass;
                var thumbnailCurrentItem = sync4
                    .find(itemClass)
                    .removeClass("synced")
                    .eq(current);
                thumbnailCurrentItem.addClass('synced');
                if (!thumbnailCurrentItem.hasClass('active')) {
                    var duration = 300;
                    sync4.trigger('to.owl.carousel', [current, duration, true]);
                }
            }
            var thumbs = sync4.owlCarousel({
                rtl: $('html').attr('dir') == 'rtl',
                startPosition: 12,
                items: 8,
                loop: false,
                margin: 10,
                autoplay: false,
                video:true,
                lazyLoad: true,
                nav: false,
                dots: false,
                onInitialized: function (e) {
                    var thumbnailCurrentItem = $(e.target).find(thumbnailItemClass).eq(this._current);
                    thumbnailCurrentItem.addClass('synced');
                },
            })
            .on('click', thumbnailItemClass, function (e) {
                e.preventDefault();
                var duration = 300;
                var itemIndex = $(e.target).parents(thumbnailItemClass).index();
                sync3.trigger('to.owl.carousel', [itemIndex, duration, true]);
            }).on("changed.owl.carousel", function (el) {
                var number = el.item.index;
                $owl_slider = sync3.data('owl.carousel');
                $owl_slider.to(number, 100, true);
            });
            $('.slick-carousel').each(function() {
                var slider = $(this);
                $(this).slick({
                    infinite: true,
                    dots: false,
                    arrows: false,
                    centerMode: true,
                    centerPadding: '0'
                });
                $(this).closest('.slick-slider-area').find('.slick-prev').on("click", function() {
                    slider.slick('slickPrev');
                });
                $(this).closest('.slick-slider-area').find('.slick-next').on("click", function() {
                    slider.slick('slickNext');
                });
            });
        });
    </script>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-618a885965747152"></script>
    {{-- @include('frontend::includes.filter_scripts') --}}
@endpush
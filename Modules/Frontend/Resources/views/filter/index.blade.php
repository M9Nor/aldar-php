@extends('frontend::layouts.master')
    @php
        // $test       = session('aaaaaa');
        // dd($test);
        $seodescription    = '';
        $seokeywords       = '';
        if(isset($contents['seodescription'])){
            if(!empty($contents['seodescription']->translateOrFirst()->description))
            {
                $seodescription = $contents['seodescription']->translateOrFirst()->description;
            }
            else
            {
                $seodescription = $contents['seodescription']->val;
            }
        }
        if(isset($contents['seokeywords'])){
            if(!empty($contents['seokeywords']->translateOrFirst()->description))
            {
                $seokeywords = $contents['seokeywords']->translateOrFirst()->description;
            }
            else
            {
                $seokeywords = $contents['seokeywords']->val;
            }
        }
        $image      = \Module::asset('frontend:assets/header_logo.svg');
    @endphp

    @include('frontend::seo.meta', [
        'options' => [
            'title'         => __("frontend::main.site_name"),
            'sub_title'     => $texts['city_title'],
            'description'   => $seodescription,
            'image'         => $image,
            'keywords'      => $seokeywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'         => __("frontend::main.site_name"),
            'sub_title'     => $texts['city_title'],
            'description'   => $seodescription,
            'image'         => $image,
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'         => __("frontend::main.site_name"),
            'sub_title'     => $texts['city_title'],
            'description'   => $seodescription,
            'image'         => $image,
            'keywords'      => $seokeywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'         => __("frontend::main.site_name"),
            'sub_title'     => $texts['city_title'],
            'description'   => $seodescription,
            'image'         => $image,
        ],
    ])

@section('content')

    <section class="filter-index">
        <div class="custom-container">
            <div class="row">
                <div class="col-md-8 col-12">
                    <div class="block-heading">
                        <div class="row">
                            <div class="col-lg-6 col-md-5 col-2">
                                <h1>    
                                    <span class="heading-icon">
                                        <i class="fa fa-th-list"></i>
                                    </span>
                                    <span class="hidden-sm-down">{{__('frontend::listing.real_estates')}}</span>
                                    <small class="count">[{{__('frontend::listing.general.matched_result')}}:{{$all_properties->total()}}]</small>
                                </h1>
                            </div>
                            <div class="col-lg-6 col-md-7 col-10 cod-pad">
                                <div class="sorting-options">
                                    <select id="date-sort" name="sort" class="sorting" title="{{__('frontend::listing.general.sort_by')}}">
                                        <option @if ( isset($params['sort']) && $params['sort']=='date_desc' ) selected
                                            @endif value="date_desc">{{__('frontend::listing.general.newer')}}</option>
                                        <option @if ( isset($params['sort']) && $params['sort']=='date_asc' ) selected
                                            @endif value="date_asc">{{__('frontend::listing.general.older')}}</option>
                                        <option @if ( isset($params['sort']) && $params['sort']=='price_desc' )selected
                                            @endif value="price_desc">{{__('frontend::listing.general.price_desc')}}
                                        </option>
                                        <option @if ( isset($params['sort']) && $params['sort']=='price_asc' ) selected
                                            @endif value="price_asc">{{__('frontend::listing.general.price_asc')}}
                                        </option>
                                        <option @if ( isset($params['sort']) && $params['sort']=='area_desc' ) selected
                                            @endif value="area_desc">{{__('frontend::listing.general.area_desc')}}
                                        </option>
                                        <option @if ( isset($params['sort']) && $params['sort']=='area_asc' ) selected
                                            @endif value="area_asc">{{__('frontend::listing.general.area_asc')}}
                                        </option>
                                        <option @if ( isset($params['sort']) && $params['sort']=='room_desc' ) selected
                                            @endif value="room_desc">{{__('frontend::listing.general.rooms_desc')}}
                                        </option>
                                        <option @if ( isset($params['sort']) && $params['sort']=='room_asc' ) selected
                                            @endif value="room_asc">{{__('frontend::listing.general.rooms_asc')}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filter-body">
                        <h5 class="mb-4">{{$texts['city_title']}}</h5>
                        <p class="mb-4">{!!$texts['brief']!!}</p>
                        <div class="">
                            <ul class="col-12 nav nav-pills nav-fill mb-4 filter-nav">
                                <li class="nav-item">
                                    <a class="nav-link active add-a" data-toggle="tab" role="tab"
                                        href="#properties">{{__('frontend::listing.general.projects_tab')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link delete-a" data-toggle="tab" role="tab"
                                        href="#brief-description">{{__('frontend::listing.general.overview_tab')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link delete-a {{ empty($texts['place_tab_description']) ? 'disabled' : '' }}"
                                        data-toggle="tab" role="tab"
                                        href="#area-report">{{__('frontend::listing.general.place_overview_tab')}}</a>
                                </li>
                            </ul>
                            <div class="col-12">
                                <div class="tab-content filter-tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active add-s" id="properties" role="tabpanel"
                                        aria-labelledby="properties-tab">
                                        <div class="row">
                                            @php
                                                $defaultLangImage = route('image', ['size' => '1000x750', 'path' => 'defaults/base.png']);
                                            @endphp
                                            {{-- {{dd($all_properties)}} --}}
                                            @forelse($all_properties as $property)
                                                <div class="col-lg-6 col-md-6 col-xs-12 portfolio p0">
                                                    @include('frontend::includes.project_single_2', ['project' => $property,'defaultLangImage' => $defaultLangImage])
                                                </div>
                                            @empty
                                                <div class="search-result w-100">
                                                    <div class="alert text-center">
                                                        {{__('frontend::strings.no_results_message')}}</div>
                                                </div>
                                            @endforelse
                                        </div>
                                        <nav aria-label="..." class="pt-55">
                                            {{-- {{ $all_properties->links('frontend::includes.pagination') }} --}}
                                            {{$all_properties->appends($_GET)->links() }}
                                        </nav>
                                    </div>
                                    <div class="tab-pane fade delete-a" id="brief-description" role="tabpanel"
                                        aria-labelledby="brief-description-tab">
                                        <div class="dynamic-description">
                                            {!!str_replace('<p>&nbsp;</p>', '', (string) $texts['overview'])!!}
                                        </div>
                                    </div>
                                    <div class="tab-pane fade delete-a" id="area-report" role="tabpanel"
                                    aria-labelledby="area-report-tab">
                                        <div class="dynamic-description">
                                            {!!str_replace('<p>&nbsp;</p>', '', (string) $texts['place_tab_description'])!!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="slide-form">
                        <h3 class="mb-2 widget-boxed-header">{{__("frontend::main.filter_section.title")}}</h3>
                        <form class="" action="{{ route('ListingController@prepareFilter') }}" name="contactform" method="post" >
                            @csrf
                            <div class="form-group">
                                <label for="">{{__('frontend::main.filter_section.dropdown.item_1')}}</label>
                                <select class="form-control city-select" name="city">
                                    <option value=""  @if(!$city) selected @endif>
                                            {{__('frontend::main.filter_section.dropdown.select_cities')}}
                                        </option>
                                        <option value="turkey"  @if($city == "turkey") selected @endif>
                                            {{__('frontend::main.filter_section.dropdown.all_cities')}}
                                        </option>
                                    @foreach($cities[app()->getLocale()] as $cityItem)
                                        <option {{ (strtolower($cityItem->native_name) == $city && $city !== "turkey") ? 'selected' : '' }} data-id="{{$cityItem->id}}" value="{{strtolower($cityItem->native_name)}}">
                                            {{$cityItem->translateOrFirst(app()->getLocale())->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('frontend::main.filter_section.dropdown.item_2')}}</label>
                                <select name="area" class="form-control region-select" title="{{__('frontend::main.filter_section.dropdown.item_2')}}">
                                      <option @if(!$area) selected @endif value="">
                                        {{__('frontend::main.filter_section.dropdown.item_2')}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('frontend::main.filter_section.dropdown.item_3')}}</label>
                                <select class="form-control" name="contract" >
                                   <option value="" @if($contract == "all") selected @endif>
                                     {{__('frontend::main.filter_section.dropdown.select_contracts')}}
                                    </option>

                                    <option value="all" @if($contract == "all_contract") selected @endif>
                                    {{__('frontend::main.filter_section.dropdown.all_contracts')}}</option>
                                    @foreach($filters->where('type','contracts') as $category)
                                        <option data-id="{{$category->id}}" value="{{$category->slug}}" {{ $category->slug == $contract ? 'selected' : '' }}>
                                            {{$category->translateOrFirst(app()->getLocale())->title}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('frontend::main.filter_section.dropdown.item_4')}}</label>
                                <select class="form-control" name="property_classification" >
                                 
                                         <option value=""@if($property_classification == "properties") selected @endif>
                                     {{__('frontend::main.filter_section.dropdown.select_properties')}}
                                    </option>
                                    <option value="all" @if($property_classification == "all_properties") selected @endif>
                                     {{__('frontend::main.filter_section.dropdown.all_property_classification')}}
                                    </option>
                                    @foreach($filters->where('type','property_classifications')->whereNotNull('parent_id') as $category)
                                        <option {{ $category->slug == $property_classification ? 'selected' : '' }} data-id="{{$category->id}}" value="{{$category->slug}}">
                                            {{$category->translateOrFirst(app()->getLocale())->title}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('frontend::main.filter_section.dropdown.item_7')}}</label>
                                @php
                                    $selected = isset($params['property_status']) ? $params['property_status'] : null;
                                @endphp
                                <select id="property_status" class="form-control" name="property_status" >
                                     <option value="" @if (!$selected) selected @endif>
                                     {{__('frontend::main.filter_section.dropdown.select_property_status')}}
                                    </option>
                                    @foreach($filters->where('type','property_status') as $category)
                                        <option data-id="{{$category->id}}" value="{{$category->slug}}" {{ $category->slug == $selected ? 'selected' : '' }}>
                                            {{$category->translateOrFirst(app()->getLocale())->title}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="min-area">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_20')}}</label>
                                    @php
                                        $value = isset($params['min_area']) ? $params['min_area'] : null;
                                    @endphp
                                    <input style="font-size:13px" value="{{ $value }}" type="number" class="form-control input-custom input-full" 
                                        name="min_area" placeholder="{{__('frontend::main.filter_section.dropdown.item_24')}}" aria-required="true">
                                </div>
                                <div class="max-area">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_21')}}</label>
                                    @php
                                        $value = isset($params['max_area']) ? $params['max_area'] : null;
                                    @endphp
                                    <input style="font-size:13px" value="{{ $value }}" type="number" class="form-control input-custom input-full" 
                                    name="max_area" placeholder="{{__('frontend::main.filter_section.dropdown.item_25')}}" aria-required="true">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="min-price">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_18')}}</label>
                                    @php
                                        $value = isset($params['min_price']) ? $params['min_price'] : null;
                                    @endphp
                                    <input style="font-size:13px" value="{{ $value }}" type="number" class="form-control input-custom input-full" 
                                        name="min_price" placeholder="{{__('frontend::main.filter_section.dropdown.item_22')}}" aria-required="true">
                                </div>
                                <div class="max-price">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_19')}}</label>
                                    @php
                                        $value = isset($params['max_price']) ? $params['max_price'] : null;
                                    @endphp
                                    <input style="font-size:13px" value="{{ $value }}" type="number" class="form-control input-custom input-full"
                                        name="max_price" placeholder="{{__('frontend::main.filter_section.dropdown.item_23')}}" aria-required="true">
                                </div>
                            </div>
                            <div class="form-group">
                                <a href="javascript:;" class="more-filter widget-boxed-header">
                                    <h4>
                                        <i class="fa fa-filter"></i>
                                        {{__('frontend::main.filter_section.advance_filter')}}
                                    </h4>
                                </a>
                            </div>
                            <div class="filter-div" style="display: none">
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_10')}}</label>
                                    <select name="payment" id="payment" class="selectpicker form-control payment-select" title="{{__('frontend::main.filter_section.dropdown.item_9')}}">
                                        <option  data-hidden="true" @if (!isset($params['payment']) || empty($params['payment'])) selected @endif value="">{{__('frontend::main.filter_section.dropdown.item_9')}}</option>
                                        @foreach($filters->where('type','payments')->whereNull('parent_id') as $category)
                                            <option @if (isset($params['payment']) && $category->slug == $params['payment']) selected @endif data-id="{{$category->id}}"   value="{{$category->slug}}">{{$category->translateOrFirst(app()->getLocale())->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_10')}}</label>
                                    <select name="installment[]" id="installment" class="selectpicker form-control installment-select" multiple title="{{__('frontend::main.filter_section.dropdown.item_10')}}">
                                        <option data-hidden="true" @if (!isset($installments) || count($installments) == 0) selected @endif value="">{{__('frontend::main.filter_section.dropdown.item_10')}}</option>
                                        @if($installments->isNotEmpty())
                                            @foreach($installments as $category)
                                                <option selected value="{{$category->slug}}">{{$category->translateOrFirst(app()->getLocale())->title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_11')}}</label>
                                    <select name="rooms[]" id="rooms" class="selectpicker form-control" multiple title="{{__('frontend::main.filter_section.dropdown.item_11')}}">
                                        <option  data-hidden="true" @if (!isset($params['rooms']) || !is_array($params['rooms']) || count($params['rooms']) == 0) selected @endif value="">{{__('frontend::main.filter_section.dropdown.item_11')}}</option>
                                        @foreach($filters->where('type','Rooms') as $category)
                                            <option
                                                @if (isset($params['rooms']) && in_array($category->translateOrFirst(app()->getLocale())->title,$params['rooms'])) selected @endif 
                                                value="{{$category->translateOrFirst(app()->getLocale())->title}}">{{$category->translateOrFirst(app()->getLocale())->title}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_12')}}</label>
                                    <select name="living[]" id="living" class="selectpicker form-control" multiple title="{{__('frontend::main.filter_section.dropdown.item_12')}}">
                                        <option  data-hidden="true" @if (!isset($params['living']) || !is_array($params['living']) || count($params['living']) == 0) selected @endif value="">{{__('frontend::main.filter_section.dropdown.item_12')}}</option>
                                        @foreach($filters->where('type','LivingRooms') as $category)
                                            <option 
                                                @if (isset($params['living']) && in_array($category->translateOrFirst(app()->getLocale())->title,$params['living'])) selected @endif  
                                                value="{{$category->translateOrFirst(app()->getLocale())->title}}">{{$category->translateOrFirst(app()->getLocale())->title}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_15')}}</label>
                                    <select name="facilities[]" id="facilities" class="selectpicker form-control" multiple title="{{__('frontend::main.filter_section.dropdown.item_15')}}">
                                        <option  data-hidden="true" @if (!isset($params['facilities']) || !is_array($params['facilities']) || count($params['facilities']) == 0) selected @endif value="">{{__('frontend::main.filter_section.dropdown.item_15')}}</option>
                                        @foreach($filters->where('type','facilities') as $category)
                                            <option  
                                                @if (isset($params['facilities']) && in_array($category->slug,$params['facilities'])) selected @endif  
                                                value="{{$category->slug}}">{{$category->translateOrFirst(app()->getLocale())->title}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_8')}}</label>
                                    <select name="features[]" id="features" class="selectpicker form-control" multiple title="{{__('frontend::main.filter_section.dropdown.item_8')}}">
                                        @foreach($filters->where('type','property_features') as $category)
                                            <option @if (isset($params['features']) && in_array($category->slug,$params['features'])) selected @endif value="{{$category->slug}}">{{$category->translateOrFirst(app()->getLocale())->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{__('frontend::main.filter_section.search')}}
                            </button>
                        </form>
                    </div>
                    <div class="side-tags mt-4">
                        @include('frontend::includes.side_contact')
                    </div>
                    <div class="side-projects mt-4">
                        @if(isset($featuredProjects[app()->getLocale()]) && $featuredProjects[app()->getLocale()]->isNotEmpty())
                            <div class="recent-post py-1">
                                @include('frontend::includes.side_projects', ['projects' => $featuredProjects[app()->getLocale()],'title' => __('frontend::listing.side.featured_properties')])
                            </div>
                        @endif
                    </div>
                    <div class="side-tags mt-4">
                        @if(isset($model))
                            @if($model->tags->isNotEmpty())
                                <div class="recent-post">
                                    <h5 class="font-weight-bold mb-4 widget-boxed-header">{{__('cms::includes.aside.tags')}}</h5>
                                    <div class="tags flex-wrap">
                                        @foreach ($model->tags as $tag)
                                            <span><a href="{{ route('ListingController@search', ['q' => $tag->translateOrFirst()->text]) }}" class="btn btn-outline-primary mb-2">{{$tag->translateOrFirst()->text}}</a></span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            @if(isset($popularTags[app()->getLocale()]) && $popularTags[app()->getLocale()]->isNotEmpty())
                                <div class="recent-post">
                                    <h5 class="font-weight-bold mb-4 widget-boxed-header">{{__('frontend::main.popular_tags')}}</h5>
                                    <div class="tags flex-wrap">
                                        @foreach ($popularTags[app()->getLocale()] as $tag)
                                            <span><a href="{{ route('ListingController@search', ['q' => $tag->translateOrFirst()->text]) }}" class="btn btn-outline-primary mb-2">{{$tag->translateOrFirst()->text}}</a></span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection


@push('scripts')

    @include('frontend::includes.filter_scripts')

    <script>
        let lang = "{{app()->getLocale()}}";
        setTimeout(function() { 
            if(lang == 'ar'){
                $('.pagination li:first span span').html('السابق');
                $('.pagination li:last a span').html('التالي');
                $('.pagination .disabled span span').html('السابق');
                $('.flaticon-right-arrow').html('السابق');
            }else{
                $('.pagination li:first span span').html('previous');
                $('.pagination .disabled span span').html('previous');
                $('.flaticon-right-arrow').html('previous');
                $('.pagination li:last a span').html('next');
            }
        }, 1000);
    </script>
@endpush
<div class="int_content_wraapper int_content_left">
    <!--===Start Revolution Slider===-->
    <div class="int_banner_slider">
        <div class="banner_box_wrapper">
            <div class="custom-container">
                <div class="row ">
                    <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 align-self-center p-0">
                        <div class="slide-form">
                            <h3 class="mb-4">{{__("frontend::main.filter_section.title")}}</h3>
                            <form class="" action="{{ route('ListingController@prepareFilter') }}" name="contactform" method="post" >
                                @csrf
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_1')}}</label>
                                    <select class="form-control city-select" name="city" required="">
                                            <option value="" selected="">
                                             {{__('frontend::main.filter_section.dropdown.select_cities')}}
                                            </option>
                                            <option value="turkey">
                                             {{__('frontend::main.filter_section.dropdown.all_cities')}}
                                            </option>
                                        @foreach($cities[app()->getLocale()] as $city)
                                            <option data-id="{{$city->id}}" value="{{strtolower($city->native_name)}}">{{$city->translateOrFirst(app()->getLocale())->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_2')}}</label>
                                    <select name="area" class="form-control region-select" title="{{__('frontend::main.filter_section.dropdown.item_2')}}">
                                        <option value="">{{__('frontend::main.filter_section.dropdown.item_2')}}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_3')}}</label>
                                    <select class="form-control" name="contract" >
                                            <option value="">
                                             {{__('frontend::main.filter_section.dropdown.select_contracts')}}
                                            </option>
                                            <option value="all">
                                            {{__('frontend::main.filter_section.dropdown.all_contracts')}}</option>
                                        @foreach($filters->where('type','contracts') as $category)
                                            <option data-id="{{$category->id}}" value="{{$category->slug}}">{{$category->translateOrFirst(app()->getLocale())->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_4')}}</label>
                                    <select class="form-control" name="property_classification" >
                                            <option value="">
                                             {{__('frontend::main.filter_section.dropdown.select_properties')}}
                                            </option>
                                            <option value="all">
                                             {{__('frontend::main.filter_section.dropdown.all_property_classification')}}
                                            </option>
                                           
                                        @foreach($filters->where('type','property_classifications')->whereNotNull('parent_id') as $category)
                                            <option data-id="{{$category->id}}" value="{{$category->slug}}">{{$category->translateOrFirst(app()->getLocale())->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('frontend::main.filter_section.dropdown.item_7')}}</label>
                                    <select class="form-control" name="property_status" >
                                            <option value="">
                                             {{__('frontend::main.filter_section.dropdown.select_property_status')}}
                                            </option>
                                            <!-- <option value="all">
                                             {{__('frontend::main.filter_section.dropdown.all_property_status')}}
                                            </option>
                                            -->
                                            @foreach($filters->where('type','property_status') as $category)
                                                <option data-id="{{$category->id}}" value="{{$category->slug}}">{{$category->translateOrFirst(app()->getLocale())->title}}</option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="min-price">
                                        <label for="">{{__('frontend::main.filter_section.dropdown.item_18')}}</label>
                                        <input style="font-size:13px" type="number" class="form-control input-custom input-full" name="min_price" placeholder="{{__('frontend::main.filter_section.dropdown.item_22')}}" aria-required="true">
                                    </div>
                                    <div class="max-price">
                                        <label for="">{{__('frontend::main.filter_section.dropdown.item_19')}}</label>
                                        <input style="font-size:13px" type="number" class="form-control input-custom input-full" name="max_price" placeholder="{{__('frontend::main.filter_section.dropdown.item_23')}}" aria-required="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="min-area">
                                        <label for="">{{__('frontend::main.filter_section.dropdown.item_20')}}</label>
                                        <input style="font-size:13px" type="number" class="form-control input-custom input-full" name="min_area" placeholder="{{__('frontend::main.filter_section.dropdown.item_24')}}" aria-required="true">
                                    </div>
                                    <div class="max-area">
                                        <label for="">{{__('frontend::main.filter_section.dropdown.item_21')}}</label>
                                        <input style="font-size:13px" type="number" class="form-control input-custom input-full" name="max_area" placeholder="{{__('frontend::main.filter_section.dropdown.item_25')}}" aria-required="true">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{__('frontend::main.filter_section.search')}}
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="main-slider col-xl-8 col-lg-8 col-md-7 align-self-center {{app()->getLocale() == 'ar' ? 'pl-0' : 'pr-0'}}">
                        <div class="main_imgblock">
                            <div class="swiper-container">
                              <!-- <div class="swiper-wrapper">
                                    @php
                                        $default = route('image', ['size' => '1200x848', 'path' => 'defaults/attachments.png']);
                                    @endphp
                                    @foreach($sliderImages as $sliderProject)
                                        @php
                                            if($sliderProject->attachments->where('input_name', 'slider_images')->isNotEmpty() && !is_null($featuredImage = $sliderProject->attachments->where('input_name', 'slider_images')->random()))
                                            {
                                                $featuredImageUrl = $featuredImage->getUid('1200x848');
                                            }
                                            else
                                            {
                                                $featuredImageUrl = $sliderProject->getTranslatedImage('1200x848');
                                            }
                                            $category               = $sliderProject->allCategories->where('type','property_classifications')->where('pivot.options', 'top_type')->first();
                                            if(is_null($category))
                                            {
                                                $category           = $sliderProject->allCategories->where('type','property_classifications')->first();
                                            }
                                            $projectLink            = route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'properties'), 'slug' => $sliderProject->slug]);
                                        @endphp
                                        <div class="swiper-slide">
                                            <div class="swiper_contbox click-image">
                                                <div class="swipper_conntent">
                                                    <a class="image-href-val" href="{{ $projectLink }}">
                                                        <img src="{{ $default }}" data-src="{{ $featuredImageUrl }}" class="img-fluid lazy slider-image" alt="{{ $sliderProject->translateOrFirst()->title }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div> -->

                                <div class="swiper-wrapper">
                                    @php
                                        $default = route('image', ['size' => '1200x848', 'path' => 'defaults/attachments.png']);
                                    @endphp
                                    @if(count($sliderImages) > 0)
                                    @foreach($sliderImages as $sliderProject)
                                    @if($sliderProject->attachments->where('input_name', 'slider_images')->isNotEmpty() && !is_null($featuredImage = $sliderProject->attachments->where('input_name', 'slider_images')->first()))
                                        @php
                                            $featuredImageUrl = $featuredImage->getUid('1200x848');
                                            $category               = $sliderProject->allCategories->where('type','property_classifications')->where('pivot.options', 'top_type')->first();
                                            if(is_null($category))
                                            {
                                                $category           = $sliderProject->allCategories->where('type','property_classifications')->first();
                                            }
                                            $projectLink            = route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'properties'), 'slug' => $sliderProject->slug]);
                                        @endphp
                                        <div class="swiper-slide">
                                            <div class="swiper_contbox click-image">
                                                <div class="swipper_conntent">
                                                    <a class="image-href-val" href="{{ $projectLink }}">
                                                        <img src="{{ $default }}" data-src="{{ $featuredImageUrl }}" class="img-fluid lazy slider-image" alt="{{ $sliderProject->translateOrFirst()->title }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                    @else
                                         @foreach($featuredProjects[app()->getLocale()] as $featuredProject)
                                                                                 @php
                                            if($featuredProject->attachments->where('input_name', 'featured_images')->isNotEmpty() && !is_null($featuredImage = $featuredProject->attachments->where('input_name', 'featured_images')->random()))
                                            {
                                                $featuredImageUrl   = $featuredImage->getUid('1200x848');
                                            }
                                            else
                                            {
                                                $featuredImageUrl   = $featuredProject->getTranslatedImage('1200x848');
                                            }
                                            $category               = $featuredProject->allCategories->where('type','property_classifications')->where('pivot.options', 'top_type')->first();
                                            if(is_null($category))
                                            {
                                                $category           = $featuredProject->allCategories->where('type','property_classifications')->first();
                                            }
                                            $projectLink            = route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'properties'),            'slug' => $featuredProject->slug]);
                                        @endphp
                                        <div class="swiper-slide">
                                            <div class="swiper_contbox click-image">
                                                <div class="swipper_conntent">
                                                    <a class="image-href-val" href="{{ $projectLink }}">
                                                        <img src="{{ $default }}" data-src="{{ $featuredImageUrl }}" class="img-fluid lazy slider-image" 
                                                        alt="{{ $featuredProject->translateOrFirst()->title }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="banner_navi">
                    @if(app()->getLocale() == 'ar')
                        <div class="swiper-button-prev"><i class="fa fa-long-arrow-right"></i></div>
                        <div class="swiper-button-next"><i class="fa fa-long-arrow-left"></i></div>
                    @else
                        <div class="swiper-button-next"><i class="fa fa-long-arrow-right"></i></div>
                        <div class="swiper-button-prev"><i class="fa fa-long-arrow-left"></i></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
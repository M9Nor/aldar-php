@foreach($projects as $key => $project)
    @php
        $even = false;
        if($key % 2 == 0){
            $even = true;
        }
    @endphp
    <section class="client-section ptb-100" style="{{ $even ? 'background:#fff' : 'background:#efefef'}}">
        <div class="container">
            <div class="row {{-- justify-content-center --}} ">
                @if(!is_null($project->landing_page_title))
                    <div class="col-md-6">
                        <div class="section-heading text-center mb-5">
                            <h2 style="color: {{$main_color}}">{{$project->landing_page_title}}</h2>
                            <p class="lead">
                                {!!$project->landing_page_desc!!}
                            </p>
                        </div>
                    </div>
                @endif
                <div class="col-md-6">
                    <h2 style="text-align: center;color:{{$main_color}};margin-bottom:7px">{!!trans('frontend::landing_page.project_images')!!}</h2>
                    <div class="owl-carousel owl-theme clients-carousel dot-indicator" style="direction:ltr">
                        @if($project->attachments->whereIn('input_name', ['image_internal'])->isNotEmpty())
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($project->attachments->whereIn('input_name', ['image_internal']) as  $internal)
                                @php
                                    $i++;
                                @endphp
                                @if($i < 3)
                                    <div class="item single-client">
                                        <img src="{{$internal->getUid('1920x1280')}}" alt="{{$internal->title . (!is_null($internal->description) ? ', ' . $internal->description : '') }}" class="client-img">
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        @if($project->attachments->whereIn('input_name', ['image_external'])->isNotEmpty())
                            @php
                                $e = 0;
                            @endphp
                            @foreach ($project->attachments->whereIn('input_name', ['image_external']) as  $external)
                                @php
                                    $e++;
                                @endphp
                                @if($e < 3)
                                    <div class="item single-client">
                                        <img src="{{$external->getUid('1920x1280')}}" alt="{{$external->title . (!is_null($external->description) ? ', ' . $external->description : '') }}" class="client-img">
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        @if($project->attachments->whereIn('input_name', ['ser_and_fac'])->isNotEmpty())
                            @php
                                $s = 0;
                            @endphp
                            @foreach ($project->attachments->whereIn('input_name', ['ser_and_fac']) as  $ser_and_fac)
                                @php
                                    $s++;
                                @endphp
                                @if($s < 3)
                                    <div class="item single-client">
                                        <img src="{{$ser_and_fac->getUid('1920x1280')}}" alt="{{$ser_and_fac->title . (!is_null($ser_and_fac->description) ? ', ' . $ser_and_fac->description : '') }}" class="client-img">
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                @if(!is_null($project->video))
                    <div class="col-md-6">
                        <h2 style="text-align: center;color:{{$main_color}}">{!!trans('frontend::landing_page.video')!!}</h2>
                        <section id="" class="video-promo ptb-100 " 
                        style="background: url('{{!empty($project->translateOrFirst()->project_image1) ? $project->getTranslatedVideoImage1('698x500') : $project->getTranslatedImage('698x500')}}')no-repeat center center / cover;">
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="video-promo-content mt-4 text-center">
                                            <a href="{{$project->video}}" class="popup-youtube video-play-icon d-inline-block"><span class="ti-control-play"></span> </a>
                                            <h5 class="mt-4 text-white">{{__('frontend::landing_page.property_video')}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                @endif
                <div class="{{!is_null($project->landing_page_title) ? 'col-md-6' : 'col-md-12'}}">
                    <h2 style="text-align: center;color:{{$main_color}}">{!!trans('frontend::landing_page.features_and_service')!!}</h2>
                    <div class="row">
                        @foreach ($project->propertyFeatures as $propertyFeature)
                            @if ($propertyFeature->pivot->options == 'top_feature')
                                <div class="col-sm-4 col-4 icon_box_area justify-content-around my-2">
                                    <div class="row" style="text-align: center">
                                        <div class="col-12 score">
                                            <a href="javascript:;">
                                                <img width="85" src="{{$propertyFeature->getTranslatedImage('150x150')}}">
                                            </a>
                                        </div>
                                        <div class="col-12 details">
                                            <a style="color: #000" href="javascript:;">{{$propertyFeature->translateOrFirst()->title}}</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @php
                            $prop = 0;
                        @endphp
                        @foreach($project->allCategories->where('type', 'facilities') as $key => $propertyFacility)
                            @php
                                $prop++;
                            @endphp
                            @if($prop < 4)
                                <div class="col-sm-4 col-4 icon_box_area justify-content-around my-2 propertyFacility">
                                    <div class="row" style="text-align: center">
                                        <div class="col-12 score">
                                            <a href="javascript:;">
                                                <img width="85" src="{{$propertyFacility->getTranslatedImage('150x150')}}">
                                            </a>
                                        </div>
                                        <div class="col-12 details">
                                            <a style="color: #000" href="javascript:;">{{$propertyFacility->translateOrFirst()->title}}</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @if(    
                    !is_null($project->airport) || !is_null($project->sea) || !is_null($project->city_distance) || !is_null($project->school) 
                    || !is_null($project->university) || !is_null($project->hospital) || !is_null($project->mall) || !is_null($project->mosque)
                )
                    <div class="col-md-12" 
                            style="{{$even ? 'background: white;padding: 20px;margin-top: 20px;box-shadow: 0 0 10px 1px rgb(71 85 95 / 29%);' 
                            : 'background: #efefef;padding: 20px;margin-top: 20px;box-shadow: 0 0 10px 1px rgb(0 0 0 / 24%);'}}"
                        >
                        <h2 style="text-align: center;color:{{$main_color}}">{{ __('frontend::properties.distance_to.title') }}</h2>
                        <div class="row" style="text-align: center">
                            @if(!is_null($project->airport))
                                <dl class="col-lg-3 col-md-4 col-4 dl-horizontal mb-0">
                                    <dt style="font-weight: bold">{{ __('frontend::properties.distance_to.airport') }}</dt>
                                    <dd>{{ __('frontend::properties.distance', [ 'distance' => $project->airport]) }}</dd>
                                </dl>
                            @endif
                            @if(!is_null($project->sea))
                                <dl class="col-lg-3 col-md-4 col-4 dl-horizontal mb-0">
                                    <dt style="font-weight: bold">{{ __('frontend::properties.distance_to.beach') }}</dt>
                                    <dd>{{ __('frontend::properties.distance', [ 'distance' => $project->sea]) }}</dd>
                                </dl>
                            @endif
                            @if(!is_null($project->city_distance))
                                <dl class="col-lg-3 col-md-4 col-4 dl-horizontal mb-0">
                                    <dt style="font-weight: bold">{{ __('frontend::properties.distance_to.city_center') }}</dt>
                                    <dd>{{ __('frontend::properties.distance', [ 'distance' => $project->city_distance]) }}</dd>
                                </dl>
                            @endif
                            @if(!is_null($project->school))
                                <dl class="col-lg-3 col-md-4 col-4 dl-horizontal mb-0">
                                    <dt style="font-weight: bold">{{ __('frontend::properties.distance_to.closest_school') }}</dt>
                                    <dd>{{ __('frontend::properties.distance', [ 'distance' => $project->school]) }}</dd>
                                </dl>
                            @endif
                            @if(!is_null($project->university))
                                <dl class="col-lg-3 col-md-4 col-4 dl-horizontal mb-0">
                                    <dt style="font-weight: bold">{{ __('frontend::properties.distance_to.closest_university') }}</dt>
                                    <dd>{{ __('frontend::properties.distance', [ 'distance' => $project->university]) }}</dd>
                                </dl>
                            @endif
                            @if(!is_null($project->hospital))
                                <dl class="col-lg-3 col-md-4 col-4 dl-horizontal mb-0">
                                    <dt style="font-weight: bold">{{ __('frontend::properties.distance_to.closest_hospital') }}</dt>
                                    <dd>{{ __('frontend::properties.distance', [ 'distance' => $project->hospital]) }}</dd>
                                </dl>
                            @endif
                            @if(!is_null($project->mall))
                                <dl class="col-lg-3 col-md-4 col-4 dl-horizontal mb-0">
                                    <dt style="font-weight: bold">{{ __('frontend::properties.distance_to.closest_mall') }}</dt>
                                    <dd>{{ __('frontend::properties.distance', [ 'distance' => $project->mall]) }}</dd>
                                </dl>
                            @endif
                            @if(!is_null($project->mosque))
                                <dl class="col-lg-3 col-md-4 col-4 dl-horizontal mb-0">
                                    <dt style="font-weight: bold">{{ __('frontend::properties.distance_to.closest_mosque') }}</dt>
                                    <dd>{{ __('frontend::properties.distance', [ 'distance' => $project->mosque]) }}</dd>
                                </dl>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endforeach
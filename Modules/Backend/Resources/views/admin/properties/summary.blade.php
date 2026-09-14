<div class="kt-widget kt-widget--user-profile-3">
    <div class="kt-widget__top">
        
        <div class="kt-widget__pic kt-widget__pic--danger kt-font-danger kt-font-boldest kt-font-light kt-hidden">
            JM
        </div>
        <div class="kt-widget__content">
            <div class="kt-widget__head">
                
                
            </div>
            <div class="kt-widget__subhead">
                {{-- <h5 class="text-danger">
                    {{__('frontend::properties.advertisers_name')}}: 
                    {{$model->advertisers_name}}
                </h5>  --}}
                <div class="row">
                    <div class="col-md-6">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.advertisers_name')}}: 
                            {{$model->advertisers_name}}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.advertisers_email')}}: 
                            {{$model->advertisers_email}}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.advertisers_phone')}}: 
                            {{$model->advertisers_phone}}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.property_explanation')}}: 
                            <br>
                            {{$model->property_explanation}}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.property_city')}}: 
                            {{$model->property_city}}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.property_area')}}: 
                            {{$model->property_area}}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.offers')}}: 
                            {{$model->offers}}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.property_classifications')}}: 
                            {{$model->property_classifications}}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.property_status')}}: 
                            {{$model->property_status}}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="font-size: 16px">
                            {{__('backend::requests.price')}}: 
                            {{$model->min_price . ' - ' . $model->max_price}}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p style="font-size: 16px">
                            {{__('frontend::properties.property_images')}}: 
                            <br>
                            @if($model->attachments->whereIn('input_name', ['property_images'])->isNotEmpty())
                                @php
                                    $default = route('image', ['size' => '1920x1280', 'path' => 'defaults/attachments.png']);
                                @endphp
                                @foreach ($model->attachments->whereIn('input_name', ['property_images']) as  $attachment)
                                    <a href="javascript:;">
                                        <img style="max-height: 200px;margin-top: 25px" class="owl-lazy" 
                                        src="{{$attachment->getUid("original")}}" data-src="{{$attachment->getUid("original")}}">
                                    </a>
                                @endforeach
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

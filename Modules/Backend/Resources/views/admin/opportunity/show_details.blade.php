@extends('cms::layouts.master')

@section('title', __('backend::opportunity.title'))
@php
    use Modules\Backend\Entities\Project;
@endphp
@push('styles')
    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.rtl.css') }}" rel="stylesheet" type="text/css">
    @else
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.css') }}" rel="stylesheet" type="text/css">
    @endif
    <style>
        .select2-container--default{
            width: 100% !important;
        }
    </style>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('backend::requests.title'),
            'items' => [
                [
                    'label' => __('backend::requests.title'),
                    'link'  => route('OpportunityController@properties')
                ]
            ]
        ]
    ])
    @slot('main')
    @endslot
        @slot('toolbar')
            <a href="{{ url()->previous() }}" class="btn btn-default btn-bold">
                {{ __('cms::global.back') }}
            </a>
            <div class="btn-group">
                
            </div>
        @endslot
    @endcomponent
@endsection
@section('content')
    <div class="kt-portlet kt-portlet--tabs kt-portlet--last kt-portlet--responsive-mobile" id="kt_page_portlet">
        
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-12">
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
                                                {{-- {{__('frontend::properties.offers')}}:  --}}
                                                {{-- {{$model->offers}} --}}
                                                {{__('frontend::properties.offers')}}: 
                                                @foreach($filters->where('type','contracts') as $category)
                                                    @if($category->slug == $model->offers)
                                                        {{$category->translateOrFirst(app()->getLocale())->title}}
                                                    @endif
                                                @endforeach
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p style="font-size: 16px">
                                                {{-- {{__('frontend::properties.opportunity_classifications')}}: 
                                                {{$model->opportunity_classifications}} --}}
                                                {{__('frontend::properties.opportunity_classifications')}}: 
                                                @foreach($filters->where('type','opportunity_classifications')->whereNotNull('parent_id') as $category)
                                                    @if($category->slug == $model->opportunity_classifications)
                                                        {{$category->translateOrFirst(app()->getLocale())->title}}
                                                    @endif
                                                @endforeach
                                            </p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p style="font-size: 16px">
                                                {{__('frontend::properties.property_status')}}: 
                                                {{-- {{$model->property_status}} --}}
                                                @foreach($filters->where('type','property_status') as $category)
                                                    @if($category->slug == $model->property_status)
                                                        {{$category->translateOrFirst(app()->getLocale())->title}}
                                                    @endif
                                                @endforeach
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p style="font-size: 16px">
                                                {{__('backend::requests.price')}}: 
                                                {{-- {{$model->min_price . ' - ' . $model->max_price}} --}}
                                                {{$model->min_price}}
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
                                                        {{-- <img style="max-height: 200px;margin-top: 25px" class="owl-lazy" 
                                                        src="{{$attachment->getUid("original")}}" data-src="{{$attachment->getUid("original")}}"> --}}
                                                        <a href="javascript:;" 
                                                        data-href='{{$attachment->getUid("original")}}' 
                                                        download="{{$attachment->filename}}" 
                                                        onclick='forceDownload(this)'>
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
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

<script>
    function forceDownload(link){
        var url = link.getAttribute("data-href");
        var fileName = link.getAttribute("download");
        // link.innerText = "Working...";
        var xhr = new XMLHttpRequest();
        xhr.open("GET", url, true);
        xhr.responseType = "blob";
        xhr.onload = function(){
            var urlCreator = window.URL || window.webkitURL;
            var imageUrl = urlCreator.createObjectURL(this.response);
            var tag = document.createElement('a');
            tag.href = imageUrl;
            tag.download = fileName;
            document.body.appendChild(tag);
            tag.click();
            document.body.removeChild(tag);
            // link.innerText="Download Image";
        }
        xhr.send();
    }
</script>

<script>
    
</script>

@endpush



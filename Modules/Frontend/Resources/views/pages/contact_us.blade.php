@extends('frontend::layouts.master')

    @include('frontend::seo.meta', [
        'options' => [
            'title'     	=> null,
            'sub_title' 	=> $model->translateOrFirst()->title,
            'description' 	=> $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
            'keywords' 	    => $model->translateOrFirst()->keywords,
        ],
    ])
    @include('frontend::seo.google', [
        'options' => [
            'title'     	=> null,
            'sub_title' 	=> $model->translateOrFirst()->title,
            'description' 	=> $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
        ],
    ])
    @include('frontend::seo.facebook', [
        'options' => [
            'title'     	=> null,
            'sub_title' 	=> $model->translateOrFirst()->title,
            'description' 	=> $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
            'keywords' 	    => $model->translateOrFirst()->keywords,
        ],
    ])
    @include('frontend::seo.twitter', [
        'options' => [
            'title'     	=> null,
            'sub_title' 	=> $model->translateOrFirst()->title,
            'description' 	=> $model->seoDescription(),
            'image'         => $model->getTranslatedImage('1000x750'),
        ],
    ])
@push('styles')

    <style>
        .project-description table{
            float: unset !important;
        }
        .gj-picker.gj-picker-bootstrap.timepicker{
            direction: ltr !important;
        }
    </style>
@endpush

@section('content')

    <div class="inner-pages">
        <section class="headings">
            <div class="text-heading text-center">
                <div class="container">
                    <h1>{{$model->translateOrFirst()->title}}</h1>
                    <h2><a href="{{ route('index') }}">{{trans('frontend::main.home')}} </a> &nbsp;/&nbsp; {{$model->translateOrFirst()->title}}</h2>
                </div>
            </div>
        </section>
    </div>
    
    <section class="contact-section-page mt-4 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-xs-12">
                    <div class="side">
                        <div class="title">
                            <h2>{{__("frontend::main.contact")}}</h2>
                        </div>
                        <div class="we-desc">
                            {!!__("frontend::main.contact_desc")!!}
                        </div>
                        <div class="first-footer">
                            <div class="contactus">
                                <ul>
                                    <li>
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
                                    </li>
                                    <li>
                                        @if(isset($contents['mobile_number_1']))
                                            @php
                                                $mobile_number_1 = '';
                                                if(isset($contents['mobile_number_1']) && !is_null($contents['mobile_number_1']))
                                                {
                                                    if(!empty($contents['mobile_number_1']->translateOrFirst()->description))
                                                    {
                                                        $mobile_number_1 = $contents['mobile_number_1']->translateOrFirst()->description;
                                                    }
                                                    else
                                                    {
                                                        $mobile_number_1 = $contents['mobile_number_1']->value;
                                                    }
                                                }
                                            @endphp
                                            <div class="info">
                                                <i class="fa fa-phone" aria-hidden="true"></i>
                                                <p class="in-p">{{$mobile_number_1}}</p>
                                            </div>
                                        @endif
                                    </li>
                                    <li>
                                        @if(isset($contents['email']))
                                            @php
                                                $email = '';
                                                if(isset($contents['email']) && !is_null($contents['email']))
                                                {
                                                    if(!empty($contents['email']->translateOrFirst()->description))
                                                    {
                                                        $email = $contents['email']->translateOrFirst()->description;
                                                    }
                                                    else
                                                    {
                                                        $email = $contents['email']->value;
                                                    }
                                                }
                                            @endphp
                                            <div class="info">
                                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                                <p class="in-p ti">{{$email}}</p>
                                            </div>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-xs-12">
                    <div class="body">
                        <div class="contact-form">
                            <form method="POST" onsubmit="submitFroms(event);" action="{{route('ContactController@storeInner')}}" name="contact_form" class="inner-contact">
                                @csrf
                                <div class="form-group">
                                    <input class="form-control" onchange="isValid('fullname')" type="text" name="fullname" placeholder="{{__('frontend::main.contact_form.name')}} *">
                                    <p class="validate-form-message" id="fullname"></p>
                                </div>
                                <div class="form-group">
                                    <input class="form-control number-flag" onchange="isValid('phone')" type="number" name="phone" placeholder="{{__('frontend::main.contact_form.phone')}} *" autocomplete="off">
                                    <span>
                                        <select class="vodiapicker" style="display: none">
                                            @foreach($all_countries as $key=>$country)
                                                <option class="{{$key == 0 ? 'test' : ''}}" data-thumbnail="{{$country->flag}}" value="{{$country->code}}">{{$country->code}}</option>
                                            @endforeach
                                        </select>
                                        <div class="lang-select lang-select2 lang-select3">
                                            <input class="op-val" type="hidden" name="country_code" value="+90">
                                            <button class="btn-select" value="" type="button" style="height: 100%"></button>
                                            <div class="b-item">
                                                <ul id="a-item" style="background:#fff"></ul>
                                            </div>
                                        </div>
                                    </span>
                                    <p class="validate-form-message" id="phone"></p>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" onchange="isValid('email')" type="text" name="email" placeholder="{{__('frontend::main.contact_form.email')}} *">
                                    <p class="validate-form-message" id="email"></p>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" onchange="isValid('language')" name="language" aria-placeholder="{{__('frontend::main.select_lang')}} *">
                                        <option value="">{{__('frontend::main.select_lang')}}</option>
                                        <option value="ar">{{__('frontend::main.arabic')}}</option>
                                        <option value="en">{{__('frontend::main.english')}}</option>
                                        <option value="tr">{{__('frontend::main.turkish')}}</option>
                                    </select>
                                    <p class="validate-form-message" id="language"></p>
                                </div>
                                <div class="form-group">
                                    {{-- <label >{{__('frontend::main.time_from')}} *</label> --}}
                                    <input id="timepicker" onchange="isValid('time_from')" name="time_from" placeholder="{{__('frontend::main.time_from')}} *">
                                    {{-- <input dir="rtl" style="direction: rtl" class="form-control" onchange="isValid('time_from')" type="time" name="time_from" placeholder="{{__('frontend::main.time_from')}} *"> --}}
                                    <p class="validate-form-message" id="time_from"></p>
                                </div>
                                <div class="form-group">
                                    {{-- <label >{{__('frontend::main.time_to')}} *</label> --}}
                                    <input id="timepicker2" onchange="isValid('time_to')" name="time_to" placeholder="{{__('frontend::main.time_to')}} *">
                                    {{-- <input dir="rtl" style="direction: rtl" class="form-control" onchange="isValid('time_to')" type="time" name="time_to" placeholder="{{__('frontend::main.time_to')}} *"> --}}
                                    <p class="validate-form-message" id="time_to"></p>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control asdasd" onchange="isValid('description')" 
                                    name="description" placeholder="{{__('frontend::main.contact_form.description')}} *"></textarea>
                                    <p class="validate-form-message" id="description"></p>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg save-btn">
                                        <div id="spinner_div" class="">
                                            <div class="update-text" >
                                                {{__('frontend::main.contact_form.send')}}
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="container">
        <div class="article-body mt-4">
            <div class="row ">
                <div class="col-md-12">
                    <h2 class="services-title">{{__("frontend::main.services")}}</h2>
                </div>
                @php
                    $defaultLangImage = route('image', ['size' => '110x110', 'path' => 'defaults/base.png']);
                @endphp
                @foreach ($services as $service)
                    <div class="col-md-6 col-12">
                        @include('frontend::includes.service_single', ['service' => $service,'defaultLangImage' => $defaultLangImage])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div >
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
            <iframe src="{{$map}}" style="border:0;width:100%;height:400px" allowfullscreen="" loading="lazy"></iframe>
        @endif
    </div>

@endsection


@push('scripts')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <script>
        $('#timepicker').timepicker({
            uiLibrary: 'bootstrap4'
        });
        $('#timepicker2').timepicker({
            uiLibrary: 'bootstrap4'
        });
    </script>

    @if(app()->getLocale() == 'ar')
        <script>
            $( document ).ready(function() {
                setTimeout(function(){ 
                    $('.gj-modal .gj-picker-bootstrap .modal-footer button:first-child').text('إلغاء');
                    $('.gj-modal .gj-picker-bootstrap .modal-footer button:last-child').text('حسناً');

                    $('.gj-modal .gj-picker-bootstrap div:first-child div:last-child span:first-child').text('صباحاً');
                    $('.gj-modal .gj-picker-bootstrap div:first-child div:last-child span:last-child').text('مساءً');
                }, 1000);
            });
        </script>
    @endif
@endpush
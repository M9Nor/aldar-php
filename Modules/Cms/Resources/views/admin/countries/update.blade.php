@php
    use Modules\Cms\Entities\Country;
@endphp

@extends('cms::layouts.master')

@section('title',  __('cms::areas.countries'))

@push('styles')
    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.rtl.css') }}" rel="stylesheet" type="text/css">
    @else
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.css') }}" rel="stylesheet" type="text/css">
    @endif
    <style>
        .repeater-table-head,.repeater-table-body,.repeater-table-body-edit{
            width: 100%;
        }
        .repeater-table-head thead{
            font-size: 16px;
        }
        .repeater-table-head tbody tr:nth-child(even) {
            background-color: #eee;
        }
        .repeater-table-head thead tr th,.repeater-table-body tbody tr td,.repeater-table-body-edit tbody tr td {
            border: 1px solid #dddddd;
            padding: 8px;
        }
    </style>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' => __('cms::areas.edit_country'),
            'items' => [
                [
                    'label' => __('cms::areas.countries'),
                    'link'  => route('CountryController@index')
                ], [
                    'label' =>__('cms::areas.edit_country'),
                    'link'  => 'javascript:;'
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
                <a href="javascript:;" data-redirect-url="javascript:;" class="submit_form btn btn-success btn-bold">
                    {{ __('cms::global.submit') }}
                </a>
                <button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu dropdown-menu-{{ $langDirection == 'rtl' ? 'left' : 'right' }}">
                    <ul class="kt-nav">
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="javascript:;" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-writing"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_continue') }}</span> <!-- Save &amp; continue -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('CountryController@create')}}"class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('CountryController@index')}}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-indent-dots"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_exit') }}</span> <!-- Save &amp; exit -->
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        @endslot
    @endcomponent
@endsection

@section('content')
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('CountryController@update',['model' => $model->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        {{-- <input type="hidden" name="type" value="{{$type}}"> --}}
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.edit_country')}}
                        </h4>
                        <div class="kt-section__content">
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-danger" role="tablist">
                                @foreach($supportedLangs as $locale => $properties)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" hreflang="{{ $locale }}" href="#tab_{{ $locale }}" role="tab">
                                            <img width="25" height="18" src="{{ Module::asset('cms:flags/' . $locale . '.svg') }}" alt="" /> {{ $properties['native'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($supportedLangs as $locale => $properties)
                                    <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_{{ $locale }}" role="tabpanel">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                @include('cms::components.inputs.text', [
                                                    'options' => [
                                                        'id'            => 'name_'.$locale,
                                                        'name'          => 'name_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.fields.native_name.label'),
                                                        'placeholder'   => __('cms::areas.fields.native_name.placeholder'),
                                                        'help'          => __('cms::areas.fields.native_name.help'),
                                                        'value'         => old('name_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->name),
                                                        'required'      => true,
                                                        
                                                    ]
                                                ])
                                            </div>
                                            <div class="col-sm-12">
                                                @php
                                                    if($loop->first){
                                                        $baseScript = true;
                                                    }else{
                                                        $baseScript = false;
                                                    }
                                                @endphp
                                                @include('cms::components.inputs.tinymce', [
                                                    'options' => [
                                                        'id'            => 'description_'.$locale,
                                                        'name'          => 'description_'.$locale,
                                                        'label'         => __('cms::areas.fields.description.label'),
                                                        'placeholder'   => __('cms::areas.fields.description.placeholder'),
                                                        'help'          => __('cms::areas.fields.description.help'),
                                                        'rows'          => 6,
                                                        'value'         => old('description_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->description),
                                                        'required'      => false,
                                                        'base_script'   => $baseScript,
                                                    ]
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        // Submits the form whenever a button with the class .submit_form is clicked.
        $('.submit_form').click(function() {
            $('#redirectUrl').val($(this).data('redirectUrl'));
            $('#addNewForm').submit();
        });
        $('.nav-link').click(function() {
            KTUtil.scrollTop();
        });
    </script>
@endpush



@php
    use Modules\Cms\Entities\CityTranslation;
@endphp

@extends('cms::layouts.master')

@section('title', __('cms::areas.cities'))

@push('styles')
    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.rtl.css') }}" rel="stylesheet" type="text/css">
    @else
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.css') }}" rel="stylesheet" type="text/css">
    @endif
    <style>
        .repeater-table-head,.repeater-table-body{
            width: 100%;
        }
        .repeater-table-head thead{
            font-size: 16px;
        }
        .repeater-table-head tbody tr:nth-child(even) {
            background-color: #eee;
        }
        .repeater-table-head thead tr th,.repeater-table-body tbody tr td {
            border: 1px solid #dddddd;
            padding: 8px;
        }
    </style>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('cms::areas.create_new_city'),
            'items' => [
                [
                    'label' => __('cms::areas.cities'),
                    'link'  => route('CityController@index')
                ], [
                    'label' => __('cms::areas.create_new_city'),
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
                <a href="javascript:;" data-redirect-url="{{ route('CityController@index') }}" class="submit_form btn btn-success btn-bold">
                    {{ __('cms::global.submit') }}
                </a>
                <button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu dropdown-menu-{{ $langDirection == 'rtl' ? 'left' : 'right' }}">
                    <ul class="kt-nav">
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('CityController@create') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('CityController@index') }}" class="submit_form kt-nav__link">
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
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('CityController@store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        {{-- <input type="hidden" name="type" value="{{$type}}"> --}}
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.create_new_city')}}
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
                                            <div class="col-md-12">
                                                @include('cms::components.inputs.image', [
                                                    'options' => [
                                                        'id'            => 'image_'.$locale,
                                                        'name'          => 'image_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.fields.image.label'),
                                                        'placeholder'   => __('cms::areas.fields.image.placeholder'),
                                                        'help'          => __('cms::areas.fields.image.help', [
                                                            'prefered_dimensions' => '1000×750',
                                                            'mimes'               => 'png | jpeg'
                                                        ]),
                                                        'default'       => old('image_'.$locale, route('image', [
                                                            'size' => '500x375',
                                                            'path' => 'defaults/base.png'
                                                        ])),
                                                        'required'      => false,
                                                        'browse'        => __('cms::cruds.contents.image.add_text'),
                                                        'remove'        => __('cms::cruds.contents.image.remove_text'),
                                                        'width'         => '500px',
                                                        'height'        => '375px',
                                                    ]
                                                ])
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                @include('cms::components.inputs.text', [
                                                    'options' => [
                                                        'id'            => 'name_'.$locale,
                                                        'name'          => 'name_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.fields.name.label'),
                                                        'placeholder'   => __('cms::areas.fields.name.placeholder'),
                                                        'help'          => __('cms::areas.fields.name.help'),
                                                        'value'         => old('name'),
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
                                                        'value'         => old('description'),
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
                        <div class="row">
                            <div class="col-sm-12">
                                @include('cms::components.inputs.select2', [
                                    'options' => [
                                        'id'                => 'country',
                                        'name'              => 'country',
                                        'label'             => __('backend::projects.fields.country.label'),
                                        'placeholder'       => __('backend::projects.fields.country.placeholder'),
                                        'help'              => __('backend::projects.fields.country.help'),
                                        'selected'          => [],
                                        'required'          => true,
                                        'multiple'          => false,
                                        'clear_button'      => false,
                                        'url'               => route('CmsController@getCountries'),
                                        'items_per_page'    => 20,
                                        'dir'               => 'rtl',
                                    ]
                                ])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'sort_order',
                                        'name'          => 'sort_order',
                                        'type'          => 'text',
                                        'label'         => __('cms::areas.fields.sort_order.label'),
                                        'placeholder'   => __('cms::areas.fields.sort_order.placeholder'),
                                        'help'          => __('cms::areas.fields.sort_order.help'),
                                        'value'         => old('sort_order'),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'link',
                                        'name'          => 'link',
                                        'type'          => 'text',
                                        'label'         => __('cms::areas.fields.link.label'),
                                        'placeholder'   => __('cms::areas.fields.link.placeholder'),
                                        'help'          => __('cms::areas.fields.link.help'),
                                        'value'         => old('link'),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'                => 'native_name',
                                        'name'              => 'native_name',
                                        'type'              => 'text',
                                        'label'             => __('cms::areas.fields.slug.label'),
                                        'placeholder'       => __('cms::areas.fields.slug.placeholder'),
                                        'help'              => __('cms::areas.fields.slug.help'),
                                        'value'             => old('native_name'),
                                        'required'          => true,
                                        'inline'            => false,
                                        'maxlength'         => 191,
                                        'status_removal'    => false
                                    ]
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="kt-section">
                        <h4 class="kt-section__title kt-section__title-sm kt-font-boldest text-primary">
                            {{__('cms::areas.custom_fields.price_change.title')}}
                        </h4>
                        <h5 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.custom_fields.price_change.rent.title')}}
                        </h5>
                        <div class="row">
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'price_change[rent][last_year]',
                                        'name'          => 'price_change[rent][last_year]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.price_change.rent.last_year.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.price_change.rent.last_year.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.price_change.rent.last_year.help'),
                                        'value'         => old('price_change.rent.last_year'),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'price_change[rent][last_3_years]',
                                        'name'          => 'price_change[rent][last_3_years]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.price_change.rent.last_3_years.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.price_change.rent.last_3_years.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.price_change.rent.last_3_years.help'),
                                        'value'         => old('price_change.rent.last_3_years'),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'price_change[rent][last_5_years]',
                                        'name'          => 'price_change[rent][last_5_years]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.price_change.rent.last_5_years.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.price_change.rent.last_5_years.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.price_change.rent.last_5_years.help'),
                                        'value'         => old('price_change.rent.last_5_years'),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                        </div>
                        <h5 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.custom_fields.price_change.sale.title')}}
                        </h5>
                        <div class="row">
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'price_change[sale][last_year]',
                                        'name'          => 'price_change[sale][last_year]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.price_change.sale.last_year.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.price_change.sale.last_year.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.price_change.sale.last_year.help'),
                                        'value'         => old('price_change.sale.last_year'),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'price_change[sale][last_3_years]',
                                        'name'          => 'price_change[sale][last_3_years]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.price_change.sale.last_3_years.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.price_change.sale.last_3_years.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.price_change.sale.last_3_years.help'),
                                        'value'         => old('price_change.sale.last_3_years'),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'price_change[sale][last_5_years]',
                                        'name'          => 'price_change[sale][last_5_years]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.price_change.sale.last_5_years.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.price_change.sale.last_5_years.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.price_change.sale.last_5_years.help'),
                                        'value'         => old('price_change.sale.last_5_years'),
                                        'required'      => false,
                                    ]
                                ])
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




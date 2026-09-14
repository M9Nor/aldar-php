@php
    use Modules\Cms\Entities\AreaTranslation;
@endphp

@extends('cms::layouts.master')

@section('title',  __('cms::areas.title'))

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
        #tags_en + span , #tags_tr + span{
            width: 100% !important
        }
    </style>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' => __('cms::areas.edit_area'),
            'items' => [
                [
                    'label' => __('cms::areas.title'),
                    'link'  => route('AreaController@index')
                ], [
                    'label' =>__('cms::areas.edit_area'),
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
                            <a href="javascript:;" data-redirect-url="{{ route('AreaController@create')}}"class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('AreaController@index')}}" class="submit_form kt-nav__link">
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
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('AreaController@update',['model' => $model->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        {{-- <input type="hidden" name="type" value="{{$type}}"> --}}
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.edit_area')}}
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
                                                            'prefered_dimensions' => '150×150 | 400×400',
                                                            'mimes'               => 'png | jpeg'
                                                        ]),
                                                        'default'       => old('image_'.$locale,$model->translate($locale) == NULL ? (new Modules\Cms\Entities\AreaTranslation)->getImage('400x400') : $model->translate($locale)->getImage('400x400') ),
                                                        'required'      => false,
                                                        'browse'        => __('cms::cruds.contents.image.add_text'),
                                                        'remove'        => __('cms::cruds.contents.image.remove_text'),
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
                                                        'value'         => old('name_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->name),
                                                        'required'      => true,
                                                    ]
                                                ])
                                            </div>
                                            <div class="col-sm-12">
                                                @include('cms::components.inputs.textarea', [
                                                    'options' => [
                                                        'id'            => 'about_'.$locale,
                                                        'name'          => 'about_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.fields.about.label'),
                                                        'placeholder'   => __('cms::areas.fields.about.placeholder'),
                                                        'help'          => __('cms::areas.fields.about.help'),
                                                        'value'         => old('about_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->about),
                                                        'required'      => false,
                                                        'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                    ]
                                                ])
                                            </div>
                                            <div class="col-sm-12">
                                                @include('cms::components.inputs.textarea', [
                                                    'options' => [
                                                        'id'            => 'short_description_'.$locale,
                                                        'name'          => 'short_description_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.fields.short_description.label'),
                                                        'placeholder'   => __('cms::areas.fields.short_description.placeholder'),
                                                        'help'          => __('cms::areas.fields.short_description.help'),
                                                        'value'         => old('short_description_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->short_description),
                                                        'required'      => false,
                                                        'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                    ]
                                                ])
                                            </div>
                                            <div class="col-sm-12">
                                                @include('cms::components.inputs.textarea', [
                                                    'options' => [
                                                        'id'            => 'details_'.$locale,
                                                        'name'          => 'details_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.fields.details.label'),
                                                        'placeholder'   => __('cms::areas.fields.details.placeholder'),
                                                        'help'          => __('cms::areas.fields.details.help'),
                                                        'value'         => old('details_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->details),
                                                        'required'      => false,
                                                        'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                    ]
                                                ])
                                            </div>
                                            <div class="col-sm-12">
                                                @include('cms::components.inputs.textarea', [
                                                    'options' => [
                                                        'id'            => 'keywords_'.$locale,
                                                        'name'          => 'keywords_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.fields.keywords.label'),
                                                        'placeholder'   => __('cms::areas.fields.keywords.placeholder'),
                                                        'help'          => __('cms::areas.fields.keywords.help'),
                                                        'value'         => old('keywords_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->keywords),
                                                        'required'      => false,
                                                        'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
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
                                            <div class="col-md-12">
                                                @include('cms::components.inputs.taggable', [
                                                    'options' => [
                                                        'id'            => 'tags_'.$locale,
                                                        'name'          => 'tags_'.$locale.'[]',
                                                        'label'         => __('cms::areas.fields.tags.label'),
                                                        'placeholder'   => __('cms::areas.fields.tags.placeholder'),
                                                        'help'          => __('cms::areas.fields.tags.help'),
                                                        'locale'        => $locale,
                                                        'data'          => $model->tags->filter(function($item) use ($locale) {
                                                            return $item->translations->pluck('locale')->contains($locale);
                                                        }),
                                                        'selected'      => $model->tags->filter(function($item) use ($locale) {
                                                            return $item->translations->pluck('locale')->contains($locale);
                                                        })->pluck('id')->toArray(),
                                                        'value'         => function($data, $key, $value){ return $value->id; },
                                                        'text'          => function($data, $key, $value ) use ($locale) { return $value->translate($locale)->text; },
                                                        'select'        => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                                                    ]
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
                                        'value'             => old('native_name',$model->native_name),
                                        'required'          => true,
                                        'inline'            => false,
                                        'maxlength'         => 191,
                                        'status_removal'    => false
                                    ]
                                ])
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                @include('cms::components.inputs.taggable', [
                                    'options' => [
                                        'id'            => 'tags',
                                        'name'          => 'tags[]',
                                        'label'         => __('cms::areas.fields.tags.label'),
                                        'placeholder'   => __('cms::areas.fields.tags.placeholder'),
                                        'help'          => __('cms::areas.fields.tags.help'),
                                        'data'          => $model->tags,
                                        'selected'      => $model->tags->pluck('id')->toArray(),
                                        'value'         => function($data, $key, $value){ return $value->id; },
                                        'text'          => function($data, $key, $value) { return $value->translate()->text; },
                                        'select'        => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                                    ]
                                ])
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-sm-12">
                                @include('cms::components.inputs.select2', [
                                    'options' => [
                                        'id'                => 'city',
                                        'name'              => 'city',
                                        'label'             => __('backend::projects.fields.city.label'),
                                        'placeholder'       => __('backend::projects.fields.city.placeholder'),
                                        'help'              => __('backend::projects.fields.city.help'),
                                        'selected'          => $model->city->formAjaxArray(),
                                        'required'          => true,
                                        'multiple'          => false,
                                        'clear_button'      => false,
                                        'url'               => route('CmsController@getCities'),
                                        // 'additional_params' => [
                                        //     'country_id' => 1,
                                        // ],
                                        'items_per_page'    => 20,
                                        'dir'               => 'rtl',
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
                                        'value'         => old('price_change.rent.last_year', !is_null($customField = $model->customFields->where('key', 'price_change.rent.last_year')->first()) ? $customField->value : ''),
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
                                        'value'         => old('price_change.rent.last_3_years', !is_null($customField = $model->customFields->where('key', 'price_change.rent.last_3_years')->first()) ? $customField->value : ''),
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
                                        'value'         => old('price_change.rent.last_5_years', !is_null($customField = $model->customFields->where('key', 'price_change.rent.last_5_years')->first()) ? $customField->value : ''),
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
                                        'value'         => old('price_change.sale.last_year', !is_null($customField = $model->customFields->where('key', 'price_change.sale.last_year')->first()) ? $customField->value : ''),
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
                                        'value'         => old('price_change.sale.last_3_years', !is_null($customField = $model->customFields->where('key', 'price_change.sale.last_3_years')->first()) ? $customField->value : ''),
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
                                        'value'         => old('price_change.sale.last_5_years', !is_null($customField = $model->customFields->where('key', 'price_change.sale.last_5_years')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="kt-section">
                        <h4 class="kt-section__title kt-section__title-sm kt-font-boldest text-primary">
                            {{__('cms::areas.custom_fields.demographic_data.title')}}
                        </h4>
                        <h5 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.custom_fields.demographic_data.people_data.title')}}
                        </h5>
                        <div class="row">
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[people_data][growth_rate]',
                                        'name'          => 'demographic_data[people_data][growth_rate]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.people_data.growth_rate.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.people_data.growth_rate.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.people_data.growth_rate.help'),
                                        'value'         => old('demographic_data.people_data.growth_rate', !is_null($customField = $model->customFields->where('key', 'demographic_data.people_data.growth_rate')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[people_data][total_population]',
                                        'name'          => 'demographic_data[people_data][total_population]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.people_data.total_population.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.people_data.total_population.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.people_data.total_population.help'),
                                        'value'         => old('demographic_data.people_data.total_population', !is_null($customField = $model->customFields->where('key', 'demographic_data.people_data.total_population')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[people_data][ecomomic_and_social_evaluation]',
                                        'name'          => 'demographic_data[people_data][ecomomic_and_social_evaluation]',
                                        'type'          => 'text',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.people_data.ecomomic_and_social_evaluation.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.people_data.ecomomic_and_social_evaluation.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.people_data.ecomomic_and_social_evaluation.help'),
                                        'value'         => old('demographic_data.people_data.ecomomic_and_social_evaluation', !is_null($customField = $model->customFields->where('key', 'demographic_data.people_data.ecomomic_and_social_evaluation')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                        </div>
                        <h5 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.custom_fields.demographic_data.education_status.title')}}
                        </h5>
                        <div class="row">
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[education_status][uneducated]',
                                        'name'          => 'demographic_data[education_status][uneducated]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.education_status.uneducated.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.education_status.uneducated.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.education_status.uneducated.help'),
                                        'value'         => old('demographic_data.education_status.uneducated', !is_null($customField = $model->customFields->where('key', 'demographic_data.education_status.uneducated')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[education_status][primary]',
                                        'name'          => 'demographic_data[education_status][primary]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.education_status.primary.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.education_status.primary.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.education_status.primary.help'),
                                        'value'         => old('demographic_data.education_status.primary', !is_null($customField = $model->customFields->where('key', 'demographic_data.education_status.primary')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[education_status][elementary]',
                                        'name'          => 'demographic_data[education_status][elementary]',
                                        'type'          => 'text',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.education_status.elementary.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.education_status.elementary.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.education_status.elementary.help'),
                                        'value'         => old('demographic_data.education_status.elementary', !is_null($customField = $model->customFields->where('key', 'demographic_data.education_status.elementary')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[education_status][secondary]',
                                        'name'          => 'demographic_data[education_status][secondary]',
                                        'type'          => 'text',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.education_status.secondary.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.education_status.secondary.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.education_status.secondary.help'),
                                        'value'         => old('demographic_data.education_status.secondary', !is_null($customField = $model->customFields->where('key', 'demographic_data.education_status.secondary')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[education_status][university]',
                                        'name'          => 'demographic_data[education_status][university]',
                                        'type'          => 'text',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.education_status.university.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.education_status.university.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.education_status.university.help'),
                                        'value'         => old('demographic_data.education_status.university', !is_null($customField = $model->customFields->where('key', 'demographic_data.education_status.university')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                        </div>
                        <h5 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.custom_fields.demographic_data.age_distribution.title')}}
                        </h5>
                        <div class="row">
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[age_distribution][0_to_14]',
                                        'name'          => 'demographic_data[age_distribution][0_to_14]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.age_distribution.0_to_14.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.age_distribution.0_to_14.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.age_distribution.0_to_14.help'),
                                        'value'         => old('demographic_data.age_distribution.0_to_14', !is_null($customField = $model->customFields->where('key', 'demographic_data.age_distribution.0_to_14')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[age_distribution][15_to_24]',
                                        'name'          => 'demographic_data[age_distribution][15_to_24]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.age_distribution.15_to_24.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.age_distribution.15_to_24.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.age_distribution.15_to_24.help'),
                                        'value'         => old('demographic_data.age_distribution.15_to_24', !is_null($customField = $model->customFields->where('key', 'demographic_data.age_distribution.15_to_24')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[age_distribution][25_to_34]',
                                        'name'          => 'demographic_data[age_distribution][25_to_34]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.age_distribution.25_to_34.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.age_distribution.25_to_34.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.age_distribution.25_to_34.help'),
                                        'value'         => old('demographic_data.age_distribution.25_to_34', !is_null($customField = $model->customFields->where('key', 'demographic_data.age_distribution.25_to_34')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[age_distribution][35_to_44]',
                                        'name'          => 'demographic_data[age_distribution][35_to_44]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.age_distribution.35_to_44.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.age_distribution.35_to_44.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.age_distribution.35_to_44.help'),
                                        'value'         => old('demographic_data.age_distribution.35_to_44', !is_null($customField = $model->customFields->where('key', 'demographic_data.age_distribution.35_to_44')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[age_distribution][45_to_54]',
                                        'name'          => 'demographic_data[age_distribution][45_to_54]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.age_distribution.45_to_54.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.age_distribution.45_to_54.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.age_distribution.45_to_54.help'),
                                        'value'         => old('demographic_data.age_distribution.45_to_54', !is_null($customField = $model->customFields->where('key', 'demographic_data.age_distribution.45_to_54')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[age_distribution][55_to_64]',
                                        'name'          => 'demographic_data[age_distribution][55_to_64]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.age_distribution.55_to_64.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.age_distribution.55_to_64.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.age_distribution.55_to_64.help'),
                                        'value'         => old('demographic_data.age_distribution.55_to_64', !is_null($customField = $model->customFields->where('key', 'demographic_data.age_distribution.55_to_64')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-4">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[age_distribution][over_65]',
                                        'name'          => 'demographic_data[age_distribution][over_65]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.age_distribution.over_65.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.age_distribution.over_65.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.age_distribution.over_65.help'),
                                        'value'         => old('demographic_data.age_distribution.over_65', !is_null($customField = $model->customFields->where('key', 'demographic_data.age_distribution.over_65')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                        </div>
                        <h5 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.custom_fields.demographic_data.marital_condition.title')}}
                        </h5>
                        <div class="row">
                            <div class="col-sm-6">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[marital_condition][single]',
                                        'name'          => 'demographic_data[marital_condition][single]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.marital_condition.single.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.marital_condition.single.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.marital_condition.single.help'),
                                        'value'         => old('demographic_data.marital_condition.single', !is_null($customField = $model->customFields->where('key', 'demographic_data.marital_condition.single')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-6">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[marital_condition][married]',
                                        'name'          => 'demographic_data[marital_condition][married]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.marital_condition.married.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.marital_condition.married.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.marital_condition.married.help'),
                                        'value'         => old('demographic_data.marital_condition.married', !is_null($customField = $model->customFields->where('key', 'demographic_data.marital_condition.married')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-6">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[marital_condition][divorced]',
                                        'name'          => 'demographic_data[marital_condition][divorced]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.marital_condition.divorced.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.marital_condition.divorced.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.marital_condition.divorced.help'),
                                        'value'         => old('demographic_data.marital_condition.divorced', !is_null($customField = $model->customFields->where('key', 'demographic_data.marital_condition.divorced')->first()) ? $customField->value : ''),
                                        'required'      => false,
                                    ]
                                ])
                            </div>
                            <div class="col-sm-6">
                                @include('cms::components.inputs.text', [
                                    'options' => [
                                        'id'            => 'demographic_data[marital_condition][widow]',
                                        'name'          => 'demographic_data[marital_condition][widow]',
                                        'type'          => 'number',
                                        'label'         => __('cms::areas.custom_fields.demographic_data.marital_condition.widow.label'),
                                        'placeholder'   => __('cms::areas.custom_fields.demographic_data.marital_condition.widow.placeholder'),
                                        'help'          => __('cms::areas.custom_fields.demographic_data.marital_condition.widow.help'),
                                        'value'         => old('demographic_data.marital_condition.widow', !is_null($customField = $model->customFields->where('key', 'demographic_data.marital_condition.widow')->first()) ? $customField->value : ''),
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



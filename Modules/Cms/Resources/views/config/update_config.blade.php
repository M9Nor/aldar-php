@extends('cms::layouts.master')

@section('title', __('cms::cruds.config.config'))

@push('styles')
    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.rtl.css') }}" rel="stylesheet" type="text/css">
    @else
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.css') }}" rel="stylesheet" type="text/css">
    @endif
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('cms::cruds.config.update'),
            'items' => [
                [
                    'label' => __('cms::cruds.config.config'),
                    'link'  => route('ConfigController@index')
                ],
                [
                    'label' => $model->translateOrFirst(app()->getLocale())->label,
                    'link'  => 'javascripts:;'
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
                <a href="javascript:;" onclick="getElementById('redirectType').value = 'save_and_exit';" class="submit_form btn btn-success btn-bold">
                    {{ __('cms::global.submit') }}
                </a>
                <button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu dropdown-menu-{{ $langDirection == 'rtl' ? 'left' : 'right' }}">
                    <ul class="kt-nav">
                        {{-- <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('ConfigController@create') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li> --}}
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('ConfigController@index') }}" class="submit_form kt-nav__link">
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
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('ConfigController@updateConfig',['model'=>$model->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        <div class="row">
            <div class="col-sm-12">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        <div class="kt-section__content">
                            @if ( $model->has_additional_info == '1' )
                                <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-danger" role="tablist">
                                    @foreach($supportedLangs as $locale => $properties)
                                        <li class="nav-item">
                                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" hreflang="{{ $locale }}" href="#tab_{{ $locale }}" role="tab">
                                                <img width="25" height="18" src="{{ Module::asset('cms:flags/' . $locale . '.svg') }}" alt="" /> {{ $properties['native'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="tab-content">
                                @if ( $model->has_additional_info == '1' )
                                    @foreach($supportedLangs as $locale => $properties)
                                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_{{ $locale }}" role="tabpanel">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.image', [
                                                        'options' => [
                                                            'id'            => 'image_'.$locale,
                                                            'name'          => 'image_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => __('cms::cruds.config.image.label'),
                                                            'placeholder'   => __('cms::cruds.config.image.placeholder'),
                                                            'help'          => __('cms::cruds.config.image.help', [
                                                                'prefered_dimensions' => '150×150 | 400×400',
                                                                'mimes'               => 'png | jpeg'
                                                            ]),
                                                            'default'       => old('image_'.$locale, $model->translateOrFirst($locale)->getImage('400x400')),
                                                            'required'      => false,
                                                            'browse'        => __('cms::cruds.config.image.add_text'),
                                                            'remove'        => __('cms::cruds.config.image.remove_text')
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'            => 'label_'.$locale,
                                                            'name'          => 'label_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => __('cms::cruds.config.label.label'),
                                                            'placeholder'   => __('cms::cruds.config.label.placeholder'),
                                                            'help'          => __('cms::cruds.config.label.help'),
                                                            'value'         => old('label_'.$locale,$model->translateOrFirst($locale)->label),
                                                            'required'      => true,
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'            => 'title_'.$locale,
                                                            'name'          => 'title_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => __('cms::cruds.config.title.label'),
                                                            'placeholder'   => __('cms::cruds.config.title.placeholder'),
                                                            'help'          => __('cms::cruds.config.title.help'),
                                                            'value'         => old('title_'.$locale,$model->translateOrFirst($locale)->title),
                                                            'required'      => true,
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'description_'.$locale,
                                                            'name'          => 'description_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => __('cms::cruds.config.description.label'),
                                                            'placeholder'   => __('cms::cruds.config.description.placeholder'),
                                                            'help'          => __('cms::cruds.config.description.help'),
                                                            'value'         => old('description_'.$locale,$model->translateOrFirst($locale)->description),
                                                            'required'      => false,
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="row">
                                    <div class="col-sm-12">
                                        @php
                                            $configTrans = $model->translateOrFirst(app()->getLocale());
                                        @endphp
                                        @if($model->input_type == 'TEXT')
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'name'        => strtolower($model->key),
                                                    'label'       => $configTrans->label,
                                                    'placeholder' => $configTrans->placeholder,
                                                    'help'        => $configTrans->help,
                                                    'value'       => old(strtolower($model->key), $model->val)
                                                ]
                                            ])
                                        @elseif($model->input_type == 'TEXTAREA')
                                            @include('cms::components.inputs.textarea', [
                                                'options' => [
                                                    'rows'        => 5,
                                                    'name'        => strtolower($model->key),
                                                    'label'       => $configTrans->label,
                                                    'placeholder' => $configTrans->placeholder,
                                                    'help'        => $configTrans->help,
                                                    'value'       => old(strtolower($model->key), $model->val)
                                                ]
                                            ])
                                        {{-- @elseif($model->input_type == 'SELECT_NORMAL')
                                            @include('cms::components.inputs.select', [
                                                'options' => [
                                                    'multiple'    => false,
                                                    'id'          => strtolower($model->key),
                                                    'name'        => strtolower($model->key).'[]',
                                                    'label'       => $configTrans->label,
                                                    'placeholder' => $configTrans->placeholder,
                                                    'help'        => $configTrans->help,
                                                    'data'        => $mainCategories,
                                                    'selected'    => old(strtolower($model->key), $model->val),
                                                    'value'       => function($data, $key, $value){ return $value->id; },
                                                    'text'        => function($data, $key, $value){ return html_entity_decode(\Illuminate\Support\Str::limit(strip_tags($value->translateOrFirst(app()->getLocale())->title), 200)); },
                                                    'select'      => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                                ]
                                            ]) --}}
                                        @endif
                                    </div>
                                </div>
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



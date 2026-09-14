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
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('ConfigController@create') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
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
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('ConfigController@update',['model'=>$model->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        <div class="row">
            <div class="col-sm-12">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        {{-- <div class="kt-section__title">
                            {{ __('permissions::roles.sections.main') }}
                        </div> --}}
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
                                            <div class="col-sm-6">
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
                                            <div class="col-sm-6">
                                                @include('cms::components.inputs.text', [
                                                    'options' => [
                                                        'id'            => 'placeholder_'.$locale,
                                                        'name'          => 'placeholder_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::cruds.config.placeholder.label'),
                                                        'placeholder'   => __('cms::cruds.config.placeholder.placeholder'),
                                                        'help'          => __('cms::cruds.config.placeholder.help'),
                                                        'value'         => old('placeholder_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->placeholder),
                                                        'required'      => false,
                                                        'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                    ]
                                                ])
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                @include('cms::components.inputs.text', [
                                                    'options' => [
                                                        'id'            => 'help_'.$locale,
                                                        'name'          => 'help_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::cruds.config.help.label'),
                                                        'placeholder'   => __('cms::cruds.config.help.placeholder'),
                                                        'help'          => __('cms::cruds.config.help.help'),
                                                        'value'         => old('help_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->help),
                                                        'required'      => false,
                                                        'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                    ]
                                                ])
                                            </div>
                                            <div class="col-sm-6">
                                                @include('cms::components.inputs.text', [
                                                    'options' => [
                                                        'id'            => 'icon_'.$locale,
                                                        'name'          => 'icon_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::cruds.config.icon.label'),
                                                        'placeholder'   => __('cms::cruds.config.icon.placeholder'),
                                                        'help'          => __('cms::cruds.config.icon.help'),
                                                        'value'         => old('icon_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->icon),
                                                        'required'      => false,
                                                        'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                    ]
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                {{-- <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div> --}}
                                <hr>
                                <div class="row">
                                    <div class="col-sm-6">
                                        @include('cms::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'input_type',
                                                'name'          => 'input_type',
                                                'type'          => 'text',
                                                'label'         => __('cms::cruds.config.input_type.label'),
                                                'placeholder'   => __('cms::cruds.config.input_type.placeholder'),
                                                'help'          => __('cms::cruds.config.input_type.help'),
                                                'data'          => [
                                                    [
                                                        'text'      => 'Text',
                                                        'value'     => 'TEXT',

                                                    ],
                                                    [
                                                        'text'      => 'Textarea',
                                                        'value'     => 'TEXTAREA',

                                                    ],
                                                    // [
                                                    //     'text'      => 'Select products',
                                                    //     'value'     => 'SELECT_AJAX',

                                                    // ],
                                                    // [
                                                    //     'text'      => 'Select category',
                                                    //     'value'     => 'SELECT_NORMAL',
                                                    // ],
                                                    // [
                                                    //     'text'      => 'All category',
                                                    //     'value'     => 'SELECT_AJAX_CAT_ALL',
                                                    // ]
                                                ],
                                                'selected'      => old('input_type',$model->input_type),
                                                'value'         => function($data, $key, $value){ return $value['value']; },
                                                'text'          => function($data, $key, $value){ return $value['text']; },
                                                'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                                                'required'      => true,
                                                'searchable'    => false,
                                                'multiple'      => false,
                                            ]
                                        ])
                                    </div>
                                    <div class="col-sm-6">
                                        @include('cms::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'config_validate',
                                                'name'          => 'config_validate',
                                                'type'          => 'text',
                                                'label'         => __('cms::cruds.config.validations.label'),
                                                'placeholder'   => __('cms::cruds.config.validations.placeholder'),
                                                'help'          => __('cms::cruds.config.validations.help'),
                                                'data'          => [
                                                    [
                                                        'text'      => 'Nullable',
                                                        'value'     => 'nullable',

                                                    ],
                                                    [
                                                        'text'      => 'Required',
                                                        'value'     => 'required',
                                                    ],
                                                ],
                                                'selected'      => old('config_validate',$model->validations),
                                                'value'         => function($data, $key, $value){ return $value['value']; },
                                                'text'          => function($data, $key, $value){ return $value['text']; },
                                                'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                                                'required'      => true,
                                                'searchable'    => false,
                                                'multiple'      => false,
                                            ]
                                        ])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'            => 'key',
                                                'name'          => 'key',
                                                'type'          => 'text',
                                                'label'         => __('cms::cruds.config.key.label'),
                                                'placeholder'   => __('cms::cruds.config.key.placeholder'),
                                                'help'          => __('cms::cruds.config.key.help'),
                                                'value'         => old('key',$model->key),
                                                'required'      => true,
                                                'disabled'      => true,
                                                'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'            => 'sort_order',
                                                'name'          => 'sort_order',
                                                'type'          => 'number',
                                                'label'         => __('cms::cruds.config.sort_order.label'),
                                                'placeholder'   => __('cms::cruds.config.sort_order.placeholder'),
                                                'help'          => __('cms::cruds.config.sort_order.help'),
                                                'value'         => old('sort_order',$model->sort_order),
                                                'required'      => false,
                                                'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                            ]
                                        ])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        @include('cms::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'has_additional_info',
                                                'name'          => 'has_additional_info',
                                                'type'          => 'text',
                                                'label'         => __('cms::cruds.config.has_additional_info.label'),
                                                'placeholder'   => __('cms::cruds.config.has_additional_info.placeholder'),
                                                'help'          => __('cms::cruds.config.has_additional_info.help'),
                                                'data'          => [
                                                    [
                                                        'text'      => __('cms::cruds.no'),
                                                        'value'     => 0,
                                                    ],
                                                    [
                                                        'text'      => __('cms::cruds.yes'),
                                                        'value'     => 1,
                                                    ],
                                                ],
                                                'selected'      => old('has_additional_info',$model->has_additional_info),
                                                'value'         => function($data, $key, $value){ return $value['value']; },
                                                'text'          => function($data, $key, $value){ return $value['text']; },
                                                'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                                                'required'      => true,
                                                'searchable'    => false,
                                                'multiple'      => false,
                                            ]
                                        ])
                                    </div>
                                    <div class="col-sm-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'            => 'val',
                                                'name'          => 'val',
                                                'type'          => 'text',
                                                'label'         => __('cms::cruds.config.val.label'),
                                                'placeholder'   => __('cms::cruds.config.val.placeholder'),
                                                'help'          => __('cms::cruds.config.val.help'),
                                                'value'         => old('val',$model->val),
                                                'required'      => true,
                                                'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                            ]
                                        ])
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



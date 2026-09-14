@php
    use Modules\Cms\Entities\Content;
@endphp

@extends('cms::layouts.master')

@section('title', __('cms::cruds.contents.contents'))

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
        #tags_en + span , #tags_tr + span{
            width: 100% !important
        }
        #currency_icon div {
            width: 160px;
            height: 100px;
        }
    </style>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('cms::cruds.contents.create'),
            'items' => [
                [
                    'label' => Content::getTypeTitle($type),
                    'link'  => route('ContentController@index', ['type' => $type])
                ], [
                    'label' => __('cms::cruds.contents.create'),
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
                <a href="javascript:;" data-redirect-url="{{ route('ContentController@index',['type' => $type]) }}" class="submit_form btn btn-success btn-bold">
                    {{ __('cms::global.submit') }}
                </a>
                <button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu dropdown-menu-{{ $langDirection == 'rtl' ? 'left' : 'right' }}">
                    <ul class="kt-nav">
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('ContentController@create',['type' => $type]) }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('ContentController@index',['type' => $type]) }}" class="submit_form kt-nav__link">
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
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('ContentController@store',['type' => $type]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        <input type="hidden" name="type" value="{{$type}}">
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{  Content::getTypeTitle($type, false) }}
                        </h4>
                        <div class="row">
                            @if(Content::typeHasField($type, 'sort_order'))
                                <div class="col-sm-12">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'sort_order',
                                            'name'          => 'sort_order',
                                            'type'          => 'number',
                                            'label'         => Content::getFieldLabel($type, 'sort_order'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'sort_order'),
                                            'help'          => Content::getFieldHelp($type, 'sort_order'),
                                            'value'         => old('sort_order'),
                                            'required'      => Content::isFieldRequired($type, 'sort_order'),
                                        ]
                                    ])
                                </div>
                            @endif
                            @if(Content::typeHasField($type, 'views'))
                                <div class="col-sm-12">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'views',
                                            'name'          => 'views',
                                            'type'          => 'number',
                                            'label'         => Content::getFieldLabel($type, 'views'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'views'),
                                            'help'          => Content::getFieldHelp($type, 'views'),
                                            'value'         => old('views'),
                                            'required'      => Content::isFieldRequired($type, 'views'),
                                        ]
                                    ])
                                </div>
                            @endif
                            @if(Content::getTypeCustomFields($type))
                                @foreach(Content::getTypeCustomFields($type) as $key => $val)
                                    <div class="col-sm-12">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'            => $key,
                                                'name'          => $key,
                                                'type'          => $val['type'],
                                                'label'         => __('backend::contents.custom_fields.'.$key.'.label'),
                                                'placeholder'   => __('backend::contents.custom_fields.'.$key.'.placeholder'),
                                                'help'          => __('backend::contents.custom_fields.'.$key.'.help'),
                                                'value'         => old($key),
                                                'required'      => $val['required'],
                                            ]
                                        ])
                                    </div>
                                @endforeach
                            @endif
                            @if($type == 'filters')
                                @if(Content::typeHasField($type, 'categories'))
                                    <div class="col-sm-12">
                                        @include('cms::components.inputs.select2', [
                                            'options' => [
                                                'id'                => 'categories_ids',
                                                'name'              => 'categories_ids[]',
                                                'label'             => Content::getFieldLabel($type, 'categories'),
                                                'placeholder'       => Content::getFieldPlaceholder($type, 'categories'),
                                                'help'              => Content::getFieldHelp($type, 'categories'),
                                                'selected'          => [],
                                                'multiple'          => true,
                                                'clear_button'      => true,
                                                'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'filters']),
                                                'items_per_page'    => 20,
                                                'dir'               => 'rtl',
                                                'required'          => Content::isFieldRequired($type, 'categories'),
                                            ]
                                        ])
                                    </div>
                                @endif
                            @else
                                @if(Content::typeHasField($type, 'categories'))
                                    <div class="col-sm-12">
                                        @include('cms::components.inputs.select2', [
                                            'options' => [
                                                'id'                => 'categories_ids',
                                                'name'              => 'categories_ids[]',
                                                'label'             => Content::getFieldLabel($type, 'categories'),
                                                'placeholder'       => Content::getFieldPlaceholder($type, 'categories'),
                                                'help'              => Content::getFieldHelp($type, 'categories'),
                                                'selected'          => [],
                                                'multiple'          => true,
                                                'clear_button'      => true,
                                                'url'               => route('CategoryController@getCategoriesSelect2',['type' => $type]),
                                                'items_per_page'    => 20,
                                                'dir'               => 'rtl',
                                                'required'          => Content::isFieldRequired($type, 'categories'),
                                            ]
                                        ])
                                    </div>
                                @endif
                            @endif

                            @if(Content::typeHasField($type, 'slug'))
                                <div class="col-sm-12">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'slug',
                                            'name'          => 'slug',
                                            'label'         => Content::getFieldLabel($type, 'slug'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'slug'),
                                            'help'          => Content::getFieldHelp($type, 'slug'),
                                            'value'         => old('slug'),
                                            'required'      => Content::isFieldRequired($type, 'slug'),
                                        ]
                                    ])
                                </div>
                            @endif
                        </div>
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
                                            @if(Content::typeHasField($type, 'image'))
                                                @php
                                                    $previewSize = ['150', '150'];
                                                    if(isset(Content::imageDimensionsByType($type)['preview']))
                                                    $previewSize = preg_split('/x/', Content::imageDimensionsByType($type)['preview']);
                                                @endphp
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.image', [
                                                        'options' => [
                                                            'id'            => 'image_'.$locale,
                                                            'name'          => 'image_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Content::getFieldLabel($type, 'image'),
                                                            'placeholder'   => Content::getFieldPlaceholder($type, 'image'),
                                                            // 'help'          => Content::getFieldHelp($type, $help, [
                                                            //     'prefered_dimensions' => implode(' | ', Content::imageDimensionsByType($type)->toArray()),
                                                            //     'mimes'               => 'png | jpeg'
                                                            // ]),
                                                            'help'          => Content::getFieldHelp($type, 'image', [
                                                                'prefered_dimensions' => Content::imageDimensionsByType($type)->last(),
                                                                'mimes'               => 'png | jpeg'
                                                            ]),
                                                            'default'       => old('image_'.$locale, Content::getImage(new Content, $previewSize[0].'x'.$previewSize[1], $type)),
                                                            'required'      => Content::isFieldRequired($type, 'image'),
                                                            // 'inline'        => '3:9',
                                                            'browse'        => __('cms::cruds.contents.image.add_text'),
                                                            'remove'        => __('cms::cruds.contents.image.remove_text'),
                                                            'width'         => $previewSize[0].'px',
                                                            'height'        => $previewSize[1].'px',
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Content::typeHasField($type, 'title'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'            => 'title_'.$locale,
                                                            'name'          => 'title_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Content::getFieldLabel($type, 'title'),
                                                            'placeholder'   => Content::getFieldPlaceholder($type, 'title'),
                                                            'help'          => Content::getFieldHelp($type, 'title'),
                                                            'value'         => old('title_'.$locale),
                                                            'required'      => Content::isFieldRequired($type, 'title'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Content::typeHasField($type, 'link_trans'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'            => 'link_trans_'.$locale,
                                                            'name'          => 'link_trans_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Content::getFieldLabel($type, 'link_trans'),
                                                            'placeholder'   => Content::getFieldPlaceholder($type, 'link_trans'),
                                                            'help'          => Content::getFieldHelp($type, 'link_trans'),
                                                            'value'         => old('link_trans_'.$locale),
                                                            'required'      => Content::isFieldRequired($type, 'link_trans'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Content::typeHasField($type, 'brief'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'brief_'.$locale,
                                                            'name'          => 'brief_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Content::getFieldLabel($type, 'brief'),
                                                            'placeholder'   => Content::getFieldPlaceholder($type, 'brief'),
                                                            'help'          => Content::getFieldHelp($type, 'brief'),
                                                            'value'         => old('brief_'.$locale),
                                                            'required'      => Content::isFieldRequired($type, 'brief'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Content::typeHasField($type,'about'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'about_'.$locale,
                                                            'name'          => 'about_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Content::getFieldLabel($type, 'about'),
                                                            'placeholder'   => Content::getFieldPlaceholder($type, 'about'),
                                                            'help'          => Content::getFieldHelp($type, 'about'),
                                                            'value'         => old('about_'.$locale),
                                                            'required'      => Content::isFieldRequired($type, 'about'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Content::typeHasField($type,'keywords'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'keywords_'.$locale,
                                                            'name'          => 'keywords_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Content::getFieldLabel($type, 'keywords'),
                                                            'placeholder'   => Content::getFieldPlaceholder($type, 'keywords'),
                                                            'help'          => Content::getFieldHelp($type, 'keywords'),
                                                            'value'         => old('keywords_'.$locale),
                                                            'required'      => Content::isFieldRequired($type, 'keywords'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Content::typeHasField($type, 'description'))
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
                                                            'label'         => Content::getFieldLabel($type, 'description'),
                                                            'placeholder'   => Content::getFieldPlaceholder($type, 'description'),
                                                            'help'          => Content::getFieldHelp($type, 'description'),
                                                            'rows'          => 6,
                                                            'value'         => old('description_'.$locale),
                                                            'base_script'   => $baseScript,
                                                            'required'      => Content::isFieldRequired($type, 'description'),
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Content::typeHasField($type, 'seo_description'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'seo_description_'.$locale,
                                                            'name'          => 'seo_description_'.$locale,
                                                            'label'         => Content::getFieldLabel($type, 'seo_description'),
                                                            'placeholder'   => Content::getFieldPlaceholder($type, 'seo_description'),
                                                            'help'          => Content::getFieldHelp($type, 'seo_description'),
                                                            'rows'          => 6,
                                                            'value'         => old('seo_description_'.$locale),
                                                            'base_script'   => false,
                                                            'required'      => Content::isFieldRequired($type, 'seo_description'),
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                @if(Content::typeHasField($type, 'tags'))
                                                    @include('cms::components.inputs.taggable', [
                                                        'options' => [
                                                            'id'            => 'tags_'.$locale,
                                                            'name'          => 'tags_'.$locale.'[]',
                                                            'label'         => __('backend::projects.fields.tags.label'),
                                                            'placeholder'   => __('backend::projects.fields.tags.placeholder'),
                                                            'help'          => __('backend::projects.fields.tags.help'),
                                                            'locale'        => $locale,
                                                        ]
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @if(Content::typeHasField($type, 'link'))
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'link',
                                            'name'          => 'link',
                                            'type'          => 'text',
                                            'label'         => Content::getFieldLabel($type, 'link'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'link'),
                                            'help'          => Content::getFieldHelp($type, 'link'),
                                            'value'         => old('link'),
                                            'required'      => Content::isFieldRequired($type, 'link'),
                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                        ]
                                    ])
                                @endif

                                @if(Content::typeHasField($type, 'currency_value'))
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'currency_value',
                                            'name'          => 'currency_value',
                                            'label'         => Content::getFieldLabel($type, 'currency_value'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'currency_value'),
                                            'help'          => Content::getFieldHelp($type, 'currency_value'),
                                            'value'         => old('currency_value'),
                                            'required'      => Content::isFieldRequired($type, 'currency_value'),
                                            'type'          => 'number',
                                            'step'          => 'any',
                                        ]
                                    ])
                                @endif
                                @if(Content::typeHasField($type, 'currency_code'))
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'currency_code',
                                            'name'          => 'currency_code',
                                            'label'         => Content::getFieldLabel($type, 'currency_code'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'currency_code'),
                                            'help'          => Content::getFieldHelp($type, 'currency_code'),
                                            'value'         => old('currency_code'),
                                            'required'      => Content::isFieldRequired($type, 'currency_code'),
                                        ]
                                    ])
                                @endif
                                @if(Content::typeHasField($type, 'currency_symbol'))
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'currency_symbol',
                                            'name'          => 'currency_symbol',
                                            'label'         => Content::getFieldLabel($type, 'currency_symbol'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'currency_symbol'),
                                            'help'          => Content::getFieldHelp($type, 'currency_symbol'),
                                            'value'         => old('currency_symbol'),
                                            'required'      => Content::isFieldRequired($type, 'currency_symbol'),
                                        ]
                                    ])
                                @endif
                                @if(Content::typeHasField($type, 'currency_icon'))
                                        @php
                                            $previewSize = ['150', '150'];
                                            if(isset(Content::imageDimensionsByType($type)['preview']))
                                            $previewSize = preg_split('/x/', Content::imageDimensionsByType($type)['preview']);
                                        @endphp
                                    @include('cms::components.inputs.image', [
                                        'options' => [
                                            'id'            => 'currency_icon',
                                            'name'          => 'currency_icon',
                                            'type'          => 'text',
                                            'label'         => Content::getFieldLabel($type, 'currency_icon'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'currency_icon'),
                                            'help'          => Content::getFieldHelp($type, 'currency_icon', [
                                                'prefered_dimensions' => implode(' | ', Content::imageDimensionsByType($type)->toArray()),
                                                'mimes'               => 'png | jpeg'
                                            ]),
                                            'default'       => old('currency_icon', Content::getIconImage(new Content, $previewSize[0].'x'.$previewSize[1], $type)),
                                            'required'      => Content::isFieldRequired($type, 'currency_icon'),
                                            // 'inline'        => '3:9',
                                            'browse'        => __('cms::cruds.contents.currency_icon.add_text'),
                                            'remove'        => __('cms::cruds.contents.currency_icon.remove_text'),
                                            'width'         => $previewSize[0].'px',
                                            'height'        => $previewSize[1].'px',
                                        ]
                                    ])
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            {{-- @if(Content::typeHasField($type, 'tags'))
                                <div class="col-sm-12">
                                    @include('cms::components.inputs.taggable', [
                                        'options' => [
                                            'id'            => 'tags',
                                            'name'          => 'tags[]',
                                            'label'         => Content::getFieldLabel($type, 'tags'),
                                            'placeholder'   => Content::getFieldPlaceholder($type, 'tags'),
                                            'help'          => Content::getFieldHelp($type, 'tags'),
                                            // 'locale'        => $locale,
                                        ]
                                    ])
                                </div>
                            @endif --}}
                            @if(Content::typeHasField($type, 'attachments'))
                                <div class="col-sm-12">
                                    @include('cms::components.inputs.dropzone', [
                                        'options' => [
                                            'id'                => 'attachments',
                                            'name'              => 'attachments',
                                            'label'             => Content::getFieldLabel($type, 'attachments'),
                                            'placeholder'       => Content::getFieldPlaceholder($type, 'attachments'),
                                            'help'              => Content::getFieldHelp($type, 'attachments'),
                                            'attachments'       => [],
                                            'required'          => false,
                                            'inline'            => false,
                                            'validation_rules'  => Content::getTypeAttachmentValidationRules($type, 'attachments'),
                                            'sub_folder'        => $type,
                                        ]
                                    ])
                                </div>
                            @endif
                        </div>
                        @if(Content::typeHasField($type, 'attachments'))
                            <hr>
                            <div id="kt_repeater_1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <strong class="text-focus mb-2">{{__('cms::cruds.contents.external_attachments.attachments')}}</strong>
                                        <table class="repeater-table-head">
                                            <thead>
                                                <tr>
                                                    <th width="20%">{{__('cms::cruds.contents.external_attachments.type.label')}}</th>
                                                    <th width="25%">{{__('cms::cruds.contents.external_attachments.name.label')}}</th>
                                                    <th width="25%">{{__('cms::cruds.contents.external_attachments.description.label')}}</th>
                                                    <th width="20%">{{__('cms::cruds.contents.external_attachments.link.label')}}</th>
                                                    <th width="10%">{{__('cms::cruds.contents.external_attachments.delete')}}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div data-repeater-list="external_attachments">
                                            <div data-repeater-item >
                                                <table class="repeater-table-body">
                                                    <tbody>
                                                        <tr>
                                                            <td width="20%">
                                                                {{-- @include('cms::components.inputs.select', [
                                                                    'options' => [
                                                                        'id'            => 'attachment',
                                                                        'name'          => 'attachment',
                                                                        'type'          => 'text',
                                                                        'input_class'   => 'adwawdawd',
                                                                        'label'         => __('cms::cruds.contents.external_attachments.type'),
                                                                        // 'placeholder'   => __('cms::cruds.config.validations.placeholder'),
                                                                        // 'help'          => __('cms::cruds.config.validations.help'),
                                                                        'data'          => [
                                                                            [
                                                                                'text'      => 'file',
                                                                                'value'     => 'file',

                                                                            ],
                                                                            [
                                                                                'text'      => 'video',
                                                                                'value'     => 'video',
                                                                            ],
                                                                            [
                                                                                'text'      => 'image',
                                                                                'value'     => 'image',
                                                                            ],
                                                                        ],
                                                                        'selected'      => old('attachment','file'),
                                                                        'value'         => function($data, $key, $value){ return $value['value']; },
                                                                        'text'          => function($data, $key, $value){ return $value['text']; },
                                                                        'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                                                                        'required'      => true,
                                                                        'searchable'    => false,
                                                                        'multiple'      => false,
                                                                    ]
                                                                ]) --}}
                                                                <div class="form-group kt-form__group">
                                                                    <label for="name" class="">
                                                                        <strong class="text-focus">{{__('cms::cruds.contents.external_attachments.type.label')}} <span class="text-danger">*</span></strong>
                                                                    </label>
                                                                    <select class="form-control asdawdwad" name="type" id="">
                                                                        <option value="video">Video</option>
                                                                        <option value="file">File</option>
                                                                        <option value="image">Image</option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td width="25%">
                                                                @include('cms::components.inputs.text', [
                                                                    'options' => [
                                                                        'id'                => 'name',
                                                                        'name'              => 'name',
                                                                        'type'              => 'text',
                                                                        'label'             => __('cms::cruds.contents.external_attachments.name.label'),
                                                                        'placeholder'       => __('cms::cruds.contents.external_attachments.name.placeholder'),
                                                                        'value'             => old('name'),
                                                                        'required'          => true,
                                                                        'inline'            => false,
                                                                        'maxlength'         => 191,
                                                                        'direction'         => 'ltr',
                                                                        'status_removal'    => false
                                                                    ]
                                                                ])
                                                            </td>
                                                            <td width="25%">
                                                                @include('cms::components.inputs.text', [
                                                                    'options' => [
                                                                        'id'                => 'description',
                                                                        'name'              => 'description',
                                                                        'type'              => 'text',
                                                                        'label'             => __('cms::cruds.contents.external_attachments.description.label'),
                                                                        'placeholder'       => __('cms::cruds.contents.external_attachments.description.placeholder'),
                                                                        'value'             => old('description'),
                                                                        'required'          => false,
                                                                        'inline'            => false,
                                                                        'maxlength'         => 100,
                                                                        'direction'         => 'ltr',
                                                                        'status_removal'    => false
                                                                    ]
                                                                ])
                                                            </td>
                                                            <td width="20%">
                                                                @include('cms::components.inputs.text', [
                                                                    'options' => [
                                                                        'id'                => 'link',
                                                                        'name'              => 'link',
                                                                        'type'              => 'text',
                                                                        'label'             => __('cms::cruds.contents.external_attachments.link.label'),
                                                                        'placeholder'       => __('cms::cruds.contents.external_attachments.link.placeholder'),
                                                                        'value'             => old('link'),
                                                                        'required'          => true,
                                                                        'inline'            => false,
                                                                        'maxlength'         => 191,
                                                                        'direction'         => 'ltr',
                                                                        'status_removal'    => false
                                                                    ]
                                                                ])
                                                            </td>
                                                            <td width="10%">
                                                                <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                                                                    <i class="la la-trash-o"></i>
                                                                    {{__('cms::cruds.contents.external_attachments.delete')}}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:20px">
                                    <div class="col-md-4">
                                        <a href="javascript:;" data-repeater-create="" class="btn btn-label-success btn-bold">
                                            <i class="la la-plus"></i>{{__('backend::projects.fields.pay.add')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endcomponent
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    {{-- <script src="assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script> --}}

    <script>
        // Class definition
        var KTFormRepeater = function() {
        // Private functions
        var demo1 = function() {
            $('#kt_repeater_1').repeater({
                initEmpty: true,
                // defaultValues: {
                //     'text-input': 'foo'
                // },
                show: function () {
                    $(this).slideDown();
                    // $(this).find('.kt-bootstrap-select').selectpicker();
                    // $('.kt-bootstrap-select').selectpicker('refresh');
                    // var test = $(this).find('.asdawdwad').find('option:selected');
                    // var test = $('.asdawdwad').find('option:selected');
                    // console.log(test.val());
                    // console.log(test.text());

                    // var opt_sel = $('.asdawdwad option:selected');
                    // opt_sel.val(test.val());
                    // opt_sel.text(test.text());
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });
        }
        return {
            // public functions
            init: function() {
                demo1();
            }
        };
        }();
            jQuery(document).ready(function() {
            KTFormRepeater.init();
        });

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



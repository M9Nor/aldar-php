@php
    use Modules\Cms\Entities\Category;
@endphp

@extends('cms::layouts.master')

@section('title', __('cms::cruds.categories.categories'))

@push('styles')
    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.rtl.css') }}" rel="stylesheet" type="text/css">
    @else
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.css') }}" rel="stylesheet" type="text/css">
    @endif
    <style>
        #tags_en + span , #tags_tr + span{
            width: 100% !important
        }
    </style>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('cms::cruds.categories.update'),
            'items' => [
                [
                    'label' => Category::getTypeTitle($type),
                    'link'  => route('CategoryController@index',['type' => $type])
                ], [
                    'label' => __('cms::cruds.categories.update'),
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
                            <a href="javascript:;" data-redirect-url="{{ route('CategoryController@create',['type' => $type]) }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('CategoryController@index',['type' => $type]) }}" class="submit_form kt-nav__link">
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
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('CategoryController@update',['model'=>$model->id,'type' => $type]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{  Category::getTypeTitle($type, false) }}
                        </h4>
                        <div class="row">
                            @if(Category::typeHasField($type, 'sort_order'))
                                <div class="col-sm-12">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'sort_order',
                                            'name'          => 'sort_order',
                                            'type'          => 'number',
                                            'label'         => Category::getFieldLabel($type, 'sort_order'),
                                            'placeholder'   => Category::getFieldPlaceholder($type, 'sort_order'),
                                            'help'          => Category::getFieldHelp($type, 'sort_order'),
                                            'value'         => old('sort_order', $model->sort_order),
                                            'required'      => Category::isFieldRequired($type, 'sort_order'),
                                        ]
                                    ])
                                </div>
                            @endif
                            @if(Category::typeHasField($type, 'parent'))
                                <div class="col-sm-12">
                                    @include('cms::components.inputs.select2', [
                                        'options' => [
                                            'id'                => 'parent_id',
                                            'name'              => 'parent_id',
                                            'label'             => Category::getFieldLabel($type, 'parent'),
                                            'placeholder'       => Category::getFieldPlaceholder($type, 'parent'),
                                            'help'              => Category::getFieldHelp($type, 'parent'),
                                            'selected'          => [],
                                            'selected'          => $allParentCategory->map(function($item) {
                                                return $item->formAjaxArray();
                                            }),
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => route('CategoryController@getCategoriesSelect2',['type' => $type == 'filters' ? 'filter_categories' : $type, 'model'=>$model]),
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'required'          => Category::isFieldRequired($type, 'parent'),
                                        ]
                                    ])
                                </div>
                            @endif
                            @if(Category::typeHasField($type, 'slug'))
                                <div class="col-sm-12">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'            => 'slug',
                                            'name'          => 'slug',
                                            'label'         => Category::getFieldLabel($type, 'slug'),
                                            'placeholder'   => Category::getFieldPlaceholder($type, 'slug'),
                                            'help'          => Category::getFieldHelp($type, 'slug'),
                                            'value'         => old('slug', $model->slug),
                                            'required'      => Category::isFieldRequired($type, 'slug'),
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
                                            @if(Category::typeHasField($type, 'image'))
                                                @php
                                                    $previewSize = ['150', '150'];
                                                    if(isset(Category::imageDimensionsByType($type)['preview']))
                                                    $previewSize = preg_split('/x/', Category::imageDimensionsByType($type)['preview']);
                                                @endphp
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.image', [
                                                        'options' => [
                                                            'id'            => 'image_'.$locale,
                                                            'name'          => 'image_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Category::getFieldLabel($type, 'image'),
                                                            'placeholder'   => Category::getFieldPlaceholder($type, 'image'),
                                                            'help'          => Category::getFieldHelp($type, 'image', [
                                                                'prefered_dimensions' => implode(' | ', Category::imageDimensionsByType($type)->toArray()),
                                                                'mimes'               => 'png | jpeg'
                                                            ]),
                                                            'default'       => old('image_'.$locale, $model->translate($locale) == NULL ? Category::getImage(new Category, $previewSize[0].'x'.$previewSize[1], $type) : Category::getImage($model->translate($locale), $previewSize[0].'x'.$previewSize[1], $type)),
                                                            'required'      => Category::isFieldRequired($type, 'image'),
                                                            'browse'        => __('cms::cruds.categories.image.add_text'),
                                                            'remove'        => __('cms::cruds.categories.image.remove_text'),
                                                            'width'         => $previewSize[0].'px',
                                                            'height'        => $previewSize[1].'px',
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Category::typeHasField($type, 'title'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'            => 'title_'.$locale,
                                                            'name'          => 'title_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Category::getFieldLabel($type, 'title'),
                                                            'placeholder'   => Category::getFieldPlaceholder($type, 'title'),
                                                            'help'          => Category::getFieldHelp($type, 'title'),
                                                            'value'         => old('title_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->title),
                                                            'required'      => Category::isFieldRequired($type, 'title'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Category::typeHasField($type, 'keywords'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'brief_'.$locale,
                                                            'name'          => 'brief_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Category::getFieldLabel($type, 'brief'),
                                                            'placeholder'   => Category::getFieldPlaceholder($type, 'brief'),
                                                            'help'          => Category::getFieldHelp($type, 'brief'),
                                                            'value'         => old('brief_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->brief),
                                                            'required'      => Category::isFieldRequired($type, 'brief'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Category::typeHasField($type, 'keywords'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'keywords_'.$locale,
                                                            'name'          => 'keywords_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Category::getFieldLabel($type, 'keywords'),
                                                            'placeholder'   => Category::getFieldPlaceholder($type, 'keywords'),
                                                            'help'          => Category::getFieldHelp($type, 'keywords'),
                                                            'value'         => old('keywords_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->keywords),
                                                            'required'      => Category::isFieldRequired($type, 'keywords'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Category::typeHasField($type, 'seo_description'))
                                                <div class="col-sm-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'seo_description_'.$locale,
                                                            'name'          => 'seo_description_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => Category::getFieldLabel($type, 'seo_description'),
                                                            'placeholder'   => Category::getFieldPlaceholder($type, 'seo_description'),
                                                            'help'          => Category::getFieldHelp($type, 'seo_description'),
                                                            'value'         => old('seo_description_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->seo_description),
                                                            'required'      => Category::isFieldRequired($type, 'seo_description'),
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                            @if(Category::typeHasField($type, 'description'))
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
                                                            'label'         => Category::getFieldLabel($type, 'description'),
                                                            'placeholder'   => Category::getFieldPlaceholder($type, 'description'),
                                                            'help'          => Category::getFieldHelp($type, 'description'),
                                                            'rows'          => 6,
                                                            'value'         => old('description_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->description),
                                                            'base_script'   => $baseScript,
                                                            'required'      => Category::isFieldRequired($type, 'description'),
                                                        ]
                                                    ])
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                @if(Category::typeHasField($type, 'tags'))
                                                    @include('cms::components.inputs.taggable', [
                                                        'options' => [
                                                            'id'            => 'tags_'.$locale,
                                                            'name'          => 'tags_'.$locale.'[]',
                                                            'label'         => __('cms::cruds.categories.tags.label'),
                                                            'placeholder'   => __('cms::cruds.categories.tags.placeholder'),
                                                            'help'          => __('cms::cruds.categories.tags.help'),
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
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="row">
                                    @if(Category::typeHasField($type, 'icon_image'))
                                        @php
                                            $previewSizeIcon = ['150', '150'];
                                            if(isset(Category::imageDimensionsByType($type)['preview']))
                                            $previewSizeIcon = preg_split('/x/', Category::imageDimensionsByType($type)['preview']);
                                        @endphp
                                        <div class="col-md-12">
                                            @include('cms::components.inputs.image', [
                                                'options' => [
                                                    'id'            => 'icon_image',
                                                    'name'          => 'icon_image',
                                                    'type'          => 'text',
                                                    'label'         => Category::getFieldLabel($type, 'icon_image'),
                                                    'placeholder'   => Category::getFieldPlaceholder($type, 'icon_image'),
                                                    'help'          => Category::getFieldHelp($type, 'icon_image', [
                                                        'prefered_dimensions' => implode(' | ', Category::imageDimensionsByType($type)->toArray()),
                                                        'mimes'               => 'png | jpeg'
                                                    ]),
                                                    'default'       => old('icon_image', $model->icon_image == NULL ? Category::getIconImage(new Category, $previewSizeIcon[0].'x'.$previewSizeIcon[1], $type) : Category::getIconImage($model, $previewSizeIcon[0].'x'.$previewSizeIcon[1], $type)),
                                                    'required'      => Category::isFieldRequired($type, 'icon_image'),
                                                    'browse'        => __('cms::cruds.categories.icon_image.add_text'),
                                                    'remove'        => __('cms::cruds.categories.icon_image.remove_text'),
                                                    'width'         => $previewSizeIcon[0].'px',
                                                    'height'        => $previewSizeIcon[1].'px',
                                                ]
                                            ])
                                        </div>
                                    @endif
                                </div>
                                {{-- @include('cms::components.inputs.taggable', [
                                    'options' => [
                                        'id'            => 'tags',
                                        'name'          => 'tags[]',
                                        'label'         => __('cms::cruds.categories.tags.label'),
                                        'placeholder'   => __('cms::cruds.categories.tags.placeholder'),
                                        'help'          => __('cms::cruds.categories.tags.help'),
                                        'data'          => $model->tags,
                                        'selected'      => $model->tags->pluck('id')->toArray(),
                                        'value'         => function($data, $key, $value){ return $value->id; },
                                        'text'          => function($data, $key, $value) { return $value->translate()->text; },
                                        'select'        => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                                    ]
                                ]) --}}
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



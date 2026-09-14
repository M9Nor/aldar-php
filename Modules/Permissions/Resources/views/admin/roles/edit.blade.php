@extends('cms::layouts.master')

@section('title', __('permissions::roles.title'))

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
            'title' =>  __('permissions::roles.edit', ['role' => $model->translateOrFirst()->title]),
            'items' => [
                [
                    'label' => __('permissions::roles.title'),
                    'link'  => route('RoleController@index')
                ], [
                    'label' => __('permissions::roles.edit', ['role' => $model->translateOrFirst()->title]),
                    'link'  => 'javascript:;'
                ]
            ]
        ]
    ])

        @slot('main')

        @endslot
        @slot('toolbar')
            <div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-outline-brand btn-bold global-select-all">{{ __('cms::global.select_all') }}</button>
                <button type="button" class="btn btn-outline-brand btn-bold global-select-none">{{ __('cms::global.select_none') }}</button>
            </div>
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
                            <a href="javascript:;" data-redirect-url="{{ route('RoleController@create') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('RoleController@index') }}" class="submit_form kt-nav__link">
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
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('RoleController@update', ['model' => $model->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        <div class="row">
            <div class="col-xl-4 col-lg-6 col-sm-12">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        <div class="kt-section__title">
                            {{ __('permissions::roles.sections.programmatic') }}
                        </div>
                        <div class="kt-section__content">
                            @include('cms::components.inputs.text', [
                                'options' => [
                                    'id'            => 'name',
                                    'name'          => 'name',
                                    'type'          => 'text',
                                    'label'         => __('permissions::roles.fields.name.label'),
                                    'placeholder'   => __('permissions::roles.fields.name.placeholder'),
                                    'help'          => __('permissions::roles.fields.name.help'),
                                    'value'         => old('name', $model->name),
                                    'required'      => true
                                ]
                            ])
                            @include('cms::components.inputs.text', [
                                'options' => [
                                    'id'            => 'color',
                                    'name'          => 'color',
                                    'type'          => 'color',
                                    'label'         => __('permissions::roles.fields.color.label'),
                                    'placeholder'   => __('permissions::roles.fields.color.placeholder'),
                                    'help'          => __('permissions::roles.fields.color.help'),
                                    'value'         => old('color', $model->color),
                                    'required'      => true
                                ]
                            ])
                        </div>
                        <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div>
                        <div class="kt-section__title">
                            {{ __('permissions::roles.sections.main') }}
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
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'            => 'title_' . $locale,
                                                'name'          => 'title[' . $locale . ']',
                                                'type'          => 'text',
                                                'label'         => __('permissions::roles.fields.title.label'),
                                                'placeholder'   => __('permissions::roles.fields.title.placeholder'),
                                                'help'          => __('permissions::roles.fields.title.help'),
                                                'value'         => old('title.' . $locale, $model->translateOrFirst($locale)->title),
                                                'required'      => true,
                                                'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                            ]
                                        ])
                                        @include('cms::components.inputs.textarea', [
                                            'options' => [
                                                'id'            => 'description_' . $locale,
                                                'name'          => 'description[' . $locale . ']',
                                                'type'          => 'text',
                                                'label'         => __('permissions::roles.fields.description.label'),
                                                'placeholder'   => __('permissions::roles.fields.description.placeholder'),
                                                'help'          => __('permissions::roles.fields.description.help'),
                                                'value'         => old('description.' . $locale, $model->translateOrFirst($locale)->description),
                                                'required'      => false,
                                                'rows'          => 4,
                                                'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                            ]
                                        ])
                                    </div>
                                @endforeach
                            </div>
                            <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div>
                            <div class="kt-section__title">
                                {{ __('permissions::roles.sections.management') }}
                            </div>
                            <div class="kt-section__content">
                                @include('cms::components.inputs.select', [
                                    'options' => [
                                        'multiple'      => true,
                                        'id'            => 'type',
                                        'name'          => 'manageable_roles[]',
                                        'type'          => 'text',
                                        'label'         => __('permissions::roles.fields.manageable_roles.label'),
                                        'placeholder'   => __('permissions::roles.fields.manageable_roles.placeholder'),
                                        'help'          => __('permissions::roles.fields.manageable_roles.help'),
                                        'data'          => $roles,
                                        'selected'      => old('manageable_roles', $model->manageableRoles->pluck('id')->toArray()),
                                        'value'         => function($data, $key, $value){ return $value->id; },
                                        'text'          => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                                        'select'        => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                                        'required'      => false,
                                        'searchable'    => false,
                                    ]
                                ])
                            </div>
                        </div>
                    </div>

                @endcomponent
            </div>
            <div class="col-xl-8 col-lg-6 col-sm-12">
                <div class="accordion accordion-solid accordion-panel accordion-toggle-svg" id="accordion">
                    @foreach($permsGroups as $group)
                    <div class="card">
                        @php
                            // Checks if the accordion has any item checked to either collapse the accordion.
                            $hasCheckedItems = $group->abilities->pluck('id')->intersect($model->abilities->pluck('id'))->count() > 0;
                        @endphp
                        <div class="card-header" id="header_{{ $group->id }}">
                            <div class="card-title {{ $hasCheckedItems ? '' : 'collapsed' }}" data-toggle="collapse" data-target="#card__id_{{ $group->id }}" aria-expanded="{{ $hasCheckedItems ? '' : 'collapsed' }}" aria-controls="card__id_{{ $group->id }}">
                                <i class="{{ $group->icon }}"></i>
                                <span>
                                    {{ $group->translateOrFirst()->title }}
                                </span> {!! $langDirection == 'rtl' ? config('cms.svgs.left_arrow') : config('cms.svgs.right_arrow') !!}

                            </div>
                        </div>
                        <div id="card__id_{{ $group->id }}" class="collapse {{ $hasCheckedItems ? 'show' : '' }}" aria-labelledby="header_{{ $group->id }}" style="">
                            <div class="card-body">
                                <div class="row multi-select-btn-group mt-2 mb-4">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-clean btn-bold btn-upper select_all">{{ __('cms::global.select_all') }}</button>
                                        <button type="button" class="btn btn-sm btn-clean btn-bold btn-upper select_none">{{ __('cms::global.select_none') }}</button>
                                    </div>
                                </div>
                                <div class="row switch-group permissions p-2">
                                    @foreach($group->abilities as $item)
                                        <div class="col-xl-4 col-lg-12 col-6">
                                            <div class="form-group row mb-0">
                                                <div class="col-xl-4 col-4">
                                                    <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                                        <label>
                                                            <input type="checkbox" {{ $model->abilities->pluck('id')->contains($item->id) ? 'checked' : '' }} name="permissions[]" value="{{ $item->name }}">
                                                            <span></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <label class="col-xl-8 col-8 col-form-label text-left">
                                                    <strong>
                                                        {{ $item->translateOrFirst()->title }}
                                                    </strong>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
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

        // Selects all/none of accordion items items.
        $('.select_all').on('click', function() {
            $(this).closest('.multi-select-btn-group').siblings('.switch-group').find('input[name*="permissions"]').prop('checked', true);
        });

        $('.select_none').on('click', function() {
            $(this).closest('.multi-select-btn-group').siblings('.switch-group').find('input[name*="permissions"]').prop('checked', false);
        });

        $('.global-select-all').on('click', function() {
            $('.collapse').collapse('show');
            $('input[name*="permissions"]').prop('checked', true);
        });

        $('.global-select-none').on('click', function() {
            $('.collapse').collapse('hide');
            $('input[name*="permissions"]').prop('checked', false);
        });
    </script>
@endpush


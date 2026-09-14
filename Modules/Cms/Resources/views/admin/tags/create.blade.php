@extends('cms::layouts.master')

@section('title', __('cms::areas.tags.tags'))

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
            'title' =>  __('cms::areas.create_new_tag'),
            'items' => [
                [
                    'label' => __('cms::areas.tags.tags'),
                    'link'  => route('TagController@index')
                ], [
                    'label' => __('cms::areas.create_new_tag'),
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
                <a href="javascript:;" data-redirect-url="{{ route('TagController@index') }}" class="submit_form btn btn-success btn-bold">
                    {{ __('cms::global.submit') }}
                </a>
                <button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu dropdown-menu-{{ $langDirection == 'rtl' ? 'left' : 'right' }}">
                    <ul class="kt-nav">
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('TagController@create') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('TagController@index') }}" class="submit_form kt-nav__link">
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
    <form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('TagController@store') }}" method="POST">
        @csrf
        <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
        {{-- <input type="hidden" name="type" value="{{$type}}"> --}}
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                    <div class="kt-section">
                        <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">
                            {{__('cms::areas.create_new_tag')}}
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
                                                @include('cms::components.inputs.image', [
                                                    'options' => [
                                                        'id'            => 'image_'.$locale,
                                                        'name'          => 'image_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.tags.fields.image.label'),
                                                        'placeholder'   => __('cms::areas.tags.fields.image.placeholder'),
                                                        'help'          => __('cms::areas.tags.fields.image.help', [
                                                            'prefered_dimensions' => '150×150 | 400×400',
                                                            'mimes'               => 'png | jpeg'
                                                        ]),
                                                        'default'       => old('image_'.$locale, (new Modules\Cms\Entities\TagTranslation)->getImage('400x400')),
                                                        'required'      => false,
                                                        'browse'        => __('cms::cruds.contents.image.add_text'),
                                                        'remove'        => __('cms::cruds.contents.image.remove_text'),
                                                    ]
                                                ])
                                                @include('cms::components.inputs.text', [
                                                    'options' => [
                                                        'id'            => 'text_'.$locale,
                                                        'name'          => 'text_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('cms::areas.tags.fields.text.label'),
                                                        'placeholder'   => __('cms::areas.tags.fields.text.placeholder'),
                                                        'help'          => __('cms::areas.tags.fields.text.help'),
                                                        'value'         => old('text'),
                                                        'required'      => true,
                                                    ]
                                                ])
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                @include('cms::components.inputs.textarea', [
                                                    'options' => [
                                                        'name'              => 'description_'.$locale,
                                                        'id'                => 'description_'.$locale,
                                                        'type'              => 'text',
                                                        'label'             => __('cms::areas.tags.fields.description.label'),
                                                        'placeholder'       => __('cms::areas.tags.fields.description.placeholder'),
                                                        'help'              => __('cms::areas.tags.fields.description.help'),
                                                        'value'             => old('description_'.$locale),
                                                        'required'          => false,
                                                        'inline'            => false,
                                                        'direction'         => 'ltr',
                                                        'status_removal'    => false
                                                    ]
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('cms::components.inputs.textarea', [
                                            'options' => [
                                                'id'                => 'keywords',
                                                'name'              => 'keywords',
                                                'type'              => 'text',
                                                'label'             => __('cms::areas.tags.fields.keywords.label'),
                                                'placeholder'       => __('cms::areas.tags.fields.keywords.placeholder'),
                                                'help'              => __('cms::areas.tags.fields.keywords.help'),
                                                'value'             => old('keywords'),
                                                'required'          => false,
                                                'inline'            => false,
                                                'direction'         => 'ltr',
                                                'status_removal'    => false
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




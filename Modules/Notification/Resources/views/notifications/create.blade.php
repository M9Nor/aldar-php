@extends('cms::layouts.master')

@section('title', __('notification::strings.notifications'))

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('notification::strings.create_new'),
            'items' => [
                [
                    'label' => __('notification::strings.notifications'),
                    'link'  => route('NotificationsController@index')
                ], [
                    'label' => __('notification::strings.create_new'),
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
            <a href="javascript:;" data-redirect-url="{{ route('NotificationsController@create') }}" class="submit_form btn btn-success btn-bold">
                {{ __('cms::global.send') }}
            </a>
            {{-- <div class="btn-group"> --}}
                {{-- <button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button> --}}
                {{-- <div class="dropdown-menu dropdown-menu-{{ $langDirection == 'rtl' ? 'left' : 'right' }}">
                    <ul class="kt-nav">
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('NotificationsController@create') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('NotificationsController@index') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-indent-dots"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_exit') }}</span> <!-- Save &amp; exit -->
                            </a>
                        </li>
                    </ul>
                </div> --}}
            {{-- </div> --}}
        @endslot
    @endcomponent
@endsection

@section('content')
<form onsubmit="onFormSubmit(event);" class="kt-form kt-form--label-right" id="addNewForm" action="{{ route('NotificationsController@postCreate') }}" method="POST">
    @csrf
    <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
    {{-- <input type="hidden" name="type" value="{{$type}}"> --}}
    <div class="row">
        <div class="col-xl-8 offset-xl-2">
            @component('cms::components.partials.portlet', ['portletClass' => 'kt-portlet--height-fluid'])
                <div class="kt-section">
                    <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">
                        {{__('notification::strings.create_new')}}
                    </h4>
                    <div class="kt-section__content">
                        @include('cms::components.inputs.image', [
                            'options' => [
                                'id'            => 'image_'.app()->getLocale(),
                                'name'          => 'image_'.app()->getLocale(),
                                'type'          => 'text',
                                'label'         => __('cms::areas.fields.image.label'),
                                'placeholder'   => __('cms::areas.fields.image.placeholder'),
                                'help'          => __('cms::areas.fields.image.help', [
                                    'prefered_dimensions' => '360x180',
                                    'mimes'               => 'png | jpeg'
                                ]),
                                'default'       => old('image_'.app()->getLocale(), (new Modules\Notification\Entities\FirebaseNotification)->getImage('360x180')),
                                'required'      => false,
                                'browse'        => __('cms::cruds.contents.image.add_text'),
                                'remove'        => __('cms::cruds.contents.image.remove_text'),
                                'inline'        => '3:9',
                                'width'         => '360px',
                                'height'        => '180px',
                            ]
                        ])
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'            => 'group',
                                'name'          => 'group',
                                'type'          => 'text',
                                'label'         => __('notification::strings.fields.group.label'),
                                'placeholder'   => __('notification::strings.fields.group.placeholder'),
                                'help'          => __('notification::strings.fields.group.help'),
                                'data'          => $roles->sortBy('id'),
                                'selected'      => old('group'),
                                'value'         => function($data, $key, $value){ return $value->name; },
                                'text'          => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                                'sub_text'      => function($data, $key, $value){ return $value->translateOrFirst()->description; },
                                'select'        => function($data, $selected, $key, $value){ return $value->name == $selected; },
                                'nullable'      => true,
                                'required'      => false,
                                'searchable'    => true,
                                'inline'        => '3:9',
                            ]
                        ])
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'name'          => 'link',
                                'label'         => __('notification::strings.fields.notification_link.label'),
                                'placeholder'   => __('notification::strings.fields.notification_link.placeholder'),
                                'help'          => __('notification::strings.fields.notification_link.help'),
                                'value'         => old('link'),
                                'required'      => false,
                                'inline'        => '3:9',
                                'direction'     => 'ltr',
                            ]
                        ])
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'name'          => 'title',
                                'label'         => __('notification::strings.fields.notification_title.label'),
                                'help'          => __('notification::strings.fields.notification_title.help'),
                                'placeholder'   => __('notification::strings.fields.notification_title.placeholder'),
                                'value'         => old('title'),
                                'required'      => true,
                                'inline'        => '3:9',
                            ]
                        ])
                        @include('cms::components.inputs.textarea', [
                            'options' => [
                                'id'            => 'body',
                                'name'          => 'body',
                                'type'          => 'text',
                                'label'         => __('notification::strings.fields.notification_body.label'),
                                'placeholder'   => __('notification::strings.fields.notification_body.placeholder'),
                                'help'          => __('notification::strings.fields.notification_body.help'),
                                'value'         => old('body'),
                                'required'      => true,
                                'inline'        => '3:9',
                            ]
                        ])
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



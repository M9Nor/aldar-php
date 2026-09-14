@extends('cms::layouts.master')

@section('title', __('cms::dashboard.title'))

@section('subheader')
    @include('cms::includes.subheader', [
        'options' => [
            'title' =>  __('cms::dashboard.title'),
            'items' => []
        ]
    ])
@endsection

@section('content')

    @component('cms::components.partials.portlet')
        @slot('bodyClass', 'kt-portlet__body--fit')
        <div class="kt-widget1">
            <div class="kt-widget1__item">
                <div class="kt-widget1__info">
                    <h3 class="kt-widget1__title">{{ __('cms::dashboard.update_currency') }}</h3>
                    <span class="kt-widget1__desc">
                        <a target="_blank" href="https://aldar-emlak.com/update_currency" class="submit_form btn btn-success btn-bold">
                            {{ __('cms::dashboard.update') }}
                        </a>
                    </span>
                </div>
            </div>
            <div class="kt-widget1__item">
                <div class="kt-widget1__info">
                    <h3 class="kt-widget1__title">{{ __('cms::dashboard.total_users.title') }}</h3>
                    <span class="kt-widget1__desc">{{ __('cms::dashboard.total_users.description') }}</span>
                </div>
                <span class="kt-widget1__number kt-font-brand">{{ $total_users }}</span>
            </div>
            <div class="kt-widget1__item">
                <div class="kt-widget1__info">
                    <h3 class="kt-widget1__title">{{ __('cms::dashboard.total_roles.title') }}</h3>
                    <span class="kt-widget1__desc">{{ __('cms::dashboard.total_roles.description') }}</span>
                </div>
                <span class="kt-widget1__number kt-font-danger">{{ $total_roles }}</span>
            </div>
            <div class="kt-widget1__item">
                <div class="kt-widget1__info">
                    <h3 class="kt-widget1__title">{{ __('cms::dashboard.total_permissions.title') }}</h3>
                    <span class="kt-widget1__desc">{{ __('cms::dashboard.total_permissions.description') }}</span>
                </div>
                <span class="kt-widget1__number kt-font-success">{{ $total_permissions }}</span>
            </div>
        </div>
    @endcomponent
    
@endsection

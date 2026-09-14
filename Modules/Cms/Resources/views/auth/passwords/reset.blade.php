@extends('cms::layouts.auth')

@section('title', __('cms::auth.reset_password.title'))

@section('content')

<div class="kt-login__signup d-block">
    <div class="kt-login__head">
        <h3 class="kt-login__title">{{ __('cms::auth.reset_password.title') }}</h3>
    </div>
    <form onsubmit="onFormSubmit(event);" class="kt-form" method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <div class="input-group">
            <input id="email" type="email" class="form-control" name="email" placeholder="{{ __('cms::auth.attributes.email.placeholder') }}" value="{{ $email ?? old('email') }}" autocomplete="email">
        </div>
        <div class="input-group">
            <input id="password" type="password" class="form-control" name="password" placeholder="{{ __('cms::auth.attributes.password.placeholder') }}" autocomplete="new-password">
        </div>
        <div class="input-group">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('cms::auth.attributes.password_confirmation.placeholder') }}" autocomplete="new-password">
        </div>
        <div class="kt-login__actions">
            <button type="submit" class="btn btn-brand btn-pill kt-login__btn-primary" data-on-loading-text="{{ __('cms::auth.reset_password.reseting') }}">{{ __('cms::auth.reset_password.reset_password') }}</button>&nbsp;&nbsp;
            <button type="button" onclick="window.location.href=`{{ route('login') }}`" class="btn btn-secondary btn-pill kt-login__btn-secondary">{{ __('cms::auth.register.back_to_login') }}</button>
        </div>
    </form>
</div>

@endsection

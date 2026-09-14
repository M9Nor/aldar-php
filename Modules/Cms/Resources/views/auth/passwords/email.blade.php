@extends('cms::layouts.auth')

@section('content')

<div class="kt-login__forgot d-block">
    <div class="kt-login__head">
        <h3 class="kt-login__title">{{ __('cms::auth.forgot_password.title') }}</h3>
    </div>
    <form onsubmit="onFormSubmit(event);" class="kt-form" method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="input-group">
            <input id="email" type="email" class="form-control" name="email" placeholder="{{ __('cms::auth.attributes.email.placeholder') }}" value="{{ old('email') }}" autocomplete="email">
        </div>
        <div class="kt-login__actions">
            <button type="submit" class="btn btn-brand btn-pill kt-login__btn-primary" data-on-loading-text="{{ __('cms::auth.forgot_password.sending') }}">{{ __('cms::auth.forgot_password.send_password_reset_link') }}</button>&nbsp;&nbsp;
            <button type="button" onclick="window.location.href=`{{ route('login') }}`" class="btn btn-secondary btn-pill kt-login__btn-secondary">{{ __('cms::auth.forgot_password.back_to_login') }}</button>
        </div>
    </form>
</div>

@if(Route::has('register'))
    <div class="kt-login__account">
        <span class="kt-login__account-msg">
            {{ __('cms::auth.forgot_password.dont_have_an_account_yet') }}
        </span>
        &nbsp;&nbsp;
        <a href="{{ route('register') }}" class="kt-login__account-link">{{ __('cms::auth.forgot_password.register') }}!</a>
    </div>
@endif

@endsection

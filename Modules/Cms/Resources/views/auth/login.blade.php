@extends('cms::layouts.auth')

@section('title', __('cms::auth.login.title'))

@section('content')

<div class="kt-login__signin d-block">
    <div class="kt-login__head">
        <h3 class="kt-login__title">{{ __('cms::auth.login.title') }}</h3>
    </div>
    <form onsubmit="onFormSubmit(event);" class="kt-form" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="input-group">
            <input id="identity" type="identity" class="form-control" name="identity" placeholder="{{ __('cms::auth.attributes.identity.placeholder') }}" value="{{ old('identity') }}" autocomplete="identity">
        </div>
        <div class="input-group">
            <input id="password" type="password" class="form-control" name="password" placeholder="{{ __('cms::auth.attributes.password.placeholder') }}" autocomplete="new-password">
        </div>
        <div class="row kt-login__extra">
            <div class="col">
                <label class="kt-checkbox">
                    <input type="checkbox"  name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('cms::auth.attributes.remember_me') }}
                    <span></span>
                </label>
            </div>
            {{-- <div class="col kt-align-right">
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="kt-login__link">
                        {{ __('cms::auth.attributes.forgot_your_password') }}
                    </a>
                @endif
            </div> --}}
        </div>
        <div class="kt-login__actions">
            <button type="submit" class="btn btn-brand btn-pill kt-login__btn-primary" data-on-loading-text="{{ __('cms::auth.login.logging') }}">{{ __('cms::auth.login.title') }}</button>
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

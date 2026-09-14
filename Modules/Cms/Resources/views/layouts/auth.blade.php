<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" direction="{{ LaravelLocalization::getCurrentLocaleDirection() }}" style="direction: {{ LaravelLocalization::getCurrentLocaleDirection() }};">

	<head>
		<base href="../../../">
		<meta charset="utf-8" />
		<title>{{ __('cms::dashboard.website') }} | @yield('title')</title>
		<meta name="description" content="Login to Aldar control panel.">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		@include('cms::auth.includes.styles')
		@stack('styles')
		<link rel="shortcut icon" type="image/x-icon" href="{{\Module::asset('frontend:assets/fav.svg')}}">
	</head>

	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
		<div class="kt-grid kt-grid--ver kt-grid--root" id="auth_app">
			<div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v4 kt-login--signin" id="kt_login">
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" style="background-image: url({{ Module::asset(config('cms.auth_background')) }});">
					<div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">
						<div class="kt-login__container">
							<div class="kt-login__logo">
								<a href="#">
									<img src="{{ Module::asset(config('cms.auth_logo')) }}">
								</a>
							</div>
							@yield('content')
						</div>
					</div>
				</div>
			</div>
		</div>
		@include('cms::components.base.toastr')
        @include('cms::auth.includes.scripts')
        @include('cms::includes.magic_ajax_function')
		@stack('scripts')
	</body>

</html>

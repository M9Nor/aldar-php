<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}" direction="{{ LaravelLocalization::getCurrentLocaleDirection() }}" style="direction: {{ LaravelLocalization::getCurrentLocaleDirection() }};">

	<head>
		<base href="">
		<meta charset="utf-8" />
        <title>{{ __('cms::dashboard.website') }} | @yield('title')</title>
        @if(env('APP_ENV', 'development') == 'development')
            {{-- To prevent most search engine web crawlers from indexing a page on your site --}}
            <meta name="robots" content="noindex">
            {{-- To prevent only Google web crawlers from indexing a page --}}
            <meta name="googlebot" content="noindex">
        @endif
		<meta name="description" content="Updates and statistics">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="{{ Module::asset('cms:metronic/plugins/global/plugins.bundle.' . (LaravelLocalization::getCurrentLocaleDirection() == 'rtl' ? 'rtl.' : '') . 'css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ Module::asset('cms:metronic/css/style.bundle.' . (LaravelLocalization::getCurrentLocaleDirection() == 'rtl' ? 'rtl.' : '') . 'css') }}" rel="stylesheet" type="text/css" />
        @include('cms::includes.styles')
        @stack('styles')
	</head>

	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed">
		<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
			<div class="kt-header-mobile__logo">
				<a href="javascript:;">
					<img alt="Logo" width="50" src="{{ Module::asset('cms:images/header_logo.svg') }}" />
				</a>
			</div>
			<div class="kt-header-mobile__toolbar">
				<button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
				<button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>
				<button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
			</div>
		</div>
		<div class="kt-grid kt-grid--hor kt-grid--root">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
				<div class="kt-aside kt-aside--fixed kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">
					@include('cms::includes.aside')
				</div>
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
					<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed">
						@include('cms::includes.header')
					</div>
					<div class="kt-content kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
						@yield('subheader')
						<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid" id="app">
                            @yield('content')
						</div>
					</div>
					<div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
						@include('cms::includes.footer')
					</div>
				</div>
			</div>
		</div>
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>
		@include('cms::components.base.toastr')
        @include('cms::includes.scripts')
        @include('cms::includes.confirmations')
        {{-- @if(in_array('Notification', array_keys(Module::allEnabled())))
            @include('notification::firebase_scripts')
        @endif --}}
        @include('cms::includes.magic_ajax_function')
        @stack('scripts')
	</body>

</html>

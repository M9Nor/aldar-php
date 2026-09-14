<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="../../../">
		<meta charset="utf-8" />
		<title>{{ Lang::has('frontend::main.site_name', app()->getLocale()) ? __('frontend::main.site_name') : __('frontend::main.site_name', [], 'ar')}} | @yield('title')</title>
		<meta name="description" content="@yield('message')">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
		<link href="{{ Module::asset('cms:metronic/css/pages/error/error-1.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ Module::asset('cms:metronic/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ Module::asset('cms:metronic/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ Module::asset('cms:metronic/css/skins/header/base/light.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ Module::asset('cms:metronic/css/skins/header/menu/light.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ Module::asset('cms:metronic/css/skins/brand/dark.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ Module::asset('cms:metronic/css/skins/aside/dark.css') }}" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" type="image/x-icon" href="{{\Module::asset('frontend:assets/fav.svg')}}">

		<link href="{{ Module::asset('cms:fonts/tajawal/font-face.css') }}" rel="stylesheet" type="text/css" />
	</head>
	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
		<div class="kt-grid kt-grid--ver kt-grid--root">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid  kt-error-v1" style="background-image: url({{ Module::asset('cms:metronic/media/error/bg1.jpg') }});">
				<div class="kt-error-v1__container">
					{{-- <h1 class="kt-error-v1__number" style="color:#B5995A">@yield('code')</h1> --}}
					<a class="kt-error-v1__number" href="{{\Module::asset('frontend:assets/header_logo.svg')}}">
						<img width="275px" src="{{\Module::asset('frontend:assets/header_logo.svg')}}" alt="website logo">
					</a>
					<p class="kt-error-v1__desc" style="font-family: 'Tajawal', sans-serif;">
						@yield('message')
					</p>
					<p class="kt-error-v1__desc">
						<a href="{{ Lang::has('cms::global.back_to_home', app()->getLocale()) ? route('index') : LaravelLocalization::getURLFromRouteNameTranslated('ar', 'index')}}" class="btn btn-brand btn-square" style="color:#fff;background-color:#4285F4;border: 1px solid #4285F4">
							{{ Lang::has('cms::global.back_to_home', app()->getLocale()) ? __('cms::global.back_to_home') : __('cms::global.back_to_home', [], 'ar')}}
						</a>
					</p>

				</div>
			</div>
		</div>
		<script>
			var KTAppOptions = {
				"colors": {
					"state": {
						"brand": "#5d78ff",
						"dark": "#282a3c",
						"light": "#ffffff",
						"primary": "#5867dd",
						"success": "#34bfa3",
						"info": "#36a3f7",
						"warning": "#ffb822",
						"danger": "#fd3995"
					},
					"base": {
						"label": [
							"#c5cbe3",
							"#a1a8c3",
							"#3d4465",
							"#3e4466"
						],
						"shape": [
							"#f0f3ff",
							"#d9dffa",
							"#afb4d4",
							"#646c9a"
						]
					}
				}
			};
		</script>
		<script src="{{ Module::asset('cms:metronic/plugins/global/plugins.bundle.js') }}" type="text/javascript"></script>
		<script src="{{ Module::asset('cms:metronic/js/scripts.bundle.js') }}" type="text/javascript"></script>
	</body>
</html>

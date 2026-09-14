<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
@if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
    <link href="{{ Module::asset('cms:metronic/css/pages/login/login-4.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/skins/header/base/light.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/skins/header/menu/light.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/skins/brand/dark.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/skins/aside/dark.rtl.css') }}" rel="stylesheet" type="text/css" />
@else
    <link href="{{ Module::asset('cms:metronic/css/pages/login/login-4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/skins/header/base/light.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/skins/header/menu/light.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/skins/brand/dark.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('cms:metronic/css/skins/aside/dark.css') }}" rel="stylesheet" type="text/css" />
@endif
<link href="{{ Module::asset('cms:fonts/tajawal/font-face.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('cms:css/metronic_overwrites.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('cms:css/app.css') }}" rel="stylesheet" type="text/css" />
<link src="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ Module::asset('cms:css/auth.css') }}" />
<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />

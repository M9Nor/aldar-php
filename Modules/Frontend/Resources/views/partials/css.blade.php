<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="x-ua-compatible" content="ie=edge">

@if(Route::currentRouteName() == 'ListingController@getContentBySlug')
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> --}}
@endif

<link rel="shortcut icon" type="image/x-icon" href="{{\Module::asset('frontend:assets/fav.svg')}}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/jquery-ui.css') }}">
<!-- GOOGLE FONTS -->
<link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i%7CMontserrat:600,800" rel="stylesheet">
<!-- FONT AWESOME -->
<link rel="stylesheet" href="{{ Module::asset('frontend:css/fontawesome-all.min.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/font-awesome.min.css') }}">
<!-- Slider Revolution CSS Files -->
<link rel="stylesheet" href="{{ Module::asset('frontend:revolution/css/settings.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:revolution/css/layers.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:revolution/css/navigation.css') }}">
<!-- ARCHIVES CSS -->
{{-- <link rel="stylesheet" href="{{ Module::asset('frontend:css/search.css') }}"> --}}
<link rel="stylesheet" href="{{ Module::asset('frontend:css/animate.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/swiper.min.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/lightcase.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/owl-carousel.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/bootstrap.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/menu.css') }}">
<link rel="stylesheet" href="{{ Module::asset('frontend:css/slick.css') }}">
{{-- <link rel="stylesheet" href="{{ Module::asset('frontend:css/styles.css?v=1.3') }}"> --}}
<link rel="stylesheet" href="{{ Module::asset('frontend:css/styles.min.css?v=6.3') }}">
<link rel="stylesheet" id="color" href="{{ Module::asset('frontend:css/default.css') }}">

<link rel="stylesheet" href="{{ Module::asset('frontend:css/main.css?v=7.02') }}">
@if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="{{ Module::asset('frontend:css/rtl.css?v=0.2') }}">
@else
    <link rel="stylesheet" href="{{ Module::asset('frontend:css/ltr.css?v=0.3') }}">
@endif

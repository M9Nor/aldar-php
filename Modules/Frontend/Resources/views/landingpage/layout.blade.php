<!doctype html>
<html lang="en" lang="{{ $currentLang}}" dir="{{ $langDirection }}" direction="{{ $langDirection }}" style="direction: {{ $langDirection }};">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        {{-- <meta name="author" content="ThemeTags"> --}}
        
        <meta property="og:site_name" content="Aldar Real Estate"/> <!-- website name -->
        <meta property="og:site" content="https://aldar-emlak.com/"/> <!-- website link -->
        <meta property="og:title" content="{{$model->meta_title}}"/> <!-- title shown in the actual shared post -->
        <meta property="og:description" content="{{$model->meta_desc}}"/> <!-- description shown in the actual shared post -->
        <meta property="og:image" content="{{$model->translate(app()->getLocale())->getMetaImage('original')}}"/> <!-- image link, make sure it's jpg -->
        {{-- <meta property="og:url" content=""/> <!-- where do you want your post to link to --> --}}
        {{-- <meta property="og:type" content="article"/> --}}

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{$model->meta_title}}">
        <meta name="twitter:description" content="{{$model->meta_desc}}">
        <meta name="twitter:image" content="{{$model->translate(app()->getLocale())->getMetaImage('original')}}">

        <meta name="description" content="{{$model->meta_desc}}">
        <meta name="keywords" content="{{$model->meta_keywords}}">
        <meta itemprop="image" content="{{$model->translate(app()->getLocale())->getMetaImage('original')}}">
        <title>{{$model->meta_title}}</title>

        <!--favicon icon-->
        <link rel="shortcut icon" type="image/x-icon" href="{{\Module::asset('frontend:assets/fav.svg')}}">

        {{-- <!--google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700%7COpen+Sans&display=swap"
            rel="stylesheet"> --}}

        <!--Bootstrap css-->
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/bootstrap.min.css') }}">
        <!--Magnific popup css-->
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/magnific-popup.css') }}">
        <!--Themify icon css-->
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/themify-icons.css') }}">
        <!--animated css-->
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/animate.min.css') }}">
        <!--ytplayer css-->
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/jquery.mb.YTPlayer.min.css') }}">
        <!--Owl carousel css-->
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/owl.theme.default.min.css') }}">
        <!--custom css-->
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/style.css') }}">
        <!--responsive css-->
        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/responsive.css') }}">

        <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/main.css?v=3.1') }}">
        
        @if(app()->getLocale() == 'ar')
            <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/rtl.css?v=1.2') }}">
        @elseif(app()->getLocale() == 'fa')
            <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/fa.css?v=1.2') }}">
        @else
            <link rel="stylesheet" href="{{ Module::asset('frontend:landingpage/css/ltr.css?v=1.2') }}">
        @endif
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <style>
            
        </style>
    </head>
    <body>
        
        {{-- <div class="decoration-box bg-grey animated slideInDown delay-2s" style="background-color: {{$main_color}} !important"></div>
        <div class="decoration-box bg-imtilak animated slideInDown delay-2s" style="background-color: {{$second_color}} !important"></div> --}}

        <div class="main">
            @include('frontend::landingpage.main')
            @if($model->subject_text == 'yes')
                @include('frontend::landingpage.subject_text')
            @endif
            @if($model->videos == 'yes')
                @include('frontend::landingpage.videos')
            @endif
            @if($model->projects == 'yes' && isset($projects))
                @include('frontend::landingpage.projects_info')
            @endif
            @if($model->blogs == 'yes' && isset($blogs))
                @include('frontend::landingpage.blogs')
            @endif
            @if($model->services == 'yes' && isset($services))
                @include('frontend::landingpage.services')
            @endif
            
            @if($model->subject_text2 == 'yes')
                @include('frontend::landingpage.subject_text2')
            @endif
            @if($model->timeline->isNotEmpty())
                @include('frontend::landingpage.time_line')
            @endif

        </div>
        @include('frontend::landingpage.footer')
        <div class="success-message" id="success_message" style="display: none">
            <div class="success-icon-div" style="text-align: center">
                <a>
                    <i class="fa fa-check" id='success_message_icon' style="color:#d4aa45;" aria-hidden="true">
                        <span style="color:#fff" class="message-span"></span>
                    </i>
                </a>
            </div>
        </div>

        @php
            if(!empty($model->footer_whatsapp)){
                $w = 'https://api.whatsapp.com/send?phone='.$model->footer_whatsapp;
            }else{
                $w = 'javascript:;';
            }
        @endphp
        <a target="_blank" href="https://api.whatsapp.com/send?phone={{$w}}">
            <div class="whatsapp-call">
                <i class="fa fa-whatsapp" style="font-size: 30px;color: #fff;"></i>
            </div>
        </a>

        <a href="javascript:;">
            <div class="arrow-up-1">
                <i class="fa fa-arrow-up" style="font-size: 30px;color: #fff;"></i>
            </div>
        </a>

        {{-- <a target="javascript:;" class="contact-now-click">
            <div class="contact-now">
                {{__('frontend::main.footer.contact_us')}}
            </div>
        </a> --}}

        <!--jQuery-->
        <script src="{{ Module::asset('frontend:landingpage/js/jquery-3.5.0.min.js') }}">></script>
        <!--Popper js-->
        <script src="{{ Module::asset('frontend:landingpage/js/popper.min.js') }}">></script>
        <!--Bootstrap js-->
        <script src="{{ Module::asset('frontend:landingpage/js/bootstrap.min.js') }}">></script>
        <!--Magnific popup js-->
        <script src="{{ Module::asset('frontend:landingpage/js/jquery.magnific-popup.min.js') }}">></script>
        <!--jquery easing js-->
        <script src="{{ Module::asset('frontend:landingpage/js/jquery.easing.min.js') }}">></script>
        <!--jquery ytplayer js-->
        <script src="{{ Module::asset('frontend:landingpage/js/jquery.mb.YTPlayer.min.js') }}">></script>
        <!--wow js-->
        <script src="{{ Module::asset('frontend:landingpage/js/wow.min.js') }}">></script>
        <!--owl carousel js-->
        <script src="{{ Module::asset('frontend:landingpage/js/owl.carousel.min.js') }}">></script>
        <!--countdown js-->
        <script src="{{ Module::asset('frontend:landingpage/js/jquery.countdown.min.js') }}">></script>
        <!--validator js-->
        <script src="{{ Module::asset('frontend:landingpage/js/validator.min.js') }}">></script>
        <!--custom js-->
        <script src="{{ Module::asset('frontend:landingpage/js/scripts.js') }}">></script>

        <script>
            $(document).on('click','.contact-now-click',function(){
                var scroll_height = 100;
                // window.scrollTo(0,scroll_height);
                window.scrollTo({
                    top: scroll_height,
                    left: 100,
                    behavior: 'smooth'
                });
            });
            $( document ).ready(function() {
                if($(window).scrollTop() > 775)
                {
                    $('.contact-now').css('bottom','15px');
                }
                else
                {
                    $('.contact-now').css('bottom','-100px');
                }
            });
            $(window).scroll(function (event) {
                var scroll = $(window).scrollTop();
                if(scroll > 775)
                {
                    $('.contact-now').css('bottom','15px');
                }
                else
                {
                    $('.contact-now').css('bottom','-100px');
                }
            });
            $(document).on('click','.arrow-up-1',function(){
                var scroll_height = $('#form_contact').offset().top - 250;
                console.log(scroll_height);
                // window.scrollTo(0,scroll_height);
                window.scrollTo({
                    top: scroll_height,
                    left: 100,
                    behavior: 'smooth'
                });
            });
            function isValid(input_name){
                $(`input[name=${input_name}]`).css('border','1px solid #ebebeb');
                $(`textarea[name=${input_name}]`).css('border','1px solid #ebebeb');
                $(`#${input_name}`).text(' ');
            }
            function showMessage(message,status){
                console.log(status,message);
                if(status == true){
                    $('#success_message_icon').addClass("fa fa-check");
                }else{
                    $('#success_message_icon').addClass("fa fa-times");
                }
                $('.message-span').text(message);
                $('.success-message').fadeIn('show');
                setTimeout(function(){
                    $('.success-message').fadeOut('slow');
                },4000);
            }
            function submitFroms(e){
                
                e.preventDefault();
                e.stopPropagation();
                $('.save-btn').prop('disabled', true);
                $('.save-btn').css('opacity', 0.7);
                var update_text     = $('.update-text').text();

                $('.update-text').text(' ');
                $('#spinner_div').addClass('spinner');
                $('.validate-form-message').text(' ');

                var form            = $(e.target),
                    action          = form.attr('action'),
                    method          = form.attr('method'),
                    data            = new FormData(),
                    submitBtn       = form.find('[type=submit]'),
                    submitBtnHtml   = submitBtn.html();
                    submitBtn.attr('disabled', true);
                    onLoadingText = submitBtn.data('onLoadingText') ?? "@lang('frontend::main.saving')";
                    submitBtn.html(onLoadingText);

                $.each(form.serializeArray(), function (key, input) {
                    data.append(input.name, input.value);
                
                    $(`input[name=${input.name}]`).css('border','1px solid #ebebeb');
                    $(`textarea[name=${input.name}]`).css('border','1px solid #ebebeb');
                    
                });
                $.ajax({
                    data:   data,
                    url:    action,
                    type:   method,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        $('#spinner_div').removeClass('spinner');
                        $('.update-text').text(update_text);
                        $('.save-btn').prop('disabled', false);
                        $('.save-btn').css('opacity', 1);
                        if(data.success){
                            if(data.disabled){
                                $('.save-btn').prop('disabled', true);
                            }
                            showMessage(data.message,true);
                            
                        }else if(data.success == false){
                            showMessage(data.message,false);
                        }else{
                            showMessage("{!!trans('frontend::landing_page.worng_inputs')!!}",false);
                            $.each(data, function(key, value) {
                                $(`input[name=${key}]`).css('border','1px solid red');
                                $(`textarea[name=${key}]`).css('border','1px solid red !important');
                                $(`#${key}`).text(value);
                            });
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        
                    },
                    complete : function(response) {
                        
                    }
                });
                return false;
            }
        </script>

        <script>
            $(function(){
                window.sr = ScrollReveal();
                if ($(window).width() < 768) {
                    if ($('.timeline-content').hasClass('js--fadeInLeft')) {
                        $('.timeline-content').removeClass('js--fadeInLeft').addClass('js--fadeInRight');
                    }
                    sr.reveal('.js--fadeInRight', {
                        origin: 'right',
                        distance: '300px',
                        easing: 'ease-in-out',
                        duration: 800,
                    });
                }else{
                    sr.reveal('.js--fadeInLeft', {
                        origin: 'left',
                        distance: '300px',
                        easing: 'ease-in-out',
                        duration: 800,
                    });
                    sr.reveal('.js--fadeInRight', {
                        origin: 'right',
                        distance: '300px',
                        easing: 'ease-in-out',
                        duration: 800,
                    });
                }
                sr.reveal('.js--fadeInLeft', {
                    origin: 'left',
                    distance: '300px',
                        easing: 'ease-in-out',
                    duration: 800,
                });
                sr.reveal('.js--fadeInRight', {
                    origin: 'right',
                    distance: '300px',
                    easing: 'ease-in-out',
                    duration: 800,
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/scrollreveal.js/3.3.1/scrollreveal.min.js"></script>
        <script>
            var langArray = [];
            $('.vodiapicker option').each(function(){
                var img = $(this).attr("data-thumbnail");
                var text = this.innerText;
                var value = $(this).val();
                var item = '<li><img src="'+ img +'" alt="" value="'+value+'"/><span>'+ text +'</span></li>';
                langArray.push(item);
            })
            $('#a-item').html(langArray);
            //Set the button value to the first el of the array
            $('.btn-select').html(langArray[0]);
            $('.btn-select').attr('value', 'en');
            //change button stuff on click
            $('#a-item li').click(function(){
                var img = $(this).find('img').attr("src");
                var value = $(this).find('img').attr('value');
                var text = this.innerText;
                var item = '<li><img src="'+ img +'" alt="" /><span>'+ text +'</span></li>';
                $('.btn-select').html(item);
                $('.btn-select').attr('value', value);
                $(".b-item").toggle();
                //console.log(value);
                // edit
                $('.op-val').val(value);
            });
            $(".btn-select").click(function(){
                $(".b-item").toggle();
            });
            //check local storage for the lang
            var sessionLang = localStorage.getItem('lang');
            if (sessionLang){
            //find an item with value of sessionLang
                var langIndex = langArray.indexOf(sessionLang);
                $('.btn-select').html(langArray[langIndex]);
                $('.btn-select').attr('value', sessionLang);
            } else {
                var langIndex = langArray.indexOf('ch');
                console.log(langIndex);
                $('.btn-select').html(langArray[langIndex]);
                //$('.btn-select').attr('value', 'en');
            }
        </script>
    </body>
</html>
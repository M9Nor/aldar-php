<!DOCTYPE html>
<html lang="{{ $currentLang}}" dir="{{ $langDirection }}" direction="{{ $langDirection }}" style="direction: {{ $langDirection }};">

<head>
    
    @yield('pageMeta')

    @include('frontend::partials.css')

    @stack('styles')
    
</head>

    <body class="int_white_bg">
        <!-- Wrapper -->
        <div id="wrapper" class="int_main_wraapper">


            <div class="success-message" id="success_message" style="display: none">
                <div class="success-icon-div">
                    <a>
                        <i class="fa fa-check" id='success_message_icon' style="color:#d4aa45;" aria-hidden="true">
                            <span style="color:#fff" class="message-span"></span>
                        </i>
                    </a>
                </div>
            </div>

            <div class="side-fixed d-none d-lg-block" style="width: 200px">
                <div class="open">
                    <i class="fa fa-commenting"></i>
                    <ul class="ml-1">
                        <li>
                            @foreach($mainPages as $mainPage)
                                @if($mainPage->slug == 'contact-us')
                                    @php
                                        $a_link = route('ListingController@getContentBySlug', ['slug' => $mainPage->slug]);
                                    @endphp
                                @endif
                            @endforeach
                            @if(isset($a_link))
                                <a href="{{$a_link}}">
                                    <i class="fa fa-edit"></i>
                                    <span class="side-item">{{__('frontend::main.floating_button.contact_form')}}</span>
                                </a>
                            @endif
                        </li>
                        <li>
                            @php
                                $mobile_number_1 = '';
                                if(isset($contents['mobile_number_1']) && !is_null($contents['mobile_number_1']))
                                {
                                    if(!empty($contents['mobile_number_1']->translateOrFirst()->description))
                                    {
                                        $mobile_number_1 = $contents['mobile_number_1']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $mobile_number_1 = $contents['mobile_number_1']->value;
                                    }
                                }
                            @endphp
                            <a href="tel:{{isset($mobile_number_1) ? $mobile_number_1 : ''}}" target="_blank" rel="noreferrer">
                                <i class="fa fa-phone-volume"></i>
                                <span class="side-item">{{__('frontend::main.floating_button.direct_call')}}</span>
                            </a>
                        </li>
                        <li>
                            @php
                                $facebook_messenger = '';
                                if(isset($contents['facebook_messenger']) && !is_null($contents['facebook_messenger']))
                                {
                                    if(!empty($contents['facebook_messenger']->translateOrFirst()->description))
                                    {
                                        $facebook_messenger = $contents['facebook_messenger']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $facebook_messenger = $contents['facebook_messenger']->value;
                                    }
                                }
                            @endphp
                            <a href="{{$facebook_messenger}}" target="_blank" rel="noreferrer">
                                <i class="fa fa-facebook"></i>
                                <span class="side-item">{{__('frontend::main.floating_button.facebook_messenger')}}</span>
                            </a>
                        </li>
                        <li>
                            @if(isset($contents['whatsapp']))
                                @php
                                    if(!empty($contents['whatsapp']->translateOrFirst()->description))
                                    {
                                        $whatsapp = $contents['whatsapp']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $whatsapp = $contents['whatsapp']->val;
                                    }
                                @endphp
                            @endif
                            <a href="{{isset($whatsapp) ? $whatsapp : ''}}" target="_blank" rel="noreferrer">
                                <i class="fa fa-whatsapp"></i>
                                <span class="side-item">{{__('frontend::main.floating_button.whatsapp')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            @include('frontend::layouts.header')
            
            @yield('content')

            @include('frontend::layouts.footer')

            @if(Cookie::get('cookies') != 'yes')
                @include('frontend::includes.cookies')
            @endif
            
            <a data-scroll href="#wrapper" class="go-up"><i class="fa fa-angle-double-up" aria-hidden="true"></i></a>
            <!-- END FOOTER -->

            {{-- <!-- START PRELOADER -->
            <div id="preloader">
                <div id="status">
                    <div class="status-mes"></div>
                </div>
            </div>
            <!-- END PRELOADER --> --}}

        </div>
        <!-- Wrapper / End -->
    </body>

    @include('frontend::partials.scripts')
    @include('frontend::partials.custom_scripts')

    @stack('scripts')
    
</html>

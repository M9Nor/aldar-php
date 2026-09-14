<header id="header-container">
    <div class="header-top">
        <div class="custom-container">
            <div class="top-info hidden-sm-down">
                @if(isset($contents['mobile_number_1']))
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
                    <div class="call-header">
                        <a href="tel:{{ $mobile_number_1 }}"><i class="fa fa-phone" aria-hidden="true"></i>&nbsp;&nbsp;<span dir="ltr">{{ $mobile_number_1 }}</span></a>
                    </div>
                @endif
                @if(isset($contents['email']))
                    <div class="mail-header">
                        <a href="mailto:{{ $contents['email']->value }}"><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;&nbsp;<span dir="ltr">{{ $contents['email']->value }}</span></a>
                    </div>
                @endif
                {{-- <div class="header-search hidden-sm-down">
                    <form class="nav-search box-border" method="get" action="{{route('ListingController@search')}}">
                        <i class="fa fa-search"></i>
                        <input style="{{app()->getLocale() == 'en' ? 'direction:ltr' : 'direction:rtl'}}" type="text" class="form-control form-control-sm" name="q" aria-label="{{__('frontend::main.header.search')}}" value="{{isset($params['q']) ?$params['q'] : ''}}" placeholder="{{__('frontend::main.header.search')}}" autocomplete="off">
                    </form>
                </div> --}}
            </div>
            <div class="top-social hidden-sm-down">
                <div class="social-icons-header">
                    <div class="social-icons">
                        @if(isset($contents['youtube']))
                            @php
                                $youtube = '';
                                if(isset($contents['youtube']) && !is_null($contents['youtube']))
                                {
                                    if(!empty($contents['youtube']->translateOrFirst()->description))
                                    {
                                        $youtube = $contents['youtube']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $youtube = $contents['youtube']->value;
                                    }
                                }
                            @endphp
                            <a href="{{ $youtube }}"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($contents['facebook']))
                            @php
                                $facebook = '';
                                if(isset($contents['facebook']) && !is_null($contents['facebook']))
                                {
                                    if(!empty($contents['facebook']->translateOrFirst()->description))
                                    {
                                        $facebook = $contents['facebook']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $facebook = $contents['facebook']->value;
                                    }
                                }
                            @endphp
                            <a href="{{ $facebook }}"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($contents['instagram']))
                            @php
                                $instagram = '';
                                if(isset($contents['instagram']) && !is_null($contents['instagram']))
                                {
                                    if(!empty($contents['instagram']->translateOrFirst()->description))
                                    {
                                        $instagram = $contents['instagram']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $instagram = $contents['instagram']->value;
                                    }
                                }
                            @endphp
                            <a href="{{ $instagram }}"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($contents['twitter']))
                            @php
                                $twitter = '';
                                if(isset($contents['twitter']) && !is_null($contents['twitter']))
                                {
                                    if(!empty($contents['twitter']->translateOrFirst()->description))
                                    {
                                        $twitter = $contents['twitter']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $twitter = $contents['twitter']->value;
                                    }
                                }
                            @endphp
                            <a href="{{ $twitter }}"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($contents['linkedin']))
                            @php
                                $linkedin = '';
                                if(isset($contents['linkedin']) && !is_null($contents['linkedin']))
                                {
                                    if(!empty($contents['linkedin']->translateOrFirst()->description))
                                    {
                                        $linkedin = $contents['linkedin']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $linkedin = $contents['linkedin']->value;
                                    }
                                }
                            @endphp
                            <a href="{{ $linkedin }}"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($contents['telegram']))
                            @php
                                $telegram = '';
                                if(isset($contents['telegram']) && !is_null($contents['telegram']))
                                {
                                    if(!empty($contents['telegram']->translateOrFirst()->description))
                                    {
                                        $telegram = $contents['telegram']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $telegram = $contents['telegram']->value;
                                    }
                                }
                            @endphp
                            <a href="{{ $telegram }}"><i class="fab fa-telegram-plane" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>
                <div class="dropdown">
                    @php
                        $defaultLangImage = route('image', ['size' => '30x30', 'path' => 'defaults/base.png']);
                    @endphp
                    <button class="btn-dropdown dropdown-toggle" type="button" id="dropdownlang" data-toggle="dropdown" aria-haspopup="true">
                        <img class="lazy"  src="{{ route('image', ['size' => '30x30', 'path' => 'flags/' . $currentLang . '.jpg']) }}" alt="{{ $currentLangNative }}" width="18px" data-src="{{ route('image', ['size' => '30x30', 'path' => 'flags/' . $currentLang . '.jpg']) }}" alt="{{ $currentLangNative }}"> {{ $currentLangNative }}
                    </button>

                    <ul class="dropdown-menu" aria-labelledby="dropdownlang">
                        @foreach($supportedLangs as $locale => $properties)
                            <li class="text-{{ $langDirection == 'rtl' ? 'right' : 'left' }}">
                                <a href="{{ LaravelLocalization::getLocalizedURL($locale, request()->fullUrl(), [], true) }}">
                                    <img class="lazy" src="{{ route('image', ['size' => '30x30', 'path' => 'flags/' . $locale . '.jpg']) }}" alt="{{ $properties['native'] }}" width="18px" data-src="{{ route('image', ['size' => '30x30', 'path' => 'flags/' . $locale . '.jpg']) }}" alt="{{ $properties['native'] }}"> {{ $properties['native'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div id="header" class="int_content_wraapper">
        <div class="custom-container">
            <div class="left-side">
                <div id="logo" class="col-lg-2 logo-white p-0">
                    <a href="{{ route('index') }}"><img src="{{\Module::asset('frontend:assets/header_logo.svg')}}" alt="{{__('frontend::main.site_name')}}"></a>
                </div>
                <div class="mmenu-trigger">
                    <button class="hamburger hamburger--collapse" type="button">
                        <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
                <nav id="navigation" class="style-1 white">
                    <ul id="responsive">
                        <li><a href="{{ route('index') }}">{{__('frontend::main.home')}}</a></li>
                        @foreach($headerMenuItems as $menuItemGroup)
                            @if($menuItemGroup->slug == 'buy-properties')
                                <li>
                                    <a href="{{route('ListingController@filter', ['property_classification' => 'apartments', 'contract' => 'for-sale', 'city' => 'istanbul'])}}">
                                        {{$menuItemGroup->translateOrFirst()->title}}
                                    </a>
                                    <ul>
                                        @foreach ($menuItemGroup->contents as $menuItem)
                                            <li>
                                                <a href="{{$menuItem->translateOrFirst()->link_trans}}">{{$menuItem->translateOrFirst()->title}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach
                                <li>
                                    <a href="#">
                                        {{__('frontend::main.opportunity')}}
                                    </a>
                                    <ul>
                                        @foreach ($headerMenuItemsOpportunity as $menuOppItem)
                                            <li>
                                                <a href="{{route('OpportunityController@filter', ['opportunity_classification' => $menuOppItem->slug, 'contract' => 'for-sale', 'city' => 'turkey'])}}">{{$menuOppItem->translateOrFirst()->title}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                        {{-- <li><a href="javascript:;">{{__('frontend::main.real_estates')}}</a></li> --}}
                        <li><a href="{{ route('ListingController@services') }}">{{__('frontend::main.services')}}</a></li>
                        <li><a href="{{ route('ListingController@articles') }}">{{__('frontend::main.articles')}}</a></li>
                        @foreach($mainPages->whereIn('slug',['who-we-are']) as $mainPage)
                            <li><a href="{{route('ListingController@getContentBySlug', ['slug' => $mainPage->slug])}}">{{$mainPage->translateOrFirst()->title}}</a></li>
                        @endforeach
                        @foreach($mainPages->whereIn('slug',['contact-us']) as $mainPage)
                            <li><a href="{{route('ListingController@getContentBySlug', ['slug' => $mainPage->slug])}}">{{$mainPage->translateOrFirst()->title}}</a></li>
                        @endforeach
                        
                    </ul>
                </nav>
                <div class="clearfix"></div>
            </div>
            <div class="right-side hidden-lg-down">
                <!-- Header Widget -->
                <div class="header-widget">
                    <div class="dropdown dropdown-currencies hidden-sm-down">
                        <form action="{{ route('FrontendController@setCurrency') }}" id="currency-form" method="post" enctype="multipart/form-data">
                            @csrf
                            <input name="currency" type="hidden">
                            @php
                                $defaultLangImage = route('image', ['size' => '30x30', 'path' => 'defaults/base.png']);
                            @endphp
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary search-btn" data-toggle="modal" data-target="#exampleModalLong">
                                <i class="fa fa-search"></i>
                            </button>
                            <button class="btn-dropdown dropdown-toggle" type="button" id="dropdownlang" data-toggle="dropdown" aria-haspopup="true">
                                {{-- <img class="lazy"  src="{{ $selectedCurrency->getIconImage($selectedCurrency, '150x150') }}" width="18px" data-src="{{ $selectedCurrency->getIconImage($selectedCurrency, '150x150') }}" alt="{{ $selectedCurrency->currency_symbol }}">  --}}
                                {{ $selectedCurrency->currency_symbol }}
                            </button>
                            
                            <ul class="dropdown-menu" aria-labelledby="dropdownlang">
                                @foreach($currencies as $locale => $currency)
                                    <li data-name="{{$currency->currency_code}}" class="change-currency text-{{ $langDirection == 'rtl' ? 'right' : 'left' }}" style="color: #000">
                                        <img style="border-radius: 10px" class="lazy" src="{{ $defaultLangImage }}" alt="{{ $currency->currency_code }}" width="25px" data-src="{{ $currency->getIconImage($currency,'150x150') }}" alt="{{ $currency->currency_code }}">
                                        {{ $currency->currency_code }}
                                    </li>
                                @endforeach
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</header>
<div class="clearfix"></div>
  <!-- Modal -->
    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="nav-search box-border" method="get" action="{{route('ListingController@search')}}">
                    <div class="modal-body">
                        <h5 class="modal-title" style="text-align: center">{{__('frontend::main.header.search')}}</h5>
                        <div class="header-search hidden-sm-down">
                            <i class="fa fa-search"></i>
                            <input style="{{app()->getLocale() == 'en' ? 'direction:ltr' : 'direction:rtl'}}" type="text" class="form-control form-control-sm" name="q" aria-label="{{__('frontend::main.header.search')}}" value="{{isset($params['q']) ?$params['q'] : ''}}" placeholder="{{__('frontend::main.header.search')}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                        <button style="width: 100%" type="submit" class="btn btn-primary">{{__('frontend::main.header.search')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
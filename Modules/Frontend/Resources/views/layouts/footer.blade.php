<!-- START FOOTER -->
<footer class="first-footer">
    <div class="top-footer">
        <div class="custom-container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="netabout">
                        <a href="{{ route('index') }}" class="logo">
                            <img src="{{\Module::asset('frontend:assets/footer_logo.svg')}}" alt="{{__('frontend::main.site_name')}}">
                        </a>
                        @if(isset($contents['footer_about_company']))
                            @php
                                if(!empty($contents['footer_about_company']->translateOrFirst()->description))
                                {
                                    $footer_about_company = $contents['footer_about_company']->translateOrFirst()->description;
                                }
                                else
                                {
                                    $footer_about_company = $contents['footer_about_company']->val;
                                }
                            @endphp
                            <p>{{$footer_about_company}}</p>
                        @endif
                    </div>
                    <div class="social-icons-header footer-social">
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
                    {{-- <div class="contactus">
                        <ul>
                            <li>
                                <div class="info">
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                                    <p class="in-p">95 South Park Avenue, USA</p>
                                </div>
                            </li>
                            <li>
                                <div class="info">
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    <p class="in-p">+456 875 369 208</p>
                                </div>
                            </li>
                            <li>
                                <div class="info">
                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                    <p class="in-p ti">support@findhouses.com</p>
                                </div>
                            </li>
                        </ul>
                    </div> --}}
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="navigation">
                        <h3>{{__("frontend::main.important_links")}}</h3>
                        <div class="nav-footer">
                            @foreach($footerMenuItems as $menuItemGroup)
                                <ul>
                                    @foreach($menuItemGroup->contents->take(10) as $menuItem)
                                        <li><a href="{{route('ListingController@getContentBySlug', ['slug' => $menuItem->slug])}}">{{$menuItem->translateOrFirst()->title}}</a></li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="navigation">
                        <h3>{{__("frontend::main.pages")}}</h3>
                        <div class="nav-footer">
                            <ul>
                                @foreach($mainPages->whereIn('slug',['who-we-are','call-us','contact-us']) as $mainPage)
                                    <li><a href="{{route('ListingController@getContentBySlug', ['slug' => $mainPage->slug])}}">{{$mainPage->translateOrFirst()->title}}</a></li>
                                @endforeach
                                <li>
                                    <a href="{{route('ListingController@faqs')}}">
                                        {{__("frontend::main.faqs")}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="newsletters">
                        <h3>{{__("frontend::main.newsletters")}}</h3>
                        <p>{{__("frontend::main.newsletters_desc")}}</p>
                    </div>
                    <form class="bloq-email" onsubmit="submitFromsEmails(event);" method="POST" action="{{route('ContactController@subscribe')}}">
                        @include('frontend::partials.honeypot', ['id' => 'footer'])
                        @csrf
                        <label for="subscribeEmail" class="error"></label>
                        <div class="email">
                            <input onchange="isValid('emails')" type="email" name="emails" placeholder="{{__("frontend::main.enter_your_email")}}">
                            <p class="validate-form-message" id="emails" style="width: 100%"> &nbsp; &nbsp;</p>
                            <button type="submit" class="btn btn-primary btn-lg save-btn-emails">
                                <div id="spinner_div_emails" class="">
                                    <div class="update-text-emails" >
                                        {{__('frontend::main.contact_form.send')}}
                                    </div>
                                </div>
                            </button>
                            {{-- <input type="submit" value="{{__("frontend::main.subscribe")}}"> --}}
                            {{-- <p class="subscription-success"></p> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="second-footer">
        <div class="custom-container">
            <div class="bottom-footer">
                @if(app()->getLocale() == 'ar')
                    <p>جميع الحقوق محفوظة لشركة الدار العقارية © 2021  تم التطوير بواسطة: <a href="https://namaa-solutions.com/ar" style="color: #0BBC77">نماء للحلول البرمجية</a></p>
                @else
                    <p>All rights reserved for ALDAR REAL ESTATE © Powered By <a href="https://namaa-solutions.com/" style="color: #0BBC77">Namaa Solutions</a></p>
                @endif

            </div>
        </div>
    </div>
</footer>
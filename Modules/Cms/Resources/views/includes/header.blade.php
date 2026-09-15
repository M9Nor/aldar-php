@include('cms::includes.header_menu')
<div class="kt-header__topbar ml-auto">
    {{-- in_array('Notification', array_keys(Module::allEnabled())) --}}
    @if(false)
        <div class="kt-header__topbar-item dropdown" id="notifications_application">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px" aria-expanded="true">
                <span class="kt-header__topbar-icon kt-pulse kt-pulse--warning" v-on:click="getNotifications(1)">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewbox="0 0 24 24"
                        version="1.1" class="kt-svg-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect id="bound" x="0" y="0" width="24" height="24" />
                            <path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z"
                                id="Combined-Shape" fill="#000000" opacity="0.3" />
                            <path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z"
                                id="Combined-Shape" fill="#000000" />
                        </g>
                    </svg>
                    <span class="kt-pulse__ring"></span>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
                <form>
                    <!--begin: Head -->
                    <div class="kt-head kt-head--skin-dark kt-head--fit-x kt-head--fit-b" style="background-color: #1e1e2d">
                        <h3 class="kt-head__title">
                            {{ __('notification::strings.notifications') }} &nbsp;
                            {{-- <span class="btn btn-success btn-sm btn-bold btn-font-md">23 new</span> --}}
                            <span class="btn btn-label-warning btn-bold btn-sm btn-icon-h btn-font-md">@{{ notifications.new_count }} {{ __('notification::strings.new_notifications') }}</span>
                        </h3>

                        <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success kt-notification-item-padding-x" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show" data-toggle="tab" href="#topbar_notifications_notifications" role="tab" aria-selected="true">{{ __('notification::strings.alerts') }}</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#topbar_notifications_events" role="tab" aria-selected="false">{{ __('admin::strings.events') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#topbar_notifications_logs" role="tab" aria-selected="false">{{ __('admin::strings.logs') }}</a>
                            </li> --}}
                        </ul>
                    </div>
                    <!--end: Head -->

                    <div class="tab-content">
                        <div class="tab-pane active show" id="topbar_notifications_notifications" role="tabpanel">
                            <div class="kt-grid kt-grid--ver" style="min-height: 200px;" v-if="is_loading">
                                <div class="kt-grid kt-grid--hor kt-grid__item kt-grid__item--fluid kt-grid__item--middle">
                                    <div class="kt-grid__item kt-grid__item--middle kt-align-center" id="notifications_container">
                                    </div>
                                </div>
                            </div>
                            <div class="kt-grid kt-grid--ver" style="min-height: 200px;" v-if="notifications.data.length == 0 && !is_loading">
                                <div class="kt-grid kt-grid--hor kt-grid__item kt-grid__item--fluid kt-grid__item--middle">
                                    <div class="kt-grid__item kt-grid__item--middle kt-align-center">
                                        {{ __('notification::strings.all_caught_up') }}
                                        <br>{{ __('notification::strings.no_new_notifications') }}
                                    </div>
                                </div>
                            </div>
                            <div class="kt-notification kt-scroll" data-scroll="true" data-height="300" data-mobile-height="200" v-if="notifications.data.length > 0 && !is_loading">
                                <a :href="notification.click_action || 'javascripts:;'" class="kt-notification__item" v-for="(notification, notification_index) in notifications.data">
                                    <div class="kt-notification__item-icon">
                                        {{-- <i class="flaticon2-line-chart kt-font-success"></i> --}}
                                        <img style="height: 30px;" :src="notification.icon" alt="">
                                    </div>
                                    <div class="kt-notification__item-details" style="padding-left: 10px; padding-right: 10px;">
                                        <div class="kt-notification__item-title">
                                            @{{ notification.title }}
                                            <br><small>@{{ notification.body }}</small>
                                        </div>
                                        <div class="kt-notification__item-time">
                                            <small>@{{ notification.date }}</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="tab-pane" id="topbar_notifications_events" role="tabpanel">
                            <div class="kt-grid kt-grid--ver" style="min-height: 200px;">
                                <div class="kt-grid kt-grid--hor kt-grid__item kt-grid__item--fluid kt-grid__item--middle">
                                    <div class="kt-grid__item kt-grid__item--middle kt-align-center">
                                        {{ __('notification::strings.all_caught_up') }}
                                        <br>{{ __('notification::strings.no_new_notifications') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="topbar_notifications_logs" role="tabpanel">
                            <div class="kt-grid kt-grid--ver" style="min-height: 200px;">
                                <div class="kt-grid kt-grid--hor kt-grid__item kt-grid__item--fluid kt-grid__item--middle">
                                    <div class="kt-grid__item kt-grid__item--middle kt-align-center">
                                        {{ __('notification::strings.all_caught_up') }}
                                        <br>{{ __('notification::strings.no_new_notifications') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
    @if(count($supportedLangs) > 1)
        <div class="kt-header__topbar-item kt-header__topbar-item--langs">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
                <span class="kt-header__topbar-icon">
                    <img class="" src="{{ route('image', ['size' => '40x40', 'path' => 'flags/' . $currentLang . '.jpg']) }}" alt="" />
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround">
                <ul class="kt-nav kt-margin-t-10 kt-margin-b-10">
                    @foreach($supportedLangs as $locale => $properties)
                        @if($locale == 'ar')
                            <li class="kt-nav__item {!! ( $locale == $currentLang ) ? 'kt-nav__item--active' : '' !!}">
                                <a hreflang="{{ $locale }}" href="{{ LaravelLocalization::getLocalizedURL($locale, request()->fullUrl(), [], true) }}" class="kt-nav__link">
                                    <span class="kt-nav__link-icon"><img src="{{ route('image', ['size' => '40x40', 'path' => 'flags/' . $currentLang . '.jpg']) }}" alt="" /></span>
                                    <span class="kt-nav__link-text">{{ $properties['native'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    <div class="kt-header__topbar-item kt-header__topbar-item--user">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
            <div class="kt-header__topbar-user">
                <span class="kt-header__topbar-welcome kt-hidden-mobile">{{ __('cms::global.hi') }},</span>
                <span class="kt-header__topbar-username kt-hidden-mobile">{{ auth()->user()->full_name }}</span>
                <img class="{{-- kt-hidden --}}" alt="Pic" src="{{ auth()->user()->getImage('75x75') }}" />

                {{-- <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">{{ auth()->user()->name[0] }}</span> --}}
            </div>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url({{ Module::asset('cms:metronic/media/misc/bg-1.jpg') }})">
                <div class="kt-user-card__avatar">
                    <img class="{{-- kt-hidden --}}" alt="Pic" src="{{ auth()->user()->getImage('75x75') }}" />
                    {{-- <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">{{ auth()->user()->name[0] }}</span> --}}
                </div>
                <div class="kt-user-card__name">
                    {{ auth()->user()->full_name }}
                    <br>
                    @foreach(auth()->user()->toArray()['roles_array'] as $role)
                        <span class="kt-font--bolder kt-badge kt-badge--inline" style="color: #fff; background: {{ $role['color'] }};">{{ $role['title'] }}</span>
                    @endforeach
                    {{-- <small><a href="mailto:{{ auth()->user()->email }}" class="kt-link">{{ auth()->user()->email }}</a></small> --}}
                </div>
            </div>


            <div class="kt-notification">
            <a href="{{route('UserController@myprofile')}}" class="kt-notification__item">
                    <div class="kt-notification__item-icon">
                        <i class="flaticon2-calendar-3 kt-font-success"></i>
                    </div>
                    <div class="kt-notification__item-details">
                        <div class="kt-notification__item-title kt-font-bold">
                            {{ __('cms::header.my-profile.title') }}
                        </div>
                        <div class="kt-notification__item-time">
                            {{ __('cms::header.my-profile.description') }}
                        </div>
                    </div>
                </a>
                <div class="kt-notification__custom kt-space-between">
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="javascript:;" onclick="getElementById('logoutForm').submit();" class="btn btn-label btn-label-brand btn-sm btn-bold">{{ __('cms::header.sign-out') }}</a>

                    @if(session()->get('temporaryLoginUser'))
                        <form id="loginBackForm" action="{{ route('UserController@loginAs', ['model' => auth()->user()->id]) }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="javascript:;" onclick="getElementById('loginBackForm').submit();" class="btn btn-clean btn-sm btn-bold">{{ __('cms::header.login_back_as_root') }}</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

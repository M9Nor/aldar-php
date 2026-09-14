<div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
    <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile {{-- kt-header-menu--layout-tab --}} kt-header-menu--layout-default">
        <ul class="kt-menu__nav ">
            @foreach(app()->make('Cms')->headerMenu->sortBy('ordering') as $menuItem)
                <li class="kt-menu__item kt-menu__item--rel {!! ($menuItem['is_active'] && count($menuItem['items']) > 1) ? 'kt-menu__item--open-dropdown' : '' !!} {!! ($menuItem['is_active']) ? ' kt-menu__item--active' : '' !!}{!! (count($menuItem['items']) > 1) ? ' kt-menu__item--submenu' : '' !!}" aria-haspopup="true"{!! (count($menuItem['items']) > 1) ? '  data-ktmenu-submenu-toggle="click" aria-haspopup="true"' : '' !!}>
                    @php
                        if(count($menuItem['items']) == 1)
                        {
                            $menuItem['label'] = $menuItem['items'][0]['label'];
                            $menuItem['link'] = $menuItem['items'][0]['link'];
                        }
                    @endphp
                    <a href="{!! count($menuItem['items']) > 1 ? 'javascript:;' : $menuItem['link'] !!}" class="kt-menu__link {{ count($menuItem['items']) > 1 ? 'kt-menu__toggle' : '' }}">
                        <span class="kt-menu__link-text">
                            {{ $menuItem['label'] }}
                        </span>
                        @if(count($menuItem['items']) > 1)
                            <i class="kt-menu__hor-arrow la la-angle-down"></i>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        @endif
                    </a>
                    @if(count($menuItem['items']) > 1)
                        <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left kt-menu__custom-width">
                            <ul class="kt-menu__subnav">
                                @foreach($menuItem['items'] as $sub_key => $menuSubItem)
                                    <li class="kt-menu__item">
                                        <a href="{!! $menuSubItem['link'] !!}" class="kt-menu__link {{ ($menuSubItem['items']) ? 'kt-menu__toggle' : '' }}">
                                            @if(isset($menuSubItem['icon']))
                                                <span class="kt-menu__link-icon">
                                                    <i class="kt-nav__link-icon {{ $menuSubItem['icon'] }}"></i>
                                                </span>
                                            @endif
                                            <span class="kt-menu__link-text">{{ $menuSubItem['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

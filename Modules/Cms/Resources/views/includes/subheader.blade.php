<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container kt-container--fluid">
        @php
            $opt = array_merge([
                'title' => __('cms::app.cms_meta.title'),
                'items' => []
            ], (isset($options) ? $options : []));
        @endphp
        <div class="kt-subheader__main">
            <h2 class="kt-subheader__title text-danger">
                {!! $opt['title'] !!}
            </h2>
            <span class="kt-subheader__separator kt-hidden"></span>
            <div class="kt-subheader__breadcrumbs">
                <a href="{!! route('DashboardController@index') !!}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                @foreach($opt['items'] as $key => $value)
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="{!! $value['link'] !!}" class="kt-subheader__breadcrumbs-link {{ ($value['link'] == request()->url() || $value['link'] == 'javascript:;') ? 'kt-subheader__breadcrumbs-link--active' : '' }}">
                        {!! $value['label'] !!}
                    </a>
                @endforeach
            </div>
            @if(isset($main))
                {!! $main !!}
            @endif
        </div>
        <div class="kt-subheader__toolbar">
            <div class="kt-subheader__wrapper">
                @if(isset($toolbar))
                    {!! $toolbar !!}
                @endif
            </div>
        </div>
    </div>
</div>

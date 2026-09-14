<div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded m-portlet--unair m-portlet--head-sm {!! ! isset($portletClass) ? '' : ' ' . $portletClass  !!}">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                @if(isset($icon))
                    <div class="m-portlet__head-icon">
                        {!! $icon !!}
                    </div>
                @endif
                <h3 class="m-portlet__head-text">
                    {!! $title !!}
                </h3>
            </div>
        </div>
        @if( isset( $tools ) )
            {!! $tools !!}
        @endif
    </div>
    <div class="m-portlet__body">
        {!! $slot !!}
    </div>
    @if(isset($footer))
        <div class="m-portlet__foot m-portlet__foot--fit">
            {!! $footer !!}
        </div>
    @endif
</div>


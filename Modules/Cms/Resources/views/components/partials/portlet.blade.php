<div class="kt-portlet kt-portlet--mobile @if(isset($portletClass)) {!! $portletClass !!}  @endif">
    @if(isset($icon) || isset($title) || isset($tools))
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            @if(isset($icon))
                <span class="kt-portlet__head-icon">
                    {!! $icon !!}
                </span>
            @endif
            @if(isset($icon))
                <h3 class="kt-portlet__head-title">
                    {!! $title !!}
                </h3>
            @endif
        </div>
        @if(isset($tools))
            {!! $tools !!}
        @endif
    </div>
    @endif
    <div class="kt-portlet__body @if(isset($bodyClass)) {!! $bodyClass !!}  @endif">
        {!! $slot !!}
    </div>
    @if(isset($footer))
        <div class="kt-portlet__foot kt-portlet__foot--fit">
            {!! $footer !!}
        </div>
    @endif
</div>
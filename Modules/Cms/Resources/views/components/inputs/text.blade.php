<?php
    use Illuminate\Support\Str;
?>

@if(isset($options))
    @php

        $opt = array_merge([
            'id'                    => null,
            'type'                  => 'text',
            'name'                  => 'text',
            'label'                 => 'Text Input',
            'placeholder'           => null,
            'help'                  => null,
            'error_bag'             => null,
            'input_class'           => '',
            'value'                 => null,
            'disabled'              => false,
            'readonly'              => false,
            'required'              => false,
            'icon'                  => false,
            'step'                  => '1',
            /**
             * Format: 3:9. The number before ':' represents the number of columns the label will take, while the number after represents
             * the number of columns the input field will take.
             */
            'inline'                => false,
            // If set to true and the input type is password, the password can be shown if desired.
            'showable'              => false,
            'direction'             => 'rtl',
            // Adds an indicator to show if the max length of characters has been reached.
            'maxlength'             => false,
            // Removes valid/invalid status from inputs on input/change events.
            'status_removal'        => true
        ], $options);

        if(! $opt['id']) $opt['id'] = $opt['name'];

        $errorName = str_replace('][', '.', $opt['name']);
        $errorName = str_replace('[', '.', $errorName);
        $errorName = str_replace(']', '', $errorName);

        $opt['error_bag'] = (! $opt['error_bag']) ? $errors : $errors->{$opt['error_bag']};
    @endphp

    <div class="form-group kt-form__group {{ $opt['inline'] ? 'row' : '' }}">

        @if($opt['label'])
            <label for="{{ $opt['id'] }}" class="{{ $opt['inline'] ? 'col-form-label col-lg-'.Str::before($opt['inline'], ':') : '' }}">
                <strong class="text-focus">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif

        <div class="{{ $opt['inline'] ? 'col-lg-'.Str::after($opt['inline'], ':') : '' }}">
            <div class="input-group {{ $opt['icon'] ? 'kt-input-icon kt-input-icon--right' : '' }} {{ $opt['input_class'] }}">

                @if($opt['type'] == 'password' && $opt['showable'])
                    <div class="input-group-prepend">
                        <button class="btn btn-secondary btn-icon toggle-password" type="button">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                @endif
                @php
                    if($opt['value'] === 0){
                        $val = 0;
                    }elseif($opt['value'] === null){
                        $val = '';
                    }else{
                        $val = $opt['value'];
                    }
                @endphp
                <input
                    name="{{ $opt['name'] }}"
                    type="{{ $opt['type'] }}"
                    class="form-control kt-input {{ !$opt['status_removal'] ? 'disable-status-removal' : '' }}" {{-- text-left --}}
                    id="{{ $opt['id'] }}"
                    step="{{ $opt['step'] }}"
                    placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}"
                    value="{{ $val }}"
                    {{-- dir="{{ $opt['direction'] }}" --}}
                    dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
                    maxlength="{{ ($opt['maxlength']) ? $opt['maxlength'] : '' }}"
                    {!! $opt['disabled'] ? 'disabled' : '' !!}
                    {!! $opt['readonly'] ? 'readonly' : '' !!}
                    {{ $opt['type'] == 'password' ? 'autocomplete=new-password' : '' }}>

                @if($opt['icon'])
                    <span class="kt-input-icon__icon kt-input-icon__icon--right">
                        <span><i class="{{ $opt['icon'] }}"></i></span>
                    </span>
                @endif

            </div>

            @if($opt['error_bag']->has($errorName))
                <div class="form-control-feedback">
                    {{ $opt['error_bag']->first( $errorName ) }}
                </div>
            @endif

            @if($opt['help'])
                <span class="form-text text-muted">{{ $opt['help'] }}</span>
            @endif
        </div>
    </div>

    @push('scripts')
        @if($opt['type'] == 'password')
            <script>
                $(".toggle-password")
                .on('mousedown', function() {
                    $(this).children('i').removeClass("fa-eye-slash").addClass("fa-eye");
                    $(this).parent('div').siblings('input').attr("type", "text");
                })
                .on('mouseup mouseleave', function() {
                    $(this).children('i').removeClass("fa-eye").addClass("fa-eye-slash");
                    $(this).parent('div').siblings('input').attr("type", "password");
                });
            </script>
        @endif

        @if($opt['maxlength'])
            <script>
                $("#{{ $opt['id'] }}").maxlength({
                    placement: "bottom-left",
                    warningClass: "kt-badge kt-badge--brand kt-badge--rounded kt-badge--inline",
                    limitReachedClass: "kt-badge kt-badge--brand kt-badge--rounded kt-badge--inline"
                });
            </script>
        @endif
    @endpush
@endif

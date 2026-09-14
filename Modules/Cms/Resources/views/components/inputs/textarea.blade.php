<?php
    use Illuminate\Support\Str;
?>

@if(isset($options))
    @php

        $opt = array_merge([
            'id'                    => null,
            'name'                  => 'textarea',
            'label'                 => 'Textarea Input',
            'placeholder'           => null,
            'help'                  => null,
            'error_bag'             => null,
            'input_class'           => 'kt-input-group',
            'value'                 => null,
            'disabled'              => false,
            'readonly'              => false,
            'required'              => false,
            'rows'                  => 3,
            /**
             * Format: 3:9. The number before ':' represents the number of columns the label will take, while the number after represents
             * the number of columns the input field will take.
             */
            'inline'                => false,
            // If set to true and the input type is password, the password can be shown if desired.
            'direction'             => 'rtl',
        ], $options);

        if(! $opt['id']) $opt['id'] = $opt['name'];

        $errorName = str_replace('][', '.', $opt['name']);
        $errorName = str_replace('[', '.', $errorName);
        $errorName = str_replace(']', '', $errorName);

        $opt['error_bag'] = (! $opt['error_bag']) ? $errors : $errors->{$opt['error_bag']};
    @endphp
    <div class="form-group kt-form__group {{ $opt['inline'] ? 'row' : '' }} {{ $opt['error_bag']->has( $opt['name'] ) ? 'has-danger' : '' }}">

        @if($opt['label'])
            <label for="{{ $opt['id'] }}" class="{{ $opt['inline'] ? 'col-form-label col-lg-'.Str::before($opt['inline'], ':') : '' }}">
                <strong class="text-focus">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif

        <div class="{{ $opt['inline'] ? 'col-lg-'.Str::after($opt['inline'], ':') : '' }}">
            <div class="input-group {{ $opt['input_class'] }}">
                <textarea
                    name="{{ $opt['name'] }}"
                    rows="{{ $opt['rows'] }}"
                    class="form-control kt-input {{ $opt['input_class'] }} " {{-- text-left --}}
                    id="{{ $opt['id'] }}"
                    placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}"
                    {{-- dir="{{ $opt['direction'] }}" --}}
                    dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
                    {!! $opt['disabled'] ? 'disabled' : '' !!}
                    {!! $opt['readonly'] ? 'readonly=""' : '' !!}>{!! ($opt['value']) ? $opt['value'] : '' !!}</textarea>

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
@endif

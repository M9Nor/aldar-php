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
            /**
             * Format: 3:9. The number before ':' represents the number of columns the label will take, while the number after represents
             * the number of columns the input field will take.
             */
            'inline'                => false,
            // If set to true and the input type is password, the password can be shown if desired.
            'showable'              => false,
            'direction'             => 'rtl',
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
            
                <div class="custom-file">
                    <input
                        name="{{ $opt['name'] }}"
                        type="file" 
                        class="custom-file-input form-control kt-input text-left" 
                        dir="{{ $opt['direction'] }}"
                        placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}"
                        {!! $opt['disabled'] ? 'disabled' : '' !!}
                        id="customFile {{ $opt['id'] }}">
                    <label class="custom-file-label" for="customFile">{{__('cms::global.choose_file')}}</label>
                </div>
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

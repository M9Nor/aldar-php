@if(isset($options))
    @php
        $opt = array_merge([
            'id'                    => null,
            'name'                  => 'text',
            'label'                 => 'Image',
            'placeholder'           => null,
            'help'                  => null,
            'error_bag'             => null,
            'accepts'               => '.png, .jpg, .jpeg',
            'disabled'              => false,
            'required'              => false,
            'default'               => null,
            'browse'                => __('cms::global.browse'),
            'remove'                => __('cms::global.remove'),
            'width'                 => '250px',
            'height'                => '150px',
            /**
             * Format: 3:9. The number before ':' represents the number of columns the label will take, while the number after represents
             * the number of columns the input field will take.
             */
            'inline'                => false,
        ], $options);

        if(! $opt['id']) $opt['id'] = $opt['name'];

        $errorName = str_replace('][', '.', $opt['name']);
        $errorName = str_replace('[', '.', $errorName);
        $errorName = str_replace(']', '', $errorName);

        $opt['error_bag'] = (! $opt['error_bag']) ? $errors : $errors->{$opt['error_bag']};
    @endphp
    <style>
        .kt-avatar .kt-avatar__holder {
            width: {{ $opt['width'] }};
            height: {{ $opt['height'] }};
        }
    </style>
    <div class="form-group kt-form__group {{ $opt['inline'] ? 'row' : '' }} {{ $opt['error_bag']->has( $opt['name'] ) ? 'has-danger' : '' }}">

        @if($opt['label'])
            <label for="{{ $opt['id'] }}" class="{{ $opt['inline'] ? 'col-form-label col-lg-'.Str::before($opt['inline'], ':') : '' }}">
                <strong class="text-focus">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif

        <div class="{{ $opt['inline'] ? 'col-lg-'.Str::after($opt['inline'], ':') : '' }}">
            <div class="input-group">
                <div class="kt-avatar kt-avatar--outline kt-avatar--danger" id="{{ $opt['id'] }}">
                    <div class="kt-avatar__holder" style="background-image: url({{ $opt['default'] }})"></div>
                    @if(! $opt['disabled'])
                        <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="{{  $opt['browse']  }}" data-original-title="{{  $opt['browse']  }}">
                            <i class="fa fa-pen"></i>
                            <input name="{{ $opt['name'] }}"
                                accept="{{ $opt['accepts'] }}"
                                placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}"
                                type="file">
                        </label>
                        <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="{{  $opt['remove']  }}" data-original-title="{{  $opt['remove']  }}">
                            <i class="fa fa-times"></i>
                        </span>
                    @endif
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

@push('scripts')
    <script>
        new KTAvatar("{{ $opt['id'] }}")
    </script>
@endpush

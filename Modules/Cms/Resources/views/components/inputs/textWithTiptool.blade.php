@if(isset($options))
    @php
        $opt = array_merge([
            'id'          => null,
            'type'        => 'text',
            'name'        => 'text',
            'label'       => 'Text Input',
            'placeholder' => null,
            'help'        => null,
            'errorBag'    => null,
            'input_class' => 'm-input-group m-input-group--air',
            'tipToolContent' => null,
            'value'       => null,
            'disabled'    => false,
            'readonly'    => false,
        ], $options);        
        if( ! $opt['id'] ) $opt['id'] = $opt['name'];

        $error_name = str_replace('][', '.', $opt['name']);
        $error_name = str_replace('[', '.', $error_name);
        $error_name = str_replace(']', '', $error_name);

        $opt['errorBag'] = ( ! $opt['errorBag'] ) ? $errors : $errors->{$opt['errorBag']};
    @endphp
    @push('styles')
    <style>
        .tooltip-inner {
            max-width: 400px; /* set this to your maximum fitting width */
            width: inherit;
            background:#fff;
            color:#000; /* will take up least amount of space */ 
        }
        .bs-tooltip-top .arrow::before, .bs-tooltip-auto[x-placement^="top"] .arrow::before {
            border-top-color:#ddd;
            
        }
        label .flaticon-questions-circular-button {
            font-size: 1.1rem
        }
        @media screen and (max-width: 401px) {
        .large-tooltip .tooltip-inner {
            max-width: 100%;
            }
        }
    </style>
    @endpush
    <div class="form-group m-form__group{{ $opt['errorBag']->has( $opt['name'] ) ? ' has-danger' : '' }}">
        
        @if( $opt['label'] )
            <strong class="text-primary">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong> <i class="flaticon-questions-circular-button" 
            data-toggle="tooltip" data-placement="bottom" data-html="true" title='{{$opt['tipToolContent']}}'></i> </label>
        @endif
        <div class="input-group {{ $opt['input_class'] }}">
            @if($opt['type'] == 'password')
            <div class="input-group-append">
                
                <button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                Popover on right
                </button>
            @endif
            <input name="{{ $opt['name'] }}" type="{{ $opt['type'] }}" class="form-control m-input" id="{{ $opt['id'] }}" placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}" value="{{ ($opt['value']) ? $opt['value'] : '' }}" {!! ($opt['disabled']) ? ' disabled=""' : '' !!} {!! ($opt['readonly']) ? ' readonly=""' : '' !!}>
        </div>
        @if( $opt['errorBag']->has( $error_name ) )
            <div class="form-control-feedback">
                {{ $opt['errorBag']->first( $error_name ) }}
            </div>
        @endif
        {{-- @if( $opt['help'] )
        <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="right" title="Tooltip on right">
            Tooltip on right
        </button>
        @endif --}}
        @if( $opt['help'] )
        <span class="m-form__help"> {{ $opt['help'] }} </span>
        @endif
    </div>

    @if( $opt['type'] == 'password' )
        @push('scripts')
        <script>
            
            $(".toggle-password").on('click', function() {
                $(this).children('i').toggleClass("fa-eye fa-eye-slash");
                var input = $(this).parent('div').siblings('input');
                if(input.attr("type") == "password")
                {
                    input.attr("type", "text");
                }
                else
                {
                    input.attr("type", "password");
                }
            });
            
        </script>
        @endpush
    @endif
    @push('scripts')
        <script>
            
            $('[data-toggle="tooltip"]').tooltip();
                            
        </script>
        @endpush
@endif
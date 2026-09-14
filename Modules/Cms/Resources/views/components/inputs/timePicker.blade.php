@if(isset($options))
    @php
        $opt = array_merge([
            'id'          => null,
            'type'        => 'text',
            'name'        => 'text',
            'label'       => null,
            'placeholder' => null,
            'help'        => null,
            'errorBag'    => null,
            'input_class' => 'm-input--air',
            'value'       => null,
            'disabled'    => false,
            'scriptOptions' => [
                'minuteStep'  => 15, 
                'defaultTime' => "00:00",
                'showSeconds' => false,
                'showMeridian'=> false,
                'snapToStep'  => true,
            ],
            'required'    => false,
        ], $options);

        if( ! $opt['id'] ) $opt['id'] = $opt['name'];

        $opt['errorBag'] = ( ! $opt['errorBag'] ) ? $errors : $errors->{$opt['errorBag']};
    @endphp
    <div class="form-group timepicker m-form__group{{ $opt['errorBag']->has( $opt['name'] ) ? ' has-danger' : '' }}">
        @if( $opt['label'] )
            <label for="{{ $opt['id'] }}">
                <strong class="text-primary">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif
        <input name="{{ $opt['name'] }}" class="form-control m-input {{ $opt['input_class'] }} time_picker" id="{{ $opt['id'] }}" readonly="" placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}" value="{{ ($opt['value']) ? $opt['value'] : '' }}" {!! ($opt['disabled']) ? ' disabled=""' : '' !!} type="text">
        @if( $opt['errorBag']->has( $opt['name'] ) )
            <div class="form-control-feedback">
                {{ is_array($error = $opt['errorBag']->first( $opt['name'] )) ? $error[0] : $error }}
            </div>
        @endif

        @if( $opt['help'] )
            <span class="m-form__help"> {{ $opt['help'] }} </span>
        @endif
    </div>

    @push('styles')
        <style>
            .bootstrap-timepicker-widget {
                direction: ltr !important;
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            // var BootstrapTimepicker = {
            //     init : function() {
            //         $("#{{ $opt['id'] }}").timepicker({!! json_encode($opt['scriptOptions']) !!});
            //         $("#{{ $opt['id'] }}").timepicker({
            //             minuteStep : 1, 
            //             defaultTime:"", 
            //             showSeconds:!0, 
            //             showMeridian:!1, 
            //             snapToStep:!0
            //         });
            //     }
            // };
            jQuery(document).ready(function(){
                $(".time_picker").timepicker({!! json_encode($opt['scriptOptions']) !!});
            });
        </script>
    @endpush
@endif
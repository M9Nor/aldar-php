@if(isset($options))
    @php
        $opt = array_merge([
            'id'          => null,
            'type'        => 'checkbox',
            'name'        => 'switch',
            'label'       => null,
            'help'        => null,
            'errorBag'    => null,
            'input_class' => 'm-input--air',
            'value'       => null,
            'checked'     => false,
            'color'       => 'success',
            'switch_class'=> 'col-3',
            'input_class' => 'col-9',
            'switch_position' => 'left',
            'required'    => false,
            'disabled'    => false,
        ], $options);

        if( ! $opt['id'] ) $opt['id'] = $opt['name'];

        $opt['errorBag'] = ( ! $opt['errorBag'] ) ? $errors : $errors->{$opt['errorBag']};
    @endphp
    <style>
        .m-form .m-form__section.m-form__section--label-align-right .m-form__group>label, .m-form.m-form--label-align-right .m-form__group>label {
            text-align: unset;
        }
    </style>
    <div class="m-form__group form-group row{{ $opt['errorBag']->has( $opt['name'] ) ? ' has-danger' : '' }}">
        @if($opt['switch_position'] == 'right')
            <div class="{{ $opt['switch_class'] }}">
                <span class="m-switch m-switch--outline m-switch--icon m-switch--{{ $opt['color'] }} text-right">
                    <label>
                        <input type="{{ $opt['type'] }}"{!! ($opt['checked']) ? ' checked="checked" ' : ' ' !!} {!! ($opt['disabled']) ? ' disabled ' : ' ' !!}name="{{ $opt['name'] }}" value="{{ $opt['value'] }}">
                        <span></span>
                    </label>
                </span>
            </div>
        @endif
        
        @if( $opt['label'] )
            <label class="{{ $opt['input_class'] }}"><strong class="text-primary">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong><br />
                @if( $opt['help'] )
                    <span class="m-form__help"> {{ $opt['help'] }} </span>
                @endif
            </label>
        @endif

        @if($opt['switch_position'] == 'left')
            <div class="{{ $opt['switch_class'] }}">
                <span class="m-switch m-switch--outline m-switch--icon m-switch--{{ $opt['color'] }} text-right">
                    <label>
                        <input type="{{ $opt['type'] }}"{!! ($opt['checked']) ? ' checked="checked" ' : ' ' !!} {!! ($opt['disabled']) ? ' disabled ' : ' ' !!}name="{{ $opt['name'] }}" value="{{ $opt['value'] }}">
                        <span></span>
                    </label>
                </span>
            </div>
        @endif

        @if( $opt['errorBag']->has( $opt['name'] ) )
            <div class="form-control-feedback">
                {{ $opt['errorBag']->first( $opt['name'] ) }}
            </div>
        @endif
    </div>
@endif
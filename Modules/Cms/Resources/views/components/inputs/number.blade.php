@if(isset($options))
    @php
        $opt = array_merge([
            'id'          => null,
            'type'        => 'number',
            'name'        => 'number',
            'label'       => 'number Input',
            'placeholder' => null,
            'help'        => null,
            'errorBag'    => null,
            'required'    => false,
            'input_class' => 'm-input-group',
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
    <div class="form-group m-form__group{{ $opt['errorBag']->has( $opt['name'] ) ? ' has-danger' : '' }}">
        @if( $opt['label'] )
            <label for="{{ $opt['id'] }}">
                <strong class="text-primary">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif
        <div class="input-group {{ $opt['input_class'] }}">
            <input name="{{ $opt['name'] }}" type="{{ $opt['type'] }}" class="form-control m-input" id="{{ $opt['id'] }}" placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}" value="{{ ($opt['value']) ? $opt['value'] : '' }}" {!! ($opt['disabled']) ? ' disabled=""' : '' !!} {!! ($opt['readonly']) ? ' readonly=""' : '' !!}>
        </div>
        @if( $opt['errorBag']->has( $error_name ) )
            <div class="form-control-feedback">
                {{ $opt['errorBag']->first( $error_name ) }}
            </div>
        @endif

        @if( $opt['help'] )
            <span class="m-form__help"> {{ $opt['help'] }} </span>
        @endif
    </div>
@endif
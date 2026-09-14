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
            'input_class' => 'm-input--air',
            'value'       => null,
            'disabled'    => false,
            'readonly'    => false,
            'required'    => false,
        ], $options);

        if( ! $opt['id'] ) $opt['id'] = $opt['name'];

        $opt['errorBag'] = ( ! $opt['errorBag'] ) ? $errors : $errors->{$opt['errorBag']};
    @endphp
    <div class="form-group m-form__group{{ $opt['errorBag']->has( $opt['name'] ) ? ' has-danger' : '' }}">
        @if( $opt['label'] )
            <label for="{{ $opt['id'] }}">
                <strong class="text-primary">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif
        <div class="input-group {{ $opt['input_class'] }}">
            @if($opt['type'] == 'password')
            <div class="input-group-append">
                <button class="btn btn-secondary toggle-password" type="button"><i class="fa fa-lg fa-eye"></i></button>
            </div>
            @endif
            <input name="{{ $opt['name'] }}" type="{{ $opt['type'] }}" class="form-control m-input" id="{{ $opt['id'] }}" placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}" value="{{ ($opt['value']) ? $opt['value'] : '' }}" {!! ($opt['disabled']) ? ' disabled=""' : '' !!} {!! ($opt['readonly']) ? ' readonly=""' : '' !!}>
        </div>
        @if( $opt['errorBag']->has( $opt['name'] ) )
            <div class="form-control-feedback">
                {{ $opt['errorBag']->first( $opt['name'] ) }}
            </div>
        @endif

        @if( $opt['help'] )
            <span class="m-form__help"> {{ $opt['help'] }} </span>
        @endif
    </div>

    @if( $opt['type'] == 'password' )
        @push('scripts')
        <script>
            $(".toggle-password").on('click', function() {
                console.log('clicked!');
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
@endif
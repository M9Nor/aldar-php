@if ( isset( $options ) )
    @php
        $opt = array_merge([
            'id'          => null,
            'class'       => null,
            'nullable'    => false,
            'searchable'  => true,
            'name'        => 'select',
            'data'        => [],
            'selected'    => null,
            'help'        => null,
            'label'       => null,
            'placeholder' => null,
            'value'       => function($data, $key, $value){ return null; },
            'text'        => function($data, $key, $value){ return null; },
            'image'       => function($data, $key, $value){ return null; },
            'sub_text'    => function($data, $key, $value){ return null; },
            'description_text'    => function($data, $key, $value){ return null; },
            'select'      => function($data, $selected, $key, $value){ return false; },
            'disabled'    => false,
            'multiple'    => false,
            'required'    => false,
        ], $options);

        if( ! $opt['id'] ) $opt['id'] = $opt['name'];
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
    <div class="form-group m-form__group{{ $errors->has($opt['name']) ? ' has-danger' : '' }}">
        @if ( ! is_null( $opt['label'] ) )
            <label for="{{ $opt['name'] }}">
                <strong class="text-primary">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong> <i class="flaticon-questions-circular-button" 
            data-toggle="tooltip" data-placement="bottom" data-html="true" title='{{$opt['tipToolContent']}}'></i> 
            </label>
        @endif
        <select class="form-control m-bootstrap-select m_selectpicker {{ $opt['class'] }}" id="{{ $opt['id'] }}" data-live-search="{{ ($opt['searchable']) ? 'true' : 'false' }}" name="{{ $opt['name'] }}" {!! ($opt['disabled']) ? ' disabled=""' : '' !!} {!! ($opt['multiple']) ? ' multiple=""' : '' !!}
            data-live-search="true"
            data-none-results-text="لا يوجد نتائج مطابقة" 
            data-none-selected-text="غير محدد" 
            data-select-all-text="تحديد الكل" 
            data-deselect-all-text="إلغاء التحديد" 
            data-count-selected-text="{0} عنصر محدد" 
            data-actions-box="true"
            data-selected-text-format="count > 4">
            @if($opt['nullable'] && !$opt['multiple'])
                <option name="{{$opt['name']}}" value="" selected=""> -- </option>
            @endif
            @foreach ($opt['data'] as $key => $value)
                <option
                    name="{{$opt['name']}}"
                    {!! ( !is_null( $sub = $opt['sub_text']($opt['data'], $key, $value) ) ) ? 'data-subtext="'. e($sub) .'" ' : '' !!}
                    {!! ( !is_null( $description = $opt['description_text']($opt['data'], $key, $value) ) ) ? 'description-text="'. e($description) .'" ' : '' !!}
                    {!! ( !is_null( $sub = $opt['image']($opt['data'], $key, $value) ) ) ? 'data-image="'. e($sub) .'" ' : '' !!}
                    value="{{ $opt['value']($opt['data'], $key, $value) }}"
                    {!! $opt['select']($opt['data'], $opt['selected'], $key, $value) ? ' selected=""' : '' !!}>
                    {!! $opt['text']($opt['data'], $key, $value) !!}
                </option>
            @endforeach
        </select>
        @if($opt['disabled'])
            @foreach ($opt['data'] as $key => $value)
                @if( $selected = $opt['select']($opt['data'], $opt['selected'], $key, $value) )
                    <input name="{{$opt['name']}}" type="hidden" value="{{ $opt['value']($opt['data'], $key, $value) }}">
                @endif
            @endforeach
        @endif
        @if ( $errors->has( $opt['name'] ) )
            <div class="form-control-feedback">
                {{ $errors->first($opt['name']) }}
            </div>
        @endif
        @if ( ! is_null( $opt['help'] ) )
            <span class="m-form__help">
                {{ $opt['help'] }}
            </span>
        @endif
    </div>
@endif
    @push('scripts')
        <script>
            
            $('[data-toggle="tooltip"]').tooltip();
                            
        </script>
    @endpush

    
        
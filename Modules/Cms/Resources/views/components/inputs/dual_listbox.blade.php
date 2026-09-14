@if(isset($options))
    @php
        $opt = array_merge([
            'id'                => null,
            'name'              => 'dual_listbox',
            'searchable'        => true,
            'icons'             => true,
            'data'              => [],
            'selected'          => null,
            'option_disabled'   => function($data, $selected, $key, $value){ return false; },
            'label'             => null,
            'help'              => null,
            'available_items'   => __('cms::dual_listbox.available_items'),
            'selected_items'    => __('cms::dual_listbox.selected_items'),
            'value'             => function($data, $key, $value){ return null; },
            'text'              => function($data, $key, $value){ return null; },
            'select'            => function($data, $selected, $key, $value){ return false; },
            'required'          => false,
            'error_bag'         => null,
            /**
             * Format: 3:9. The number before ':' represents the number of columns the label will take, while the number after represents
             * the number of columns the input field will take.
             */
            'inline'            => false
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
            <select id="{{ $opt['id'] }}" name="{{ $opt['name'] }}" class="kt-dual-listbox" multiple
                data-available-title="{{ $opt['available_items'] }}"
                data-selected-title="{{ $opt['selected_items'] }}"
                @if(! $opt['searchable'])
                    data-search="false"
                @endif
                @if($opt['icons'])
                    data-add="<i class='flaticon2-{{ $langDirection == 'rtl' ? 'back' : 'next' }}'></i>"
                    data-add-all="<i class='flaticon2-{{ $langDirection == 'rtl' ? 'fast-back' : 'fast-next' }}'></i>"
                    data-remove="<i class='flaticon2-{{ $langDirection == 'rtl' ? 'next' : 'back' }}'></i>"
                    data-remove-all="<i class='flaticon2-{{ $langDirection == 'rtl' ? 'fast-next' : 'fast-back' }}'></i>"
                @endif
                >
                @foreach ($opt['data'] as $key => $value)
                    <option
                        value="{{ $opt['value']($opt['data'], $key, $value) }}"
                        {!! $opt['select']($opt['data'], $opt['selected'], $key, $value) ? ' selected=""' : '' !!}
                        {!! $opt['option_disabled']($opt['data'],$opt['selected'], $key, $value) ? ' disabled=""' : '' !!}>
                        {!! $opt['text']($opt['data'], $key, $value) !!}
                    </option>
                @endforeach
            </select>

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
        var t = $("#{{ $opt['id'] }}")
        , a = null != t.attr("data-available-title") ? t.attr("data-available-title") : "{{ $opt['available_items'] }}"
        , e = null != t.attr("data-selected-title") ? t.attr("data-selected-title") : "{{ $opt['selected_items'] }}"
        , l = null != t.attr("data-add") ? t.attr("data-add") : "{{ __('cms::dual_listbox.add') }}"
        , d = null != t.attr("data-remove") ? t.attr("data-remove") : "{{ __('cms::dual_listbox.remove') }}"
        , i = null != t.attr("data-add-all") ? t.attr("data-add-all") : "{{ __('cms::dual_listbox.add_all') }}"
        , o = null != t.attr("data-remove-all") ? t.attr("data-remove-all") : "{{ __('cms::dual_listbox.remove_all') }}"
        , n = [];
        t.children("option").each(function() {
            var t = $(this).val()
            , a = $(this).text()
            , e = !!$(this).is(":selected");
            n.push({
                text: a,
                value: t,
                selected: e
            })
        });
        var r = null != t.attr("data-search") ? t.attr("data-search") : "";
        t.empty();
        var s = new DualListbox(t.get(0),{
            addEvent: function(t) {
                console.log(t)
            },
            removeEvent: function(t) {
                console.log(t)
            },
            availableTitle: a,
            selectedTitle: e,
            addButtonText: l,
            removeButtonText: d,
            addAllButtonText: i,
            removeAllButtonText: o,
            options: n
        });
        "false" == r && s.search.classList.add("dual-listbox__search--hidden");
        $('.dual-listbox__search').attr('placeholder', "{{ __('cms::dual_listbox.search') }}").attr('class', 'form-control kt-input');
        $('.dual-listbox__container').attr('class', 'dual-listbox__container justify-content-between');
    </script>
@endpush


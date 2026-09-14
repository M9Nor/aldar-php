@if ( isset( $options ) )
    @php
        $opt = array_merge([
            'id'                    => null,
            'input_class'           => null,
            'name'                  => 'select',
            'data'                  => [],
            'selected'              => [],
            'label'                 => null,
            'locale'                => null,
            'placeholder'           => '--',
            'help'                  => null,
            'error_bag'             => null,
            'disabled'              => false,
            'multiple'              => true,
            'required'              => false,
            'clear_button'          => false,
            'input_attributes'      => null,
            'url'                   => route('TagController@list'),
            'dir'                   => LaravelLocalization::getCurrentLocaleDirection(),
            'value'                 => function($data, $key, $value){ return null; },
            'text'                  => function($data, $key, $value){ return null; },
            'sub_text'              => function($data, $key, $value){ return null; },
            'select'                => function($data, $selected, $key, $value){ return false; },
            /**
             * Format: 3:9. The number before ':' represents the number of columns the label will take, while the number after represents
             * the number of columns the input field will take.
             */
             'inline'               => false,
             'icon'                 => false,

        ], $options);

        if(! $opt['id']) $opt['id'] = $opt['name'];

        $errorName = str_replace('][', '.', $opt['name']);
        $errorName = str_replace('[', '.', $errorName);
        $errorName = str_replace(']', '', $errorName);

        $opt['error_bag'] = (! $opt['error_bag']) ? $errors : $errors->{$opt['error_bag']};

        @endphp
    <style>
        /* .select2-container {
            -webkit-box-shadow: 0px 3px 20px 0px rgba(113,106,202,0.11) !important;
            box-shadow: 0px 3px 20px 0px rgba(113,106,202,0.11) !important;
        } */

    </style>
    <div class="form-group kt-form__group {{ $opt['inline'] ? 'row' : '' }}">
        @if(! is_null($opt['label']))
            <label for="{{ $opt['name'] }}" class="{{ $opt['inline'] ? 'col-form-label col-lg-'.Str::before($opt['inline'], ':') : '' }}">
                <strong class="text-focus">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif
        <div class="{{ $opt['clear_button'] ? 'input-group m-input-group ' : ''}} {{ $opt['inline'] ? 'col-lg-'.Str::after($opt['inline'], ':') : '' }}">
            <div class="input-group {{ $opt['icon'] ? 'kt-input-icon kt-input-icon--right' : '' }} {{ $opt['input_class'] }}">
                <select class="form-control {{ $opt['input_class'] }}"
                        id="{{ $opt['id'] }}" {!! $opt['input_attributes'] !!}
                        name="{{ $opt['name'] }}"
                        {!! ($opt['disabled']) ? ' disabled' : '' !!}
                        {!! ($opt['multiple']) ? ' multiple' : '' !!}>
                        @foreach ($opt['data'] as $key => $value)
                            <option
                                name="{{$opt['name']}}"
                                {!! ( !is_null( $sub = $opt['sub_text']($opt['data'], $key, $value) ) ) ? 'data-subtext="'. e($sub) .'" ' : '' !!}
                                value="{{ $opt['value']($opt['data'], $key, $value) }}"
                                {!! $opt['select']($opt['data'], $opt['selected'], $key, $value) ? ' selected=""' : '' !!}>
                                {{ $opt['text']($opt['data'], $key, $value) }}
                            </option>
                        @endforeach
                </select>
                @if($opt['clear_button'])
                    <div class="input-group-append w-0">
                        <button class="btn btn-outline-secondary m-btn--icon empty-select2" type="button" {!! ($opt['disabled']) ? ' disabled' : '' !!}>
                            <i class="la la-close"></i>
                        </button>
                    </div>
                @endif
            </div>
            @if($opt['error_bag']->has($errorName))
                <div class="form-control-feedback">
                    {{ $opt['error_bag']->first( $errorName ) }}
                </div>
            @endif
            @if(!empty($opt['help']))
                <span class="m-form__help">
                    {!! $opt['help'] !!}
                </span>
            @endif
        </div>
    </div>
@endif

@push('scripts')
<script>
    // Define the Arabic localization for select2 messages.
    (function () {
        if (jQuery && jQuery.fn && jQuery.fn.select2 && jQuery.fn.select2.amd) var e = jQuery.fn.select2.amd;
        return e.define("select2/i18n/{{ app()->getLocale() }}", [], function () {
            return {
                errorLoading: function () {
                    return "@lang('cms::select2.error_loading')";
                },
                inputTooLong: function (e) {
                    var t = e.input.length - e.maximum;
                    return "@lang('cms::select2.input_too_long') " + t + " @lang('cms::select2.elements')";
                },
                inputTooShort: function (e) {
                    var t = e.minimum - e.input.length;
                    return "@lang('cms::select2.input_too_short') " + t + " @lang('cms::select2.elements')";
                },
                loadingMore: function () {
                    return "@lang('cms::select2.loading_more')";
                },
                maximumSelected: function (e) {
                    return "@lang('cms::select2.maximum_selected') " + e.maximum + " @lang('cms::select2.elements_only')";
                },
                noResults: function () {
                    return "@lang('cms::select2.no_results')";
                },
                searching: function () {
                    return "@lang('cms::select2.searching')"
                }
            }
        }), {
            define: e.define,
            require: e.require
        }
    })();

    var selected{{ $opt['id'] }} = [];

    @foreach($opt['selected'] as $id)
        selected{{ $opt['id'] }}.push("{{ $id }}");
    @endforeach
    var tags{{ $opt['id'] }} = $(`[id="{{ $opt['id'] }}"]`);

    tags{{ $opt['id'] }}.select2({
        placeholder: "{{ $opt['placeholder'] }}",
        language: "{{ app()->getLocale() }}",
        dir: "{{ LaravelLocalization::getCurrentLocaleDirection() }}",
        minimumInputLength: 0,
        maximumSelectionLength: 45,
        ajax: {
            url: "{{ $opt['url'] }}",
            dataType: "json",
            delay: 400,
            data: function (e) {
                return {
                    q: $.trim(e.term),
                    count: $(this).children('option').length,
                    locale: "{{$opt['locale']}}"
                }
            },
            processResults: function (e) {
                return {
                    results: e
                }
            },
            cache: !0
        }
    });

    tags{{ $opt['id'] }}.val(selected{{ $opt['id'] }}).trigger('change');

    // Check if the option being selected already exists and translated to all available languages. If not, open a form to create it or add translations.
    tags{{ $opt['id'] }}.on('select2:selecting', function(event) {
        var keyword = event.params.args.data;

        if(keyword)
        {
            event.preventDefault();
            $(this).select2("close");

            $.ajax({
                url: "{{ route('TagController@save') }}",
                type: 'POST',
                data:  {
                    _token: "{{ csrf_token() }}",
                    keyword: keyword,
                    locale: "{{$opt['locale']}}"
                },
                success: function (response) {
                    if(response.success && response.tag)
                    {
                        items = tags{{ $opt['id'] }}.val();
                        var tag = response.tag;
                        // Set the value, creating a new option if necessary
                        if(!tags{{ $opt['id'] }}.find("option[value='" + tag.id + "']").length)
                        {
                            // Create a DOM Option and pre-select by default
                            var newTag = new Option(tag.t_text, tag.id, false, false);
                            // Append it to the select
                            tags{{ $opt['id'] }}.append(newTag).trigger('change');
                        }
                        items.push(tag.id);
                        tags{{ $opt['id'] }}.val(items).trigger('change');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }
    });
</script>
@endpush

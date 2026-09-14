@if(isset($options))
    @php
        $opt = array_merge([
            'id'                => null,
            'class'             => null,
            'name'              => 'select',
            'selected'          => [],
            'label'             => null,
            'placeholder'       => '--',
            'help'              => null,
            'disabled'          => false,
            'multiple'          => false,
            'required'          => false,
            'clear_button'      => false,
            'input_attributes'  => null,
            'additional_params'  => [],
            'items_per_page'    => 20,
            'url'               => '#',
            'dir'               => LaravelLocalization::getCurrentLocaleDirection()
        ], $options);

        if(! $opt['id']) $opt['id'] = $opt['name'];
    @endphp

    <div class="form-group m-form__group{{ $errors->has($opt['name']) ? ' has-danger' : '' }} pt-0">
        @if(! is_null($opt['label']))
            <label for="{{ $opt['name'] }}">
                <strong class="text-primary">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif
        <div class="{{ $opt['clear_button'] ? 'input-group m-input-group' : '' }}">
            <select class="form-control {{ $opt['class'] }}" 
                    id="{{ $opt['id'] }}" {!! $opt['input_attributes'] !!} 
                    name="{{ $opt['name'] }}" 
                    {!! ($opt['disabled']) ? ' disabled' : '' !!} 
                    {!! ($opt['multiple']) ? ' multiple' : '' !!}>
            </select>
            @if($opt['clear_button'])
            <div class="input-group-append">
                <button class="btn btn-outline-primary m-btn--icon empty-select2" type="button" {!! ($opt['disabled']) ? ' disabled' : '' !!}>
                    <span><i class="la la-close"></i></span>
                </button>
            </div>
            @endif
        </div>
        @if($errors->has($opt['name']))
            <div class="form-control-feedback">
                {{ $errors->first($opt['name']) }}
            </div>
        @endif
        @if(!empty($opt['help']))
            <span class="m-form__help">
                {!! $opt['help'] !!}
            </span>
        @endif
    </div>

@endif

@push('styles')
    <style>
        .select2-container--default .select2-results__option {
            padding: 10px 15px;
            direction: {{ $opt['dir'] }};
        }
        .select2-container--default .select2-results__option.select2-results__option--highlighted {
            background: #f4f5f8;
            color: #3f4047;
        }
        .select2-container--default .select2-results__option[aria-selected=true] {
            background: #f4f5f8;
            color: #3f4047;
        }
        .select2-container--default .select2-results__option[aria-selected=true]:after {
            content: "[@lang('cms::select2.selected')]";
            font-size: 11px;
            margin: 0 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #575962;
            direction: {{ $opt['dir'] }};
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Define the Arabic localization for select2 messages.
        (function () {
            if(jQuery && jQuery.fn && jQuery.fn.select2 && jQuery.fn.select2.amd) var e = jQuery.fn.select2.amd;
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

        var selected = [];

        // An array of the selected options.
        var selected_data = {!! json_encode($opt['selected']) !!};
        
        if($.isArray(selected_data))
        {
            selected = selected_data;
        }
        else
        {
            selected.push(selected_data);
        }

        var {{ $opt['name'] }}_parameters = [];
        
        // These parameters are to be used for passing extra information to the server-side functions.
        {{ $opt['name'] }}_parameters.additional_params = {!! json_encode($opt['additional_params']) !!};

        // Number of the paginated results per request.
        {{ $opt['name'] }}_parameters.items_per_page = {!! json_encode($opt['items_per_page']) !!};

        $(`[name="{{ $opt['name'] }}"]`).select2({
            placeholder: "{{ $opt['placeholder'] }}",
            language: "{{ app()->getLocale() }}",
            dir: "{{ LaravelLocalization::getCurrentLocaleDirection() }}",
            minimumInputLength: 0,
            data: selected,
            delay: 250,
            ajax: {
                url: "{{ $opt['url'] }}",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    var query = {
                        search              : params.term,
                        page                : params.page || 1,
                        items_per_page      : {{ $opt['name'] }}_parameters.items_per_page,
                        additional_params   : {{ $opt['name'] }}_parameters.additional_params
                    }
                    
                    return query;
                },
                processResults: function (data, params) {
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: !0
            },
            templateResult: function(e) {
                if (e.loading) {
                    return e.text;
                }

                return `<span>${e.text}</span> ${e.sub_text ? `<small class="text-muted">${e.sub_text}</small>` : ``}`;
            },
            templateSelection: function(e) {
                if (e.loading) {
                    return e.text;
                }

                return `<span>${e.text}</span> ${e.sub_text ? `<small class="text-muted">${e.sub_text}</small>` : ``}`;
            },
            escapeMarkup: function(m) {
                return m;
            }
        });

        @if($opt['clear_button'])
            $('.empty-select2').click(function(){
                console.log($(this).closest('.input-group-append').siblings('select'))
                $(this).closest('.input-group-append').siblings('select').val("").trigger('change');
            });
        @endif
    </script>
@endpush

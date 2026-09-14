<?php
    use Illuminate\Support\Str;
?>

@if(isset($options))
    @php
        $opt = array_merge([
            'id'                    => null,
            'input_class'           => null,
            'name'                  => 'select',
            'selected'              => [],
            'label'                 => null,
            'placeholder'           => '--',
            'help'                  => null,
            'error_bag'             => null,
            'disabled'              => false,
            'multiple'              => false,
            'required'              => false,
            'clear_button'          => false,
            'self_initialize'       => true,
            'input_attributes'      => null,
            'label_star'            => false,
            'additional_params'     => [],
            'on_deselect_warning'   => [
                'type'          => __('cms::select2.messages.cannot_be_deselected.type'),
                'title'         => __('cms::select2.messages.cannot_be_deselected.title'),
                'description'   => __('cms::select2.messages.cannot_be_deselected.description')
            ],
            'items_per_page'        => 20,
            'url'                   => '#',
            'dir'                   => LaravelLocalization::getCurrentLocaleDirection(),
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

@push('styles')
    <style>
        .select2-container--default .select2-results__option {
            padding: 10px 15px;
            position: relative;
            direction: {{ $opt['dir'] }};
        }
        .select2-container--default .select2-results_option.select2-results_option--highlighted {
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
            position: absolute;
            right: 0px;
            bottom: 0;
            font-weight: 600;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #575962;
            direction: {{ $opt['dir'] }};
        }
        .select2.select2-container.select2-container--default{
            width:100%!important;
        }
    </style>
    @if(app()->getLocale() == 'ar')
        <style>
            .select2-container--default .select2-results__option[aria-selected=true]:after {
                right: unset;
                left: 0px;
            }
        </style>
    @endif
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
        if($.isArray(selected_data)){
            selected = selected_data;
        }
        else{
            selected.push(selected_data);
        }
        var {{ $opt['id'] }}_parameters = [];
        // These parameters are to be used for passing extra information to the server-side functions.
        {{ $opt['id'] }}_parameters.additional_params = {!! json_encode($opt['additional_params']) !!};
        // Number of the paginated results per request.
        {{ $opt['id'] }}_parameters.items_per_page = {!! json_encode($opt['items_per_page']) !!};
        var on_deselect_warning = {!! json_encode($opt['on_deselect_warning']) !!};
        var {{ $opt['id'] }}_config = {
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
                        items_per_page      : {{ $opt['id'] }}_parameters.items_per_page,
                        additional_params   : {{ $opt['id'] }}_parameters.additional_params
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
               // console.log(e);
                if(e.image){
                    return `
                        <div class="select-item d-flex w-100">
                            <div class="align-self-center">
                                <img width="50" src="${e.image}" alt="">
                            </div>
                            <div class="align-self-center mx-3">
                                <h6 class="text-primary mb-1">
                                    <strong>${e.text}</strong>
                                </h6>
                                ${e.sub_text ? `<small class="text-muted">${e.sub_text}</small>` : ``}
                            </div>
                        </div>
                    `;
                }
                else{
                    return `
                        <div class="select-item">
                            <h6 class="text-primary mb-1">
                                <strong>${e.text}</strong>
                            </h6>
                            ${e.sub_text ? `<small class="text-muted">${e.sub_text}</small>` : ``}
                        </div>
                    `;
                }
            },
            templateSelection: function(e) {
                if (e.loading) {
                    return e.text;
                }
                return `<span>${e.text}</span>`;
            },
            escapeMarkup: function(m) {
                return m;
            }
        }
        // Ignore disabled options and prevent them from being deselected.
        $(`[id="{{ $opt['id'] }}"]`).on("select2:unselecting", function (e) {
            if (e.params.args.data.disabled) {
                e.preventDefault();
                swal(on_deselect_warning.title, on_deselect_warning.description, on_deselect_warning.type);
            } else {
                return true;
            }
        });
        @if($opt['self_initialize'])
            $(`[id="{{ $opt['id'] }}"]`).select2({{ $opt['id'] }}_config);
        @endif
        @if($opt['clear_button'])
            $('.empty-select2').click(function(){

                // Ignore disabled options and prevent them from being deselected.
                let values_to_deselect = [];
                $(this).closest('.input-group-append').siblings('select').children('option[disabled]').each(function(i, e) {
                    values_to_deselect.push($(this).val());
                });
                $(this).closest('.input-group-append').siblings('select').val(values_to_deselect).trigger('change');
            });
        @endif
    </script>
@endpush

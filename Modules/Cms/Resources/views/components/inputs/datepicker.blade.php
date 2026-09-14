<?php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
?>

@if(isset($options))
    @php

        $opt = array_merge([
            'id'                    => null,
            'name'                  => 'text',
            'label'                 => 'Datepicker Input',
            'placeholder'           => null,
            'help'                  => null,
            'error_bag'             => null,
            'input_class'           => '',
            'value'                 => null,
            'disabled'              => false,
            'readonly'              => false,
            'start_date'            => Carbon::now()->subWeek()->format('Y/m/d'),
            'end_date'              => Carbon::now()->format('Y/m/d'),
            'required'              => false,
            'icon'                  => false,
            /**
             * Format: 3:9. The number before ':' represents the number of columns the label will take, while the number after represents
             * the number of columns the input field will take.
             */
            'inline'                => false,
            // If set to true and the input type is password, the password can be shown if desired.
            'direction'             => 'rtl',
            // Removes valid/invalid status from inputs on input/change events.
            'status_removal'        => true
        ], $options);

        if(! $opt['id']) $opt['id'] = $opt['name'];

        $errorName = str_replace('][', '.', $opt['name']);
        $errorName = str_replace('[', '.', $errorName);
        $errorName = str_replace(']', '', $errorName);

        $opt['error_bag'] = (! $opt['error_bag']) ? $errors : $errors->{$opt['error_bag']};
    @endphp

    <div class="form-group kt-form__group {{ $opt['inline'] ? 'row' : '' }}">

        @if($opt['label'])
            <label for="{{ $opt['id'] }}" class="{{ $opt['inline'] ? 'col-form-label col-lg-'.Str::before($opt['inline'], ':') : '' }}">
                <strong class="text-focus">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif

        <div class="{{ $opt['inline'] ? 'col-lg-'.Str::after($opt['inline'], ':') : '' }}">
            <div class="input-group {{ $opt['icon'] ? 'kt-input-icon kt-input-icon--right' : '' }} {{ $opt['input_class'] }} pull-left">

                @if($opt['type'] == 'password' && $opt['showable'])
                    <div class="input-group-prepend">
                        <button class="btn btn-secondary btn-icon toggle-password" type="button">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                @endif
                <input
                    name="{{ $opt['name'] }}"
                    type="text"
                    class="form-control kt-input text-left {{ !$opt['status_removal'] ? 'disable-status-removal' : '' }}"
                    id="{{ $opt['id'] }}"
                    placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}"
                    value="{{ ($opt['value']) ? $opt['value'] : '' }}"
                    data-start-date="{{ $opt['start_date'] }}"
                    data-end-date="{{ $opt['end_date'] }}"
                    {{-- dir="{{ $opt['direction'] }}" --}}
                    {!! $opt['disabled'] ? 'disabled' : '' !!}
                    {!! $opt['readonly'] ? 'readonly' : '' !!}
                    {{ $opt['type'] == 'password' ? 'autocomplete=new-password' : '' }}>

                @if($opt['icon'])
                    <span class="kt-input-icon__icon kt-input-icon__icon--right">
                        <span><i class="{{ $opt['icon'] }}"></i></span>
                    </span>
                @endif

            </div>

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

    @push('scripts')
        <script>
            var start = $('#{{ $opt['id'] }}').data('startDate');
            var end = $('#{{ $opt['id'] }}').data('endDate');

            $('#{{ $opt['id'] }}').daterangepicker({
                // singleDatePicker: true,
                buttonClasses: ' btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                opens: "{{ LaravelLocalization::getCurrentLocaleDirection() == 'rtl' ? 'left' : 'right' }}",
                startDate: start,
                endDate: end,
                locale: {!! json_encode(__('cms::global.lang.date.locale')) !!},
                ranges: {
                    "{{ __('cms::global.lang.date.ranges.today') }}": [moment(), moment()],
                    "{{ __('cms::global.lang.date.ranges.last_7_days') }}": [moment().subtract(6, 'days'), moment()],
                    "{{ __('cms::global.lang.date.ranges.last_30_days') }}": [moment().subtract(29, 'days'), moment()]
                }
            }, function(start, end, label) {
                $('#{{ $opt['id'] }} .form-control').val(start.format('YYYY/MM/DD') + ' / ' + end.format('YYYY/MM/DD'));
            });

            $('#{{ $opt['id'] }}').val('');
        </script>
    @endpush
@endif

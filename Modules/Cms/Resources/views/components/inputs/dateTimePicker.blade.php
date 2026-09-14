<?php
    use Illuminate\Support\Str;
?>
@if(isset($options))
    @php
        $opt = array_merge([
            'id'                => null,
            'name'              => 'datetime',
            'label'             => 'Date Time Picker Input',
            'placeholder'       => null,
            'help'              => null,
            'errorBag'          => null,
            'input_class'       => '',
            'error_bag'         => null,
            'value'             => null,
            'load_scripts'      => true,
            'show_clear'        => false,
            'format'            => 'yyyy/mm/dd hh:ii',
            'minViewMode'       => "days",
            'viewMode'          => "centuries",
            'required'          => false,
            'disabled'          => false,
            'readonly'          => true,
            'inline'            => false,
            'direction'         => 'rtl',
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
            <div class="input-group {{ $opt['input_class'] }}">
                <input  name="{{ $opt['name'] }}" 
                        @if($opt['readonly']) readonly @endif 
                        @if($opt['disabled']) disabled="" @endif 
                        type="text" 
                        class="form-control m-input {{ $opt['input_class'] }} date-time-picker" 
                        id="{{ $opt['id'] }}" 
                        placeholder="{{ ($opt['placeholder']) ? $opt['placeholder'] : '' }}"
                        dir="{{ $opt['direction'] }}"
                        value="{{ ($opt['value']) ? $opt['value'] : '' }}">
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
    @if($opt['load_scripts'])
        @push('scripts')
            <script>
                var BootstrapDatetimepicker_{{ $opt['name'] }} = {
                    init : function() {
                        // $("#m_datetimepicker_1").datetimepicker({
                        //     todayHighlight : !0,
                        //     autoclose: !0,
                        //     format :"yyyy.mm.dd hh:ii"
                        // }),
                        // $("#m_datetimepicker_2, #m_datetimepicker_1_validate, #m_datetimepicker_2_validate, #m_datetimepicker_3_validate").datetimepicker({
                        //     todayHighlight:!0,
                        //     autoclose:!0,
                        //     pickerPosition:"bottom-left",
                        //     format:"yyyy/mm/dd hh:ii"
                        // }),
                        // $("#m_datetimepicker_2_modal").datetimepicker({
                        //     todayHighlight:!0,
                        //     autoclose:!0,
                        //     pickerPosition:"bottom-left",
                        //     format:"yyyy/mm/dd hh:ii"
                        // }),
                        $(".date-time-picker").datetimepicker({
                            isRTL          : {{ $langDirection == 'rtl' ? 'true' : 'false' }},
                            language       : 'en',
                            todayHighlight : true,
                            autoclose      : true,
                            todayBtn       : true,
                            pickerPosition : "top-left",
                            format         : "{!! $opt['format'] !!}",
                            clearBtn       : true,
                            minViewMode    : "{!! $opt['minViewMode'] !!}",
                            viewMode       : "{!! $opt['viewMode'] !!}"
                        });
                        // $("#m_datetimepicker_3_modal").datetimepicker({todayHighlight:!0,autoclose:!0,pickerPosition:"bottom-left",todayBtn:!0,format:"yyyy/mm/dd hh:ii"}),
                        // $("#m_datetimepicker_4_1").datetimepicker({todayHighlight:!0,autoclose:!0,pickerPosition:"bottom-left",format:"yyyy.mm.dd hh:ii"}),
                        // $("#m_datetimepicker_4_2").datetimepicker({todayHighlight:!0,autoclose:!0,pickerPosition:"bottom-right",format:"yyyy/mm/dd hh:ii"}),
                        // $("#m_datetimepicker_4_3").datetimepicker({todayHighlight:!0,autoclose:!0,pickerPosition:"top-left",format:"yyyy-mm-dd hh:ii"}),
                        // $("#m_datetimepicker_4_4").datetimepicker({todayHighlight:!0,autoclose:!0,pickerPosition:"top-right",format:"yyyy-mm-dd hh:ii"}),
                        // $("#m_datetimepicker_5").datetimepicker({format:"dd MM yyyy - HH:ii P",showMeridian:!0,todayHighlight:!0,autoclose:!0,pickerPosition:"bottom-left"}),
                        // $("#m_datetimepicker_6").datetimepicker({format:"yyyy/mm/dd",todayHighlight:!0,autoclose:!0,startView:2,minView:2,forceParse:0,pickerPosition:"bottom-left"}),
                        // $("#m_datetimepicker_7").datetimepicker({format:"hh:ii",showMeridian:!0,todayHighlight:!0,autoclose:!0,startView:1,minView:0,maxView:1,forceParse:0,pickerPosition:"bottom-left"})
                    }
                };

                jQuery(document).ready( function(){ BootstrapDatetimepicker_{{ $opt['name'] }}.init() });
            </script>
        @endpush
    @endif
@endif

@push('styles')
    <style>
        [data-repeater-item]:last-child {
            border: unset;
        }
        [data-repeater-item] {
            border-bottom: 1px dashed #ebedf2;
            margin: 20px 0;
        }
    </style>
@endpush
<div id="kt_repeater_1">
    {{-- model pay repeater --}}
    @foreach($model->payments as $payment)
        <div class="row" id="delete_payment_{{$payment->id}}">
            <div class="col-md-4">
                @include('cms::components.inputs.select2', [
                    'options' => [
                        'id'                => "paying_pay_id_{$payment->id}",
                        'name'              => "paying[{$payment->id}][pay_id]",
                        'input_class'       => "paying-pay-id{$payment->id}",
                        'label'             => __('backend::projects.fields.pay.label'),
                        'placeholder'       => __('backend::projects.fields.pay.placeholder'),
                        'help'              => __('backend::projects.fields.pay.help'),
                        'selected'          => $payment->payCategory->formAjaxArray(),
                        'required'          => true,
                        'multiple'          => false,
                        'clear_button'      => false,
                        'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'payments']),
                        'additional_params' => [
                            'is_parent' => 1,
                        ],
                        'items_per_page'    => 20,
                        'dir'               => 'rtl',
                    ]
                ])
            </div>
            <div class="col-md-4">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "first_pay_{$payment->id}",
                        'name'              => "paying[{$payment->id}][first_pay]",
                        'type'              => 'text',
                        'label'             => __('backend::projects.fields.first_pay.label'),
                        'placeholder'       => __('backend::projects.fields.first_pay.placeholder'),
                        'help'              => __('backend::projects.fields.first_pay.help'),
                        'value'             => old('first_pay',$payment->first_pay),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 20,
                        'direction'         => 'ltr',
                        'status_removal'    => false
                    ]
                ])
            </div>
            <div class="col-md-4">
                @include('cms::components.inputs.select2', [
                    'options' => [
                        'id'                => "number_pays_{$payment->id}",
                        'name'              => "paying[{$payment->id}][number_pays]",
                        'input_class'       => "number-pays-id{$payment->id}",
                        'label'             => __('backend::projects.fields.number_pays.label'),
                        'placeholder'       => __('backend::projects.fields.number_pays.placeholder'),
                        'help'              => __('backend::projects.fields.number_pays.help'),
                        'selected'          => $payment->numberCategory->formAjaxArray(),
                        'required'          => true,
                        'multiple'          => false,
                        'clear_button'      => false,
                        'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'payments']),
                        'additional_params' => [
                            'category_parent_id' => $payment->payCategory->id,
                        ],
                        'items_per_page'    => 20,
                        'dir'               => 'rtl',
                    ]
                ])
            </div>
            @push('scripts')
                <script>
                    $(".paying-pay-id{{$payment->id}}").on('change', function() {
                        if($(this).val() != '' && $(this).val() != null){
                            $(".number-pays-id{{$payment->id}}").attr('disabled',false);
                            $(".number-pays-id{{$payment->id}}").val('').trigger('change');
                            number_pays_{{$payment->id}}_parameters.additional_params.category_parent_id = $(this).val();
                        }
                    });
                </script>
            @endpush
            <div class="col-md-12">
                @include('cms::components.inputs.textarea', [
                    'options' => [
                        'id'                => "note_pays_ar_{$payment->id}",
                        'name'              => "paying[{$payment->id}][note_pays_ar]",
                        'type'              => 'text',
                        'label'             => __('backend::projects.fields.note_pays_ar.label'),
                        'placeholder'       => __('backend::projects.fields.note_pays_ar.placeholder'),
                        'help'              => __('backend::projects.fields.note_pays_ar.help'),
                        'value'             => old('note_pays_ar',$payment->notes_ar),
                        'required'          => false,
                        'inline'            => false,
                        'maxlength'         => 20,
                        'direction'         => 'ltr',
                        'status_removal'    => false
                    ]
                ])
                {{-- <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div> --}}
            </div>
            <div class="col-md-12">
                @include('cms::components.inputs.textarea', [
                    'options' => [
                        'id'                => "note_pays_en_{$payment->id}",
                        'name'              => "paying[{$payment->id}][note_pays_en]",
                        'type'              => 'text',
                        'label'             => __('backend::projects.fields.note_pays_en.label'),
                        'placeholder'       => __('backend::projects.fields.note_pays_en.placeholder'),
                        'help'              => __('backend::projects.fields.note_pays_en.help'),
                        'value'             => old('note_pays_en',$payment->notes_tr),
                        'required'          => false,
                        'inline'            => false,
                        'maxlength'         => 20,
                        'direction'         => 'ltr',
                        'status_removal'    => false
                    ]
                ])
                {{-- <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div> --}}
            </div>
            <div class="col-md-12">
                @include('cms::components.inputs.textarea', [
                    'options' => [
                        'id'                => "note_pays_fa_{$payment->id}",
                        'name'              => "paying[{$payment->id}][note_pays_fa]",
                        'type'              => 'text',
                        'label'             => __('backend::projects.fields.note_pays_fa.label'),
                        'placeholder'       => __('backend::projects.fields.note_pays_fa.placeholder'),
                        'help'              => __('backend::projects.fields.note_pays_fa.help'),
                        'value'             => old('note_pays_fa',$payment->notes_en),
                        'required'          => false,
                        'inline'            => false,
                        'maxlength'         => 20,
                        'direction'         => 'ltr',
                        'status_removal'    => false
                    ]
                ])
                {{-- <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div> --}}
            </div>
            <hr>
            <div class="col-md-12" style="margin-bottom: 10px">
                <a href="javascript:;" data-paymentId="{{$payment->id}}" class="btn-sm btn btn-label-danger btn-bold delete-payment">
                    <i class="la la-trash-o"></i>
                    {{__('cms::cruds.contents.external_attachments.delete')}}
                </a>
            </div>
            <hr>
        </div>
    @endforeach
    {{-- add new pay repeater --}}
    <div class="row">
        <div data-repeater-list="new_paying" class="col-md-12">
            <div data-repeater-item class=" row align-items-center" >
                <div class="col-md-4">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'pay_id',
                            'name'              => 'pay_id',
                            'input_class'       => 'pay-id',
                            'label'             => __('backend::projects.fields.pay.label'),
                            'placeholder'       => __('backend::projects.fields.pay.placeholder'),
                            'help'              => __('backend::projects.fields.pay.help'),
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => false,
                            'clear_button'      => false,
                            'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'payments']),
                            'additional_params' => [
                                'is_parent' => 1,
                            ],
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                            'self_initialize'   => false,
                        ]
                    ])
                </div>
                <div class="col-md-4">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'first_pay',
                            'name'              => 'first_pay',
                            'type'              => 'text',
                            'label'             => __('backend::projects.fields.first_pay.label'),
                            'placeholder'       => __('backend::projects.fields.first_pay.placeholder'),
                            'help'              => __('backend::projects.fields.first_pay.help'),
                            'value'             => old('first_pay'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
                <div class="col-md-4">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'number_pays',
                            'name'              => 'number_pays',
                            'input_class'       => 'number-pays-id',
                            'label'             => __('backend::projects.fields.number_pays.label'),
                            'placeholder'       => __('backend::projects.fields.number_pays.placeholder'),
                            'help'              => __('backend::projects.fields.number_pays.help'),
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => false,
                            'clear_button'      => false,
                            'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'payments']),
                            'additional_params' => [
                                'category_parent_id' => null,
                            ],
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                            'self_initialize'   => false,
                        ]
                    ])
                </div>
                <div class="col-md-12">
                    @include('cms::components.inputs.textarea', [
                        'options' => [
                            'id'                => 'note_pays_ar',
                            'name'              => 'note_pays_ar',
                            'type'              => 'text',
                            'label'             => __('backend::projects.fields.note_pays_ar.label'),
                            'placeholder'       => __('backend::projects.fields.note_pays_ar.placeholder'),
                            'help'              => __('backend::projects.fields.note_pays_ar.help'),
                            'value'             => old('note_pays_ar'),
                            'required'          => false,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                      {{-- <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div> --}}
                </div>
                <div class="col-md-12">
                    @include('cms::components.inputs.textarea', [
                        'options' => [
                            'id'                => 'note_pays_en',
                            'name'              => 'note_pays_en',
                            'type'              => 'text',
                            'label'             => __('backend::projects.fields.note_pays_en.label'),
                            'placeholder'       => __('backend::projects.fields.note_pays_en.placeholder'),
                            'help'              => __('backend::projects.fields.note_pays_en.help'),
                            'value'             => old('note_pays_en'),
                            'required'          => false,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                      {{-- <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div> --}}
                </div>
                <div class="col-md-12">
                    @include('cms::components.inputs.textarea', [
                        'options' => [
                            'id'                => 'note_pays_fa',
                            'name'              => 'note_pays_fa',
                            'type'              => 'text',
                            'label'             => __('backend::projects.fields.note_pays_fa.label'),
                            'placeholder'       => __('backend::projects.fields.note_pays_fa.placeholder'),
                            'help'              => __('backend::projects.fields.note_pays_fa.help'),
                            'value'             => old('note_pays_fa'),
                            'required'          => false,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                      {{-- <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div> --}}
                </div>
                <hr>
                <div class="col-md-12" style="margin-bottom: 10px">
                    <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                        <i class="la la-trash-o"></i>
                        {{__('cms::cruds.contents.external_attachments.delete')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class=" row">
        <div class="col-md-12">
           <div class="col-md-4">
                <a href="javascript:;" data-repeater-create="" class="btn btn-label-success btn-bold">
                    <i class="la la-plus"></i>{{__('backend::projects.fields.pay.add')}}
                </a>
            </div>
        </div>
    </div>
</div>

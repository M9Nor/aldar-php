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
<div id="kt_repeater_2">
    <div class=" row">
        <div data-repeater-list="prices" class="col-md-12">
            <div data-repeater-item class=" row align-items-center">
                <div class="col-md-3">
                    {{-- @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'balance',
                            'name'              => 'balance',
                            'type'              => 'text',
                            'label'             => __('backend::opportunity.fields.balance.label'),
                            'placeholder'       => __('backend::opportunity.fields.balance.placeholder'),
                            'help'              => __('backend::opportunity.fields.balance.help'),
                            'value'             => old('balance'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ]) --}}
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'balance',
                            'name'              => 'balance',
                            'input_class'       => 'balance',
                            'label'             => __('backend::opportunity.fields.balance.label'),
                            'placeholder'       => __('backend::opportunity.fields.balance.placeholder'),
                            'help'              => __('backend::opportunity.fields.balance.help'),
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => false,
                            'clear_button'      => false,
                            'url'               => route('CmsController@getContentsSelect2',['type' => 'currencies']),
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                            'self_initialize'   => false,
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'room',
                            'name'              => 'room',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.room.label'),
                            'placeholder'       => __('backend::opportunity.fields.room.placeholder'),
                            'help'              => __('backend::opportunity.fields.room.help'),
                            'value'             => old('room'),
                            // 'required'          => true,
                            'inline'            => false,
                            'direction'         => 'ltr',
                            'status_removal'    => false,
                            'step'              => '0.5'
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'salon',
                            'name'              => 'salon',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.salon.label'),
                            'placeholder'       => __('backend::opportunity.fields.salon.placeholder'),
                            'help'              => __('backend::opportunity.fields.salon.help'),
                            'value'             => old('salon'),
                            // 'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
                {{-- <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'bath',
                            'name'              => 'bath',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.bath.label'),
                            'placeholder'       => __('backend::opportunity.fields.bath.placeholder'),
                            'help'              => __('backend::opportunity.fields.bath.help'),
                            'value'             => old('bath'),
                            'required'          => false,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div> --}}
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'lowest_area',
                            'name'              => 'lowest_area',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.lowest_area.label'),
                            'placeholder'       => __('backend::opportunity.fields.lowest_area.placeholder'),
                            'help'              => __('backend::opportunity.fields.lowest_area.help'),
                            'value'             => old('lowest_area'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'highest_area',
                            'name'              => 'highest_area',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.highest_area.label'),
                            'placeholder'       => __('backend::opportunity.fields.highest_area.placeholder'),
                            'help'              => __('backend::opportunity.fields.highest_area.help'),
                            'value'             => old('highest_area'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'lowest_price',
                            'name'              => 'lowest_price',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.lowest_price.label'),
                            'placeholder'       => __('backend::opportunity.fields.lowest_price.placeholder'),
                            'help'              => __('backend::opportunity.fields.lowest_price.help'),
                            'value'             => old('lowest_price'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'highest_price',
                            'name'              => 'highest_price',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.highest_price.label'),
                            'placeholder'       => __('backend::opportunity.fields.highest_price.placeholder'),
                            'help'              => __('backend::opportunity.fields.highest_price.help'),
                            'value'             => old('highest_price'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'discount',
                            'name'              => 'discount',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.discount.label'),
                            'placeholder'       => __('backend::opportunity.fields.discount.placeholder'),
                            'help'              => __('backend::opportunity.fields.discount.help'),
                            'value'             => old('discount'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'projecttypeprice',
                            'name'              => 'projecttypeprice',
                            'input_class'       => 'projecttypeprice',
                            'label'             => __('backend::opportunity.fields.opportunitytypeprice.label'),
                            'placeholder'       => __('backend::opportunity.fields.opportunitytypeprice.placeholder'),
                            'help'              => '',
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => false,
                            'clear_button'      => false,
                            'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'opportunity_classifications']),
                            // 'additional_params' => [
                            //     'category_parent_id' => null,
                            // ],
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                            'self_initialize'   => false,
                        ]
                    ])
                </div>

                <div class="col-md-12" style="margin-bottom: 10px">
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                            <i class="la la-trash-o"></i>
                            {{__('cms::cruds.contents.external_attachments.delete')}}
                        </a>
                    </div>
                </div>
            {{-- <div class="col-md-2">
                <a href="javascript:;" data-repeater-create="" class="btn btn-label-brand btn-bold">
                    <i class="la la-plus"></i>{{__('backend::opportunity.fields.pay.add')}}
                </a>
                <a href="javascript:;" data-repeater-delete="" class="btn btn-label-danger btn-bold">
                    <i class="la la-trash-o"></i>{{__('backend::opportunity.fields.pay.delete')}}
                </a>

            </div> --}}
            </div>
            {{-- <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div> --}}
        </div>
    </div>
    <div class=" row">
        <div class="col-md-12">
           <div class="col-md-4">
                <a href="javascript:;" data-repeater-create="" class="btn btn-label-success btn-bold">
                    <i class="la la-plus"></i>{{__('backend::opportunity.fields.pay.add')}}
                </a>
            </div>
        </div>
    </div>
</div>

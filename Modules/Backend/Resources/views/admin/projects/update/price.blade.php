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
    @foreach($model->prices as $price)
        <div class="row" id="delete_price_{{$price->id}}">
            <div class="col-md-3">
                @include('cms::components.inputs.select2', [
                    'options' => [
                        'id'                => "prices_balance_{$price->id}",
                        'name'              => "prices[{$price->id}][balance]",
                        'input_class'       => "prices-balance-id-{$price->id}",
                        'label'             => __('backend::projects.fields.balance.label'),
                        'placeholder'       => __('backend::projects.fields.balance.placeholder'),
                        'help'              => __('backend::projects.fields.balance.help'),
                        'selected'          => old("ices[{$price->id}][balance]",$price->content->formAjaxArray()),
                        'required'          => true,
                        'multiple'          => false,
                        'clear_button'      => false,
                        'url'               => route('CmsController@getContentsSelect2',['type' => 'currencies']),
                        'items_per_page'    => 20,
                        'dir'               => 'rtl',
                    ]
                ])
            </div>
            <div class="col-md-3">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "prices_room_{$price->id}",
                        'name'              => "prices[{$price->id}][room]",
                        'type'              => 'number',
                        'label'             => __('backend::projects.fields.room.label'),
                        'placeholder'       => __('backend::projects.fields.room.placeholder'),
                        'help'              => __('backend::projects.fields.room.help'),
                        'value'             => old('room',round($price->room_number,2)),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 191,
                        'direction'         => 'ltr',
                        'status_removal'    => false,
                        'step'              => '2'
                    ]
                ])
            </div>
            <div class="col-md-3">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "prices_salon_{$price->id}",
                        'name'              => "prices[{$price->id}][salon]",
                        'type'              => 'number',
                        'label'             => __('backend::projects.fields.salon.label'),
                        'placeholder'       => __('backend::projects.fields.salon.placeholder'),
                        'help'              => __('backend::projects.fields.salon.help'),
                        'value'             => old('salon',round($price->salons_number)),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 191,
                        'direction'         => 'ltr',
                        'status_removal'    => false
                    ]
                ])
            </div>
            {{-- <div class="col-md-3">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "prices_bath_{$price->id}",
                        'name'              => "prices[{$price->id}][bath]",
                        'type'              => 'number',
                        'label'             => __('backend::projects.fields.bath.label'),
                        'placeholder'       => __('backend::projects.fields.bath.placeholder'),
                        'help'              => __('backend::projects.fields.bath.help'),
                        'value'             => old('bath',round($price->bathes_number)),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 191,
                        'direction'         => 'ltr',
                        'status_removal'    => false
                    ]
                ])
            </div> --}}
            <div class="col-md-3">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "prices_lowest_area_{$price->id}",
                        'name'              => "prices[{$price->id}][lowest_area]",
                        'type'              => 'number',
                        'label'             => __('backend::projects.fields.lowest_area.label'),
                        'placeholder'       => __('backend::projects.fields.lowest_area.placeholder'),
                        'help'              => __('backend::projects.fields.lowest_area.help'),
                        'value'             => old('lowest_area',round($price->lowest_area)),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 191,
                        'direction'         => 'ltr',
                        'status_removal'    => false
                    ]
                ])
            </div>
            <div class="col-md-3">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "prices_highest_area_{$price->id}",
                        'name'              => "prices[{$price->id}][highest_area]",
                        'type'              => 'number',
                        'label'             => __('backend::projects.fields.highest_area.label'),
                        'placeholder'       => __('backend::projects.fields.highest_area.placeholder'),
                        'help'              => __('backend::projects.fields.highest_area.help'),
                        'value'             => old('highest_area',round($price->highest_area)),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 191,
                        'direction'         => 'ltr',
                        'status_removal'    => false
                    ]
                ])
            </div>
            <div class="col-md-3">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "prices_lowest_price_{$price->id}",
                        'name'              => "prices[{$price->id}][lowest_price]",
                        'type'              => 'number',
                        'label'             => __('backend::projects.fields.lowest_price.label'),
                        'placeholder'       => __('backend::projects.fields.lowest_price.placeholder'),
                        'help'              => __('backend::projects.fields.lowest_price.help'),
                        'value'             => old('lowest_price',$price->getRawOriginal('lowest_price')),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 191,
                        'direction'         => 'ltr',
                        'status_removal'    => false,
                        'step'              => 2,
                    ]
                ])
            </div>
            <div class="col-md-3">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "prices_highest_price_{$price->id}",
                        'name'              => "prices[{$price->id}][highest_price]",
                        'type'              => 'number',
                        'label'             => __('backend::projects.fields.highest_price.label'),
                        'placeholder'       => __('backend::projects.fields.highest_price.placeholder'),
                        'help'              => __('backend::projects.fields.highest_price.help'),
                        'value'             => old('highest_price',$price->getRawOriginal('highest_price')),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 191,
                        'direction'         => 'ltr',
                        'status_removal'    => false,
                        'step'              => 2,
                    ]
                ])
            </div>
            <div class="col-md-3">
                @include('cms::components.inputs.text', [
                    'options' => [
                        'id'                => "prices_discount_{$price->id}",
                        'name'              => "prices[{$price->id}][discount]",
                        'type'              => 'number',
                        'label'             => __('backend::projects.fields.discount.label'),
                        'placeholder'       => __('backend::projects.fields.discount.placeholder'),
                        'help'              => __('backend::projects.fields.discount.help'),
                        'value'             => old('discount',$price->discount),
                        'required'          => true,
                        'inline'            => false,
                        'maxlength'         => 191,
                        'direction'         => 'ltr',
                        'status_removal'    => false,
                        'step'              => 2,
                    ]
                ])
            </div>
            <div class="col-md-3">
                @include('cms::components.inputs.select2', [
                    'options' => [
                        'id'                => "prices_projecttypeprice_{$price->id}",
                        'name'              => "prices[{$price->id}][projecttypeprice]",
                        'input_class'       => "prices-projecttypeprice-id-{$price->id}",
                        'label'             => __('backend::projects.fields.projecttypeprice.label'),
                        'placeholder'       => __('backend::projects.fields.projecttypeprice.placeholder'),
                        'help'              => __('backend::projects.fields.projecttypeprice.help'),
                        'selected'          => old("prices[{$price->id}][projecttypeprice]", !empty($price->category) ? $price->category->formAjaxArray() : ''),
                        'required'          => true,
                        'multiple'          => false,
                        'clear_button'      => false,
                        'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'property_classifications']),
                        'items_per_page'    => 20,
                        'dir'               => 'rtl',
                    ]
                ])
            </div>
            <div class="col-md-3">
                @include('cms::components.inputs.select', [
                    'options' => [
                        'id'            => "prices_is_sold_{$price->id}",
                        'name'          => "prices[{$price->id}][is_sold]",
                        'type'          => 'text',
                        'label'         => __('backend::projects.is_sold'),
                        'placeholder'   => __('backend::projects.is_sold'),
                        'help'          => '',
                        'data'          => [
                            [
                                'text'      => __('cms::cruds.no'),
                                'value'     => 'no',
                            ],
                            [
                                'text'      => __('cms::cruds.yes'),
                                'value'     => 'yes',
                            ],
                        ],
                        'selected'      => old("prices[{$price->id}][is_sold]",$price->is_sold),
                        'value'         => function($data, $key, $value){ return $value['value']; },
                        'text'          => function($data, $key, $value){ return $value['text']; },
                        'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                        'required'      => true,
                        'searchable'    => false,
                        'multiple'      => false,
                    ]
                ])
            </div>
            <div class="col-md-12" style="margin-bottom: 10px">
                <a href="javascript:;" data-priceId="{{$price->id}}" class="btn-sm btn btn-label-danger btn-bold delete-price">
                    <i class="la la-trash-o"></i>
                    {{__('cms::cruds.contents.external_attachments.delete')}}
                </a>
            </div>
        </div>
    @endforeach
    <div class=" row">
        <div data-repeater-list="new_prices" class="col-md-12">
            <div data-repeater-item class=" row align-items-center">
                <div class="col-md-3">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'balance',
                            'name'              => 'balance',
                            'input_class'       => 'balance',
                            'label'             => __('backend::projects.fields.balance.label'),
                            'placeholder'       => __('backend::projects.fields.balance.placeholder'),
                            'help'              => __('backend::projects.fields.balance.help'),
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
                            'label'             => __('backend::projects.fields.room.label'),
                            'placeholder'       => __('backend::projects.fields.room.placeholder'),
                            'help'              => __('backend::projects.fields.room.help'),
                            'value'             => old('room'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
                            'direction'         => 'ltr',
                            'status_removal'    => false,
                            'step'              => '2'
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'salon',
                            'name'              => 'salon',
                            'type'              => 'number',
                            'label'             => __('backend::projects.fields.salon.label'),
                            'placeholder'       => __('backend::projects.fields.salon.placeholder'),
                            'help'              => __('backend::projects.fields.salon.help'),
                            'value'             => old('salon'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
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
                            'label'             => __('backend::projects.fields.bath.label'),
                            'placeholder'       => __('backend::projects.fields.bath.placeholder'),
                            'help'              => __('backend::projects.fields.bath.help'),
                            'value'             => old('bath'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
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
                            'label'             => __('backend::projects.fields.lowest_area.label'),
                            'placeholder'       => __('backend::projects.fields.lowest_area.placeholder'),
                            'help'              => __('backend::projects.fields.lowest_area.help'),
                            'value'             => old('lowest_area'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
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
                            'label'             => __('backend::projects.fields.highest_area.label'),
                            'placeholder'       => __('backend::projects.fields.highest_area.placeholder'),
                            'help'              => __('backend::projects.fields.highest_area.help'),
                            'value'             => old('highest_area'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
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
                            'label'             => __('backend::projects.fields.lowest_price.label'),
                            'placeholder'       => __('backend::projects.fields.lowest_price.placeholder'),
                            'help'              => __('backend::projects.fields.lowest_price.help'),
                            'value'             => old('lowest_price'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
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
                            'label'             => __('backend::projects.fields.highest_price.label'),
                            'placeholder'       => __('backend::projects.fields.highest_price.placeholder'),
                            'help'              => __('backend::projects.fields.highest_price.help'),
                            'value'             => old('highest_price'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
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
                            'label'             => __('backend::projects.fields.projecttypeprice.label'),
                            'placeholder'       => __('backend::projects.fields.projecttypeprice.placeholder'),
                            'help'              => '',
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => false,
                            'clear_button'      => false,
                            'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'property_classifications']),
                            // 'additional_params' => [
                            //     'category_parent_id' => null,
                            // ],
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                            'self_initialize'   => false,
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'discount',
                            'name'              => 'discount',
                            'type'              => 'number',
                            'label'             => __('backend::projects.fields.discount.label'),
                            'placeholder'       => __('backend::projects.fields.discount.placeholder'),
                            'help'              => __('backend::projects.fields.discount.help'),
                            'value'             => old('discount'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select_base', [
                        'options' => [
                            'id'            => 'is_sold',
                            'name'          => 'is_sold',
                            'type'          => 'text',
                            'label'         => __('backend::projects.is_sold'),
                            'placeholder'   => __('backend::projects.is_sold'),
                            'help'          => '',
                            'data'          => [
                                [
                                    'text'      => __('cms::cruds.no'),
                                    'value'     => 'no',
                                ],
                                [
                                    'text'      => __('cms::cruds.yes'),
                                    'value'     => 'yes',
                                ],
                            ],
                            'selected'      => old('is_sold','no'),
                            'value'         => function($data, $key, $value){ return $value['value']; },
                            'text'          => function($data, $key, $value){ return $value['text']; },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                            'required'      => true,
                            'searchable'    => false,
                            'multiple'      => false,
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

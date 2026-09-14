<div class="kt-section kt-section--first">
    <div class="row">
        <div class="col-md-12">
            <div class="kt-wizard-v4__form">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="kt-section__body">
                            <div class="kt-section__content">
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('cms::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'services',
                                                'name'          => 'services',
                                                'type'          => 'text',
                                                'label'         => __('cms::landing_page.fields.services.label'),
                                                'placeholder'   => __('cms::landing_page.fields.services.placeholder'),
                                                'help'          => __('cms::landing_page.fields.services.help'),
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
                                                'selected'      => old('services','no'),
                                                'value'         => function($data, $key, $value){ return $value['value']; },
                                                'text'          => function($data, $key, $value){ return $value['text']; },
                                                'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                                                'required'      => true,
                                                'searchable'    => false,
                                                'multiple'      => false,
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-12">
                                        @include('cms::components.inputs.select', [
                                            'options' => [
                                                'id'                => 'services_ids',
                                                'name'              => 'services_ids[]',
                                                'label'             => __('cms::landing_page.fields.services_ids.label'),
                                                'placeholder'       => __('cms::landing_page.fields.services_ids.placeholder'),
                                                'help'              => __('cms::landing_page.fields.services_ids.help'),
                                                'data'              => $services->get(),
                                                'selected'          => old('services'),
                                                'value'             => function($data, $key, $value){ return $value->id; },
                                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                                'required'          => false,
                                                'searchable'        => false,
                                                'multiple'          => true,
                                            ]
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="kt-section kt-section--first">
    <div class="kt-wizard-v4__form">
        <div class="kt-section__body">
            <div class="row">
                <div class="col-md-6">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'agent',
                            'name'              => 'agent',
                            'label'             => __('backend::projects.fields.agent.label'),
                            'placeholder'       => __('backend::projects.fields.agent.placeholder'),
                            'help'              => __('backend::projects.fields.agent.help'),
                            'data'              => $agents,
                            'selected'          => old('agent'),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                            'required'          => false,
                            'searchable'        => true,
                            'multiple'          => false,
                            'nullable'          => true
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'offers',
                            'name'              => 'offers[]',
                            'label'             => __('backend::projects.fields.offers.label'),
                            'placeholder'       => __('backend::projects.fields.offers.placeholder'),
                            'help'              => __('backend::projects.fields.offers.help'),
                            'data'              => $contracts,
                            'selected'          => old('offers'),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                            'required'          => true,
                            'searchable'        => false,
                            'multiple'          => true,
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'top_offers',
                            'name'              => 'top_offers[]',
                            'label'             => __('backend::projects.fields.top_offers.label'),
                            'placeholder'       => __('backend::projects.fields.top_offers.placeholder'),
                            'help'              => __('backend::projects.fields.top_offers.help'),
                            'data'              => [],
                            'selected'          => old('top_offers', []),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                            'required'          => true,
                            'searchable'        => true,
                            'multiple'          => false,
                            'actions_box'       => false,
                            'max_options'       => 3,
                        ]
                    ])
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'categories_id',
                            'name'              => 'categories_id',
                            'input_class'       => 'project-category-id',
                            'label'             => __('backend::projects.fields.category.label'),
                            'placeholder'       => __('backend::projects.fields.category.placeholder'),
                            'help'              => __('backend::projects.fields.category.help'),
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => false,
                            'clear_button'      => false,
                            'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'property_classifications','slug' =>'properties']),
                            'additional_params' => [
                                'is_parent' => 1,
                            ],
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'project_type',
                            'name'              => 'project_type[]',
                            'input_class'       => 'project-type',
                            'label'             => __('backend::projects.fields.type.label'),
                            'placeholder'       => __('backend::projects.fields.type.placeholder'),
                            'help'              => __('backend::projects.fields.type.help'),
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => true,
                            'clear_button'      => false,
                            'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'property_classifications']),
                            'additional_params' => [
                                'category_parent_id' => null,
                            ],
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                        ]
                    ])
                    @push('scripts')
                        <script>
                            $('.project-type').attr('disabled',true);
                            $('.project-category-id').on('change', function() {
                                if($(this).val() != '' && $(this).val() != null){
                                    $('.project-type').attr('disabled',false);
                                    $('.project-type').val('').trigger('change');
                                    project_type_parameters.additional_params.category_parent_id = $(this).val();
                                }
                            });
                        </script>
                    @endpush
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'top_type',
                            'name'              => 'top_type',
                            'label'             => __('backend::projects.fields.top_type.label'),
                            'placeholder'       => __('backend::projects.fields.top_type.placeholder'),
                            'help'              => __('backend::projects.fields.top_type.help'),
                            'data'              => [],
                            'selected'          => old('top_type'),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return $value->id == $selected; },
                            'required'          => true,
                            'searchable'        => true,
                            'multiple'          => false,
                            'actions_box'       => false
                        ]
                    ])
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'status',
                            'name'              => 'status[]',
                            'label'             => __('backend::projects.fields.status.label'),
                            'placeholder'       => __('backend::projects.fields.status.placeholder'),
                            'help'              => __('backend::projects.fields.status.help'),
                            'data'              => $property_status,
                            'selected'          => old('status'),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                            'required'          => true,
                            'searchable'        => false,
                            'multiple'          => true,
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'features',
                            'name'              => 'features[]',
                            'label'             => __('backend::projects.fields.features.label'),
                            'placeholder'       => __('backend::projects.fields.features.placeholder'),
                            'help'              => __('backend::projects.fields.features.help'),
                            'data'              => $property_features,
                            'selected'          => old('features', []),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                            'required'          => true,
                            'searchable'        => true,
                            'multiple'          => true,
                            'actions_box'       => false
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'top_features',
                            'name'              => 'top_features[]',
                            'label'             => __('backend::projects.fields.top_features.label'),
                            'placeholder'       => __('backend::projects.fields.top_features.placeholder'),
                            'help'              => __('backend::projects.fields.top_features.help'),
                            'data'              => [],
                            'selected'          => old('top_features', []),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                            'required'          => true,
                            'searchable'        => true,
                            'multiple'          => true,
                            'actions_box'       => false,
                            'max_options'       => 3,
                        ]
                    ])
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-md-12">
                    @include('cms::components.inputs.taggable', [
                        'options' => [
                            'id'            => 'tags',
                            'name'          => 'tags[]',
                            'label'         => __('backend::projects.fields.tags.label'),
                            'placeholder'   => __('backend::projects.fields.tags.placeholder'),
                            'help'          => __('backend::projects.fields.tags.help'),
                        ]
                    ])
                </div>
            </div> --}}
            <div class="row">
                <div class="col-md-4">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'turkish_nationality',
                            'name'          => 'turkish_nationality',
                            'type'          => 'text',
                            'label'         => __('backend::projects.fields.turkish_nationality.label'),
                            'placeholder'   => __('backend::projects.fields.turkish_nationality.placeholder'),
                            'help'          => __('backend::projects.fields.turkish_nationality.help'),
                            'data'          => [
                                [
                                    'text'      => __('cms::cruds.no'),
                                    'value'     => 0,
                                ],
                                [
                                    'text'      => __('cms::cruds.yes'),
                                    'value'     => 1,
                                ],
                            ],
                            'selected'      => old('turkish_nationality',0),
                            'value'         => function($data, $key, $value){ return $value['value']; },
                            'text'          => function($data, $key, $value){ return $value['text']; },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                            'required'      => true,
                            'searchable'    => false,
                            'multiple'      => false,
                        ]
                    ])
                </div>
                <div class="col-md-4">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'disabled',
                            'name'          => 'disabled_at',
                            'type'          => 'text',
                            'label'         => __('backend::projects.fields.disabled_at.label'),
                            'placeholder'   => __('backend::projects.fields.disabled_at.placeholder'),
                            'help'          => __('backend::projects.fields.disabled_at.help'),
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
                            'selected'      => old('disabled_at','yes'),
                            'value'         => function($data, $key, $value){ return $value['value']; },
                            'text'          => function($data, $key, $value){ return $value['text']; },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                            'required'      => true,
                            'searchable'    => false,
                            'multiple'      => false,
                        ]
                    ])
                </div>
                 <div class="col-md-4">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'is_featured',
                            'name'          => 'is_featured',
                            'type'          => 'text',
                            'label'         => __('backend::projects.fields.is_featured.label'),
                            'placeholder'   => __('backend::projects.fields.is_featured.placeholder'),
                            'help'          => __('backend::projects.fields.is_featured.help'),
                            'data'          => [
                              [
                                    'text'      => __('cms::cruds.yes'),
                                    'value'     => 'yes',
                                ],
                                
                                [
                                    'text'      => __('cms::cruds.no'),
                                    'value'     => 'no',
                                ],
                              
                            ],
                            'selected'      => old('is_featured','no'),
                            'value'         => function($data, $key, $value){ return $value['value']; },
                            'text'          => function($data, $key, $value){ return $value['text']; },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                            'required'      => true,
                            'searchable'    => false,
                            'multiple'      => false,
                        ]
                    ])
                </div>
                <div class="col-md-4">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'is_statistics',
                            'name'          => 'is_statistics',
                            'type'          => 'text',
                            'label'         => __('backend::projects.fields.is_statistics.label'),
                            'placeholder'   => __('backend::projects.fields.is_statistics.placeholder'),
                            'help'          => __('backend::projects.fields.is_statistics.help'),
                            'data'          => [
                              [
                                    'text'      => __('cms::cruds.yes'),
                                    'value'     => 'yes',
                                ],
                                
                                [
                                    'text'      => __('cms::cruds.no'),
                                    'value'     => 'no',
                                ],
                              
                            ],
                            'selected'      => old('is_statistics','no'),
                            'value'         => function($data, $key, $value){ return $value['value']; },
                            'text'          => function($data, $key, $value){ return $value['text']; },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                            'required'      => true,
                            'searchable'    => false,
                            'multiple'      => false,
                        ]
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('[name="features[]"]').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            var topFeaturesSelect = $('[name="top_features[]"]');
            var selected = $(this).find(":selected");
            var targets = [];
            $.each(selected, function(index, value){
                targets.push($(this).val());
            });
            topFeaturesSelect.html('');
            $('[name="features[]"] option').each(function(index, option) {
                if(jQuery.inArray(option.value, targets) !== -1)
                {
                    let newOption = new Option(option.text, option.value);
                    topFeaturesSelect.append(newOption);
                }
            });
            topFeaturesSelect.selectpicker('refresh');
        });
        $('[name="offers[]"]').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            var topOffersSelect = $('[name="top_offers[]"]');
            var selected = $(this).find(":selected");
            var targets = [];
            $.each(selected, function(index, value){
                targets.push($(this).val());
            });
            topOffersSelect.html('');
            $('[name="offers[]"] option').each(function(index, option) {
                if(jQuery.inArray(option.value, targets) !== -1)
                {
                    let newOption = new Option(option.text, option.value);
                    topOffersSelect.append(newOption);
                }
            });
            topOffersSelect.selectpicker('refresh');
        });

        $('[name="project_type[]"]').on('change', function (e, clickedIndex, isSelected, previousValue) {
            var projectTypesSelect = $('[name="top_type"]');
            var selected = $(this).find(":selected");
            var targets = [];
            $.each(selected, function(index, value){
                targets.push($(this).val());
            });
            projectTypesSelect.html('');
            $('[name="project_type[]"] option').each(function(index, option) {
                if(jQuery.inArray(option.value, targets) !== -1)
                {
                    let newOption = new Option(option.text, option.value);
                    projectTypesSelect.append(newOption);
                }
            });
            projectTypesSelect.selectpicker('refresh');
        });
    </script>
@endpush

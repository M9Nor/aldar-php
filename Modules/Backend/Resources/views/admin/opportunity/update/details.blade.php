<div class="kt-section kt-section--first">
    <div class="kt-wizard-v4__form">
        <div class="kt-section__body">
            <div class="row">
                <div class="col-md-6">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'agent',
                            'name'              => 'agent',
                            'label'             => __('backend::opportunity.fields.agent.label'),
                            'placeholder'       => __('backend::opportunity.fields.agent.placeholder'),
                            'help'              => __('backend::opportunity.fields.agent.help'),
                            'data'              => $agents->where('type','agents'),
                            'selected'          => old('agent',$modelAgents->get()->pluck('id')->toArray()),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return in_array($value->id,$selected); },
                            // 'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                            'required'          => false,
                            'searchable'        => true,
                            'multiple'          => false,
                            'nullable'          => true
                        ]
                    ])
                </div>
                @php
                    $modelPropertyOffersList        = $modelPropertyOffers->where('type','contracts')->get();
                    $modelTopPropertyOffersList     = $modelPropertyOffers->where('type','contracts')->wherePivot('options', 'top_offer')->get();
                @endphp
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'offers',
                            'name'              => 'offers[]',
                            'label'             => __('backend::opportunity.fields.offers.label'),
                            'placeholder'       => __('backend::opportunity.fields.offers.placeholder'),
                            'help'              => __('backend::opportunity.fields.offers.help'),
                            'data'              => $propertyOffers->where('type','contracts'),
                            'selected'          => old('offers',$modelPropertyOffersList->pluck('id')->toArray()),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return in_array($value->id,$selected); },
                            'required'          => true,
                            'searchable'        => true,
                            'multiple'          => true,
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'top_offers',
                            'name'              => 'top_offers[]',
                            'label'             => __('backend::opportunity.fields.top_offers.label'),
                            'placeholder'       => __('backend::opportunity.fields.top_offers.placeholder'),
                            'help'              => __('backend::opportunity.fields.top_offers.help'),
                            'data'              => $modelPropertyOffersList,
                            'selected'          => old('top_offers', $modelTopPropertyOffersList->pluck('id')->toArray()),
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
                  <div class="col-md-3">
                 @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'roi',
                            'name'              => 'roi',
                            'type'              => 'number',
                            'label'             => __('backend::opportunity.fields.roi.label'),
                            'placeholder'       => __('backend::opportunity.fields.roi.placeholder'),
                            'help'              => __('backend::opportunity.fields.roi.help'),
                            // 'required'          => true,
                            'inline'            => false,
                            'value'             => old('roi',$model->roi == NULL ? NULL : $model->roi),
                            'direction'         => 'rtl',
                            'status_removal'    => false,
                            'step'              => '10'
                        ]
                    ])
                </div>

                <div class="col-md-3">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'categories_id',
                            'name'              => 'categories_id',
                            'input_class'       => 'project-category-id',
                            'label'             => __('backend::opportunity.fields.category.label'),
                            'placeholder'       => __('backend::opportunity.fields.category.placeholder'),
                            'help'              => __('backend::opportunity.fields.category.help'),
                            'selected'          => $modelClassifications->where('type','opportunity_classifications')->whereNull('parent_id')->where('slug','opportunity')->get()->map(function($item) {
                                return $item->formAjaxArray();
                            }),
                            'required'          => true,
                            'multiple'          => false,
                            'clear_button'      => false,
                            
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                        ]
                    ])
                </div>
                @php
                    $modelProjectTypesList  = $modelProjectType->where('type','opportunity_classifications')->whereNotNull('parent_id')->get();
                    $modelTopProjectType    = $modelProjectType->where('type','opportunity_classifications')->wherePivot('options', 'top_type')->first();
                @endphp
                <div class="col-md-3">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'project_type',
                            'name'              => 'project_type[]',
                            'input_class'       => 'project-type',
                            'label'             => __('backend::opportunity.fields.type.label'),
                            'placeholder'       => __('backend::opportunity.fields.type.placeholder'),
                            'help'              => __('backend::opportunity.fields.type.help'),
                            'selected'          => $modelProjectTypesList->map(function($item) {
                                return $item->formAjaxArray();
                            }),
                            'required'          => true,
                            'multiple'          => true,
                            'clear_button'      => false,
                            'url'               => route('CategoryController@getCategoriesSelect2',['type' => 'opportunity_classifications']),
                            'additional_params' => [
                                'category_parent_id' => is_null( $modelClassifications->where('type','opportunity_classifications')->whereNull('parent_id')->first() ) ? '' : $modelClassifications->where('type','opportunity_classifications')->whereNull('parent_id')->first()->id,
                            ],
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                        ]
                    ])
                    @push('scripts')
                        <script>
                            // $('.project-type').attr('disabled',true);
                            $('.project-category-id').on('change', function() {
                                if($(this).val() != '' && $(this).val() != null){
                                    $('.project-type').attr('disabled',false);
                                    $('.project-type').val('').trigger('change');
                                    project_type_parameters.additional_params.category_parent_id = $(this).val();
                                    // console.log(project_type_parameters.additional_params.category_parent_id);
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
                            'label'             => __('backend::opportunity.fields.top_type.label'),
                            'placeholder'       => __('backend::opportunity.fields.top_type.placeholder'),
                            'help'              => __('backend::opportunity.fields.top_type.help'),
                            'data'              => $modelProjectTypesList,
                            'selected'          => old('top_type', !is_null($modelTopProjectType) ? $modelTopProjectType->id : null),
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
                            'label'             => __('backend::opportunity.fields.status.label'),
                            'placeholder'       => __('backend::opportunity.fields.status.placeholder'),
                            'help'              => __('backend::opportunity.fields.status.help'),
                            'data'              => $propertyStatus->where('type','property_status'),
                            'selected'          => old('status',$modelPropertyStatus->where('type','property_status')->get()->pluck('id')->toArray()),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return in_array($value->id,$selected); },
                            'required'          => true,
                            'searchable'        => false,
                            'multiple'          => true,
                        ]
                    ])
                </div>
                @php
                    $modelPropertyFeaturesList      = $modelPropertyFeatures->where('type','property_features')->get();
                    $modelTopPropertyFeaturesList   = $modelPropertyFeatures->where('type','property_features')->wherePivot('options', 'top_feature')->get();
                @endphp
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'features',
                            'name'              => 'features[]',
                            'label'             => __('backend::opportunity.fields.features.label'),
                            'placeholder'       => __('backend::opportunity.fields.features.placeholder'),
                            'help'              => __('backend::opportunity.fields.features.help'),
                            'data'              => $propertyFeatures->where('type','property_features'),
                            'selected'          => old('features',$modelPropertyFeaturesList->pluck('id')->toArray()),
                            'value'             => function($data, $key, $value){ return $value->id; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                            'select'            => function($data, $selected, $key, $value){ return in_array($value->id,$selected); },
                            'required'          => true,
                            'searchable'        => true,
                            'multiple'          => true,
                        ]
                    ])
                </div>
                <div class="col-md-3">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'top_features',
                            'name'              => 'top_features[]',
                            'label'             => __('backend::opportunity.fields.top_features.label'),
                            'placeholder'       => __('backend::opportunity.fields.top_features.placeholder'),
                            'help'              => __('backend::opportunity.fields.top_features.help'),
                            'data'              => $modelPropertyFeaturesList,
                            'selected'          => old('top_features', $modelTopPropertyFeaturesList->pluck('id')->toArray()),
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
                            'label'         => __('backend::opportunity.fields.tags.label'),
                            'placeholder'   => __('backend::opportunity.fields.tags.placeholder'),
                            'help'          => __('backend::opportunity.fields.tags.help'),
                            'data'          => $model->tags,
                            'selected'      => $model->tags->pluck('id')->toArray(),
                            'value'         => function($data, $key, $value){ return $value->id; },
                            'text'          => function($data, $key, $value) { return $value->translate()->text; },
                            'select'        => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                        ]
                    ])
                </div>
            </div> --}}
            <div class="row">
                <div class="col-md-6">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'turkish_nationality',
                            'name'          => 'turkish_nationality',
                            'type'          => 'text',
                            'label'         => __('backend::opportunity.fields.turkish_nationality.label'),
                            'placeholder'   => __('backend::opportunity.fields.turkish_nationality.placeholder'),
                            'help'          => __('backend::opportunity.fields.turkish_nationality.help'),
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
                            'selected'      => old('turkish_nationality',$model->turkish_nationality),
                            'value'         => function($data, $key, $value){ return $value['value']; },
                            'text'          => function($data, $key, $value){ return $value['text']; },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                            'required'      => true,
                            'searchable'    => false,
                            'multiple'      => false,
                        ]
                    ])
                </div>
                <div class="col-md-6">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'disabled_at',
                            'name'          => 'disabled_at',
                            'type'          => 'text',
                            'label'         => __('backend::opportunity.fields.disabled_at.label'),
                            'placeholder'   => __('backend::opportunity.fields.disabled_at.placeholder'),
                            'help'          => __('backend::opportunity.fields.disabled_at.help'),
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
                            'selected'      => old('disabled_at',is_null($model->disabled_at) ? 'yes' : 'no'),
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
            var topOfferSelect = $('[name="top_offers[]"]');
            var selected = $(this).find(":selected");
            var targets = [];
            $.each(selected, function(index, value){
                targets.push($(this).val());
            });
            topOfferSelect.html('');
            $('[name="offers[]"] option').each(function(index, option) {
                if(jQuery.inArray(option.value, targets) !== -1)
                {
                    let newOption = new Option(option.text, option.value);
                    topOfferSelect.append(newOption);
                }
            });
            topOfferSelect.selectpicker('refresh');
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

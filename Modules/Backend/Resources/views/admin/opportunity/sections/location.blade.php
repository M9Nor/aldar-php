<div class="kt-section kt-section--first">
    <div class="row">
        <div class="col-md-12">
            <div class="kt-wizard-v4__form">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="kt-section__body">
                            <div class="row">
                                <div class="col-md-4">
                                    @include('cms::components.inputs.select2', [
                                        'options' => [
                                            'id'                => 'country',
                                            'name'              => 'country',
                                            'label'             => __('backend::opportunity.fields.country.label'),
                                            'placeholder'       => __('backend::opportunity.fields.country.placeholder'),
                                            'help'              => __('backend::opportunity.fields.country.help'),
                                            'selected'          => $country == null ? '' :$country->formAjaxArray(),
                                            'required'          => true,
                                            'multiple'          => false,
                                            'clear_button'      => false,
                                            'url'               => route('CmsController@getCountries'),
                                            // 'additional_params' => [
                                            //     'is_parent' => 1,
                                            // ],
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.select2', [
                                        'options' => [
                                            'id'                => 'city',
                                            'name'              => 'city',
                                            'label'             => __('backend::opportunity.fields.city.label'),
                                            'placeholder'       => __('backend::opportunity.fields.city.placeholder'),
                                            'help'              => __('backend::opportunity.fields.city.help'),
                                            'selected'          => $city == null ? '' : $city->formAjaxArray(),
                                            'required'          => true,
                                            'multiple'          => false,
                                            'clear_button'      => false,
                                            'url'               => route('CmsController@getCities'),
                                            // 'additional_params' => [
                                            //     'country_id' => 1,
                                            // ],
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.select2', [
                                        'options' => [
                                            'id'                => 'area',
                                            'name'              => 'pro_area',
                                            'label'             => __('backend::opportunity.fields.area.label'),
                                            'placeholder'       => __('backend::opportunity.fields.area.placeholder'),
                                            'help'              => __('backend::opportunity.fields.area.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'multiple'          => false,
                                            'clear_button'      => false,
                                            'url'               => route('CmsController@getAreas'),
                                            'additional_params' => [
                                                'city_id' => null,
                                            ],
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.select2', [
                                        'options' => [
                                            'id'                => 'areas',
                                            'name'              => 'areas[]',
                                            'label'             => __('backend::opportunity.fields.areas.label'),
                                            'placeholder'       => __('backend::opportunity.fields.areas.placeholder'),
                                            'help'              => __('backend::opportunity.fields.areas.help'),
                                            'selected'          => [],
                                            'required'          => true,
                                            'multiple'          => true,
                                            'clear_button'      => false,
                                            'url'               => route('CmsController@getAreas'),
                                            'additional_params' => [
                                                'area_id' => 1,
                                            ],
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                        ]
                                    ])
                                </div>
                                @push('scripts')
                                    <script>
                                        // $('#city').attr('disabled',true);
                                        $('#area').attr('disabled',true);
                                        $('#areas').attr('disabled',true);
                                        $('#country').on('change', function() {     
                                            if($(this).val() != '' && $(this).val() != null){
                                                $('#city').attr('disabled',false);
                                                $('#city').val('').trigger('change');
                                                city_parameters.additional_params.country_id = $(this).val();
                                                // console.log(city_parameters.additional_params.country_id);
                                            }
                                        });
                                        $('#city').on('change', function() {     
                                            if($(this).val() != '' && $(this).val() != null){
                                                $('#area').attr('disabled',false);
                                                $('#area').val('').trigger('change');
                                                area_parameters.additional_params.city_id = $(this).val();
                                                // console.log(area_parameters.additional_params.city_id);
                                            }
                                        });
                                        $('#area').on('change', function() {     
                                            if($(this).val() != '' && $(this).val() != null){
                                                $('#areas').attr('disabled',false);
                                                $('#areas').val('').trigger('change');
                                                areas_parameters.additional_params.area_id = $(this).val();
                                                // console.log(areas_parameters.additional_params.area_id);
                                            }
                                        });
                                    </script>
                                @endpush
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'sea',
                                            'name'              => 'sea',
                                            'label'             => __('backend::opportunity.fields.sea.label'),
                                            'placeholder'       => __('backend::opportunity.fields.sea.placeholder'),
                                            'help'              => __('backend::opportunity.fields.sea.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'selected'          => null,                                                                                  
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => null,
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'inline'            => false,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'pro_city_distance',
                                            'name'              => 'pro_city_distance',
                                            'label'             => __('backend::opportunity.fields.city_distance.label'),
                                            'placeholder'       => __('backend::opportunity.fields.city_distance.placeholder'),
                                            'help'              => __('backend::opportunity.fields.city_distance.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'selected'          => null,                                                                                  
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => null,
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'inline'            => false,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'airport',
                                            'name'              => 'airport',
                                            'label'             => __('backend::opportunity.fields.airport.label'),
                                            'placeholder'       => __('backend::opportunity.fields.airport.placeholder'),
                                            'help'              => __('backend::opportunity.fields.airport.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'selected'          => null,                                                                                  
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => null,
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'inline'            => false,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'school',
                                            'name'              => 'school',
                                            'label'             => __('backend::opportunity.fields.school.label'),
                                            'placeholder'       => __('backend::opportunity.fields.school.placeholder'),
                                            'help'              => __('backend::opportunity.fields.school.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'selected'          => null,                                                                                  
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => null,
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'inline'            => false,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'university',
                                            'name'              => 'university',
                                            'label'             => __('backend::opportunity.fields.university.label'),
                                            'placeholder'       => __('backend::opportunity.fields.university.placeholder'),
                                            'help'              => __('backend::opportunity.fields.university.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'selected'          => null,                                                                                  
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => null,
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'inline'            => false,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'hospital',
                                            'name'              => 'hospital',
                                            'label'             => __('backend::opportunity.fields.hospital.label'),
                                            'placeholder'       => __('backend::opportunity.fields.hospital.placeholder'),
                                            'help'              => __('backend::opportunity.fields.hospital.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'selected'          => null,                                                                                  
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => null,
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'inline'            => false,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'mosque',
                                            'name'              => 'mosque',
                                            'label'             => __('backend::opportunity.fields.mosque.label'),
                                            'placeholder'       => __('backend::opportunity.fields.mosque.placeholder'),
                                            'help'              => __('backend::opportunity.fields.mosque.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'selected'          => null,                                                                                  
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => null,
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'inline'            => false,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'mall',
                                            'name'              => 'mall',
                                            'label'             => __('backend::opportunity.fields.mall.label'),
                                            'placeholder'       => __('backend::opportunity.fields.mall.placeholder'),
                                            'help'              => __('backend::opportunity.fields.mall.help'),
                                            'selected'          => [],
                                            'required'          => false,
                                            'selected'          => null,                                                                                  
                                            'multiple'          => false,
                                            'clear_button'      => true,
                                            'url'               => null,
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                            'inline'            => false,
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
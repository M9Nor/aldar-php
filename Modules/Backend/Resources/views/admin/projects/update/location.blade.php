
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
                                            'label'             => __('backend::projects.fields.country.label'),
                                            'placeholder'       => __('backend::projects.fields.country.placeholder'),
                                            'help'              => __('backend::projects.fields.country.help'),
                                            'selected'          => $model->country->formAjaxArray(),
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
                                            'label'             => __('backend::projects.fields.city.label'),
                                            'placeholder'       => __('backend::projects.fields.city.placeholder'),
                                            'help'              => __('backend::projects.fields.city.help'),
                                            'selected'          => $model->city->formAjaxArray(),
                                            'required'          => true,
                                            'multiple'          => false,
                                            'clear_button'      => false,
                                            'url'               => route('CmsController@getCities'),
                                            'additional_params' => [
                                                'country_id' => $model->country->id,
                                            ],
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
                                            'label'             => __('backend::projects.fields.area.label'),
                                            'placeholder'       => __('backend::projects.fields.area.placeholder'),
                                            'help'              => __('backend::projects.fields.area.help'),
                                            'selected'          => empty($model->area) ? [] : $model->area->formAjaxArray(),
                                            'required'          => false,
                                            'multiple'          => false,
                                            'clear_button'      => false,
                                            'url'               => route('CmsController@getAreas'),
                                            'additional_params' => [
                                                'city_id' => empty($model->area) ? null : $model->city->id,
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
                                            'label'             => __('backend::projects.fields.areas.label'),
                                            'placeholder'       => __('backend::projects.fields.areas.placeholder'),
                                            'help'              => __('backend::projects.fields.areas.help'),
                                            'selected'          => $model->areas->map(function($item) {
                                                return $item->formAjaxArray();
                                            }),
                                            'required'          => false,
                                            'multiple'          => true,
                                            'clear_button'      => false,
                                            'url'               => route('CmsController@getAreas'),
                                            'additional_params' => [
                                                'area_id' => empty($model->area) ? null : $model->area->id,
                                            ],
                                            'items_per_page'    => 20,
                                            'dir'               => 'rtl',
                                        ]
                                    ])
                                </div>
                                @push('scripts')
                                    <script>
                                        $('#country').on('change', function() {     
                                            if($(this).val() != '' && $(this).val() != null){
                                                $('#city').attr('disabled',false);
                                                $('#city').val('').trigger('change');
                                                city_parameters.additional_params.country_id = $(this).val();
                                            }
                                        });
                                        $('#city').on('change', function() {     
                                            if($(this).val() != '' && $(this).val() != null){
                                                $('#area').attr('disabled',false);
                                                $('#area').val('').trigger('change');
                                                area_parameters.additional_params.city_id = $(this).val();
                                            }
                                        });
                                        $('#area').on('change', function() {     
                                            if($(this).val() != '' && $(this).val() != null){
                                                $('#areas').attr('disabled',false);
                                                $('#areas').val('').trigger('change');
                                                areas_parameters.additional_params.area_id = $(this).val();
                                            }
                                        });
                                    </script>
                                @endpush
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'sea',
                                            'name'              => 'sea',
                                            'label'             => __('backend::projects.fields.sea.label'),
                                            'placeholder'       => __('backend::projects.fields.sea.placeholder'),
                                            'help'              => __('backend::projects.fields.sea.help'),
                                            'inline'            => false,
                                            'value'             => old('sea',$model->sea),
                                            'maxlength'         => 191,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'pro_city_distance',
                                            'name'              => 'pro_city_distance',
                                            'label'             => __('backend::projects.fields.city_distance.label'),
                                            'placeholder'       => __('backend::projects.fields.city_distance.placeholder'),
                                            'help'              => __('backend::projects.fields.city_distance.help'),
                                            'inline'            => false,
                                            'value'             => old('pro_city_distance',$model->city_distance),
                                            'maxlength'         => 191,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'airport',
                                            'name'              => 'airport',
                                            'label'             => __('backend::projects.fields.airport.label'),
                                            'placeholder'       => __('backend::projects.fields.airport.placeholder'),
                                            'help'              => __('backend::projects.fields.airport.help'),
                                            'inline'            => false,
                                            'value'             => old('airport',$model->airport),
                                            'maxlength'         => 191,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'school',
                                            'name'              => 'school',
                                            'label'             => __('backend::projects.fields.school.label'),
                                            'placeholder'       => __('backend::projects.fields.school.placeholder'),
                                            'help'              => __('backend::projects.fields.school.help'),
                                            'inline'            => false,
                                            'value'             => old('school',$model->school),
                                            'maxlength'         => 191,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'university',
                                            'name'              => 'university',
                                            'label'             => __('backend::projects.fields.university.label'),
                                            'placeholder'       => __('backend::projects.fields.university.placeholder'),
                                            'help'              => __('backend::projects.fields.university.help'),
                                            'inline'            => false,
                                            'value'             => old('university',$model->university),
                                            'maxlength'         => 191,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'hospital',
                                            'name'              => 'hospital',
                                            'label'             => __('backend::projects.fields.hospital.label'),
                                            'placeholder'       => __('backend::projects.fields.hospital.placeholder'),
                                            'help'              => __('backend::projects.fields.hospital.help'),
                                            'inline'            => false,
                                            'value'             => old('hospital',$model->hospital),
                                            'maxlength'         => 191,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'mosque',
                                            'name'              => 'mosque',
                                            'label'             => __('backend::projects.fields.mosque.label'),
                                            'placeholder'       => __('backend::projects.fields.mosque.placeholder'),
                                            'help'              => __('backend::projects.fields.mosque.help'),
                                            'inline'            => false,
                                            'value'             => old('mosque',$model->mosque),
                                            'maxlength'         => 191,
                                        ]
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('cms::components.inputs.text', [
                                        'options' => [
                                            'id'                => 'mall',
                                            'name'              => 'mall',
                                            'label'             => __('backend::projects.fields.mall.label'),
                                            'placeholder'       => __('backend::projects.fields.mall.placeholder'),
                                            'help'              => __('backend::projects.fields.mall.help'),
                                            'inline'            => false,
                                            'value'             => old('mall',$model->mall),
                                            'maxlength'         => 191,
                                        ]
                                    ])
                                </div>
                             
                                {{-- <div class="col-md-12">
                                    @include('cms::components.inputs.tinymce', [
                                        'options' => [
                                            'id'                => 'details_area',
                                            'name'              => 'details_area',
                                            'type'              => 'text',
                                            'label'             => __('backend::projects.fields.details_area.label'),
                                            'placeholder'       => __('backend::projects.fields.details_area.placeholder'),
                                            'help'              => __('backend::projects.fields.details_area.help'),
                                            'value'             => old('details_area',$model->details_area),
                                            'required'          => true,
                                            'inline'            => false,
                                            'maxlength'         => 20,
                                            'direction'         => 'ltr',
                                            'status_removal'    => false,
                                            'base_script'       =>false,
                                        ]
                                    ])
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
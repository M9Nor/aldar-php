@push('filter.toolbar')
    <a href="javascript:;" id="toggle_filter" class="btn btn-outline-warning btn-sm btn-icon btn-icon-md btn-elevate datatable-custom-tools" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="{{ __('cms::global.filter') }}">
        <i class="fa fa-filter"></i>
    </a>
@endpush

@push('filter.form')
    <div id="filter" class="kt-section" style="display: none;">
        <div class="kt-section__title">
            {{ __('backend::opportunity.filter.title') }}
        </div>
        <div class="kt-section__desc">
            {{ __('backend::opportunity.filter.description') }}
        </div>
        <div class="kt-section__content">
            <form id="filter_form" class="kt-form kt-form--fit" action="javascript:;" method="POST">
                <div class="row kt-margin-b-20">
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'name'          => 'title',
                                'type'          => 'text',
                                'label'         => __('backend::opportunity.fields.title.label'),
                                'placeholder'   => __('backend::opportunity.fields.title.placeholder'),
                                'help'          => __('backend::opportunity.fields.title.help'),
                                'value'         => old('title'),
                                'required'      => false
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'country',
                                'name'              => 'country',
                                'label'             => __('backend::opportunity.fields.country.label'),
                                'placeholder'       => __('backend::opportunity.fields.country.placeholder'),
                                'help'              => __('backend::opportunity.fields.country.help'),
                                'data'              => $countries,
                                'selected'          => old('country'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->name; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'city',
                                'name'              => 'city',
                                'label'             => __('backend::opportunity.fields.city.label'),
                                'placeholder'       => __('backend::opportunity.fields.city.placeholder'),
                                'help'              => __('backend::opportunity.fields.city.help'),
                                'data'              => $cities,
                                'selected'          => old('cities'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->name; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'area',
                                'name'              => 'area',
                                'label'             => __('backend::opportunity.fields.area.label'),
                                'placeholder'       => __('backend::opportunity.fields.area.placeholder'),
                                'help'              => __('backend::opportunity.fields.area.help'),
                                'data'              => $areas,
                                'selected'          => old('areas'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->name; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'nullable'      => true,
                                'name'          => 'status',
                                'label'         => __('backend::opportunity.fields.status.label'),
                                'help'          => __('backend::opportunity.fields.status.help'),
                                'data'          => $statuses,
                                'selected'      => old('status'),
                                'value'         => function($data, $key, $value){ return $value['code']; },
                                'text'          => function($data, $key, $value){ return __($value['label']); },
                                'select'        => function($data, $selected, $key, $value){ return $selected == $value['code']; },
                                'required'      => false,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'agent',
                                'name'              => 'agent',
                                'label'             => __('backend::opportunity.fields.agent.label'),
                                'placeholder'       => __('backend::opportunity.fields.agent.placeholder'),
                                'help'              => __('backend::opportunity.fields.agent.help'),
                                'data'              => $agents,
                                'selected'          => old('agents'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'contracts',
                                'name'              => 'contracts',
                                'label'             => __('backend::opportunity.fields.offers.label'),
                                'placeholder'       => __('backend::opportunity.fields.offers.placeholder'),
                                'help'              => __('backend::opportunity.fields.offers.help'),
                                'data'              => $contracts,
                                'selected'          => old('contracts'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'opportunity_classifications',
                                'name'              => 'opportunity_classifications',
                                'label'             => __('backend::opportunity.fields.category.label'),
                                'placeholder'       => __('backend::opportunity.fields.category.placeholder'),
                                'help'              => __('backend::opportunity.fields.category.help'),
                                'data'              => $opportunity_classifications,
                                'selected'          => old('opportunity_classifications'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'property_type',
                                'name'              => 'property_type',
                                'label'             => __('backend::opportunity.fields.type.label'),
                                'placeholder'       => __('backend::opportunity.fields.type.placeholder'),
                                'help'              => __('backend::opportunity.fields.type.help'),
                                'data'              => $property_type,
                                'selected'          => old('property_type'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'property_features',
                                'name'              => 'property_features[]',
                                'label'             => __('backend::opportunity.fields.features.label'),
                                'placeholder'       => __('backend::opportunity.fields.features.placeholder'),
                                'help'              => __('backend::opportunity.fields.features.help'),
                                'data'              => $property_features,
                                'selected'          => old('property_features'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => true,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <!-- <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'payments',
                                'name'              => 'payments',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.pay.label'),
                                'placeholder'       => __('backend::opportunity.fields.pay.placeholder'),
                                'help'              => __('backend::opportunity.fields.pay.help'),
                                'data'              => $payments,
                                'selected'          => old('payments'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.select', [
                            'options' => [
                                'id'                => 'number_pays',
                                'name'              => 'number_pays',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.number_pays.label'),
                                'placeholder'       => __('backend::opportunity.fields.number_pays.placeholder'),
                                'help'              => __('backend::opportunity.fields.number_pays.help'),
                                'data'              => $number_pays,
                                'selected'          => old('number_pays'),
                                'value'             => function($data, $key, $value){ return $value->id; },
                                'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                'select'            => function($data, $selected, $key, $value){ return $selected == $value->id; },
                                'required'          => false,
                                'searchable'        => true,
                                'multiple'          => false,
                                'nullable'          => true,
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'id'                => 'first_pay',
                                'name'              => 'first_pay',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.first_pay.label'),
                                'placeholder'       => __('backend::opportunity.fields.first_pay.placeholder'),
                                'help'              => __('backend::opportunity.fields.first_pay.help'),
                                'value'             => old('first_pay'),
                                'required'          => false,
                                'inline'            => false,
                                'maxlength'         => 20,
                                'direction'         => 'ltr',
                                'status_removal'    => false
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'id'                => 'room',
                                'name'              => 'room',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.room.label'),
                                'placeholder'       => __('backend::opportunity.fields.room.placeholder'),
                                'help'              => __('backend::opportunity.fields.room.help'),
                                'value'             => old('room'),
                                'required'          => false,
                                'inline'            => false,
                                'maxlength'         => 20,
                                'direction'         => 'ltr',
                                'status_removal'    => false
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'id'                => 'salon',
                                'name'              => 'salon',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.salon.label'),
                                'placeholder'       => __('backend::opportunity.fields.salon.placeholder'),
                                'help'              => __('backend::opportunity.fields.salon.help'),
                                'value'             => old('salon'),
                                'required'          => false,
                                'inline'            => false,
                                'maxlength'         => 20,
                                'direction'         => 'ltr',
                                'status_removal'    => false
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'id'                => 'bath',
                                'name'              => 'bath',
                                'type'              => 'text',
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
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'id'                => 'lowest_area',
                                'name'              => 'lowest_area',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.lowest_area.label'),
                                'placeholder'       => __('backend::opportunity.fields.lowest_area.placeholder'),
                                'help'              => __('backend::opportunity.fields.lowest_area.help'),
                                'value'             => old('lowest_area'),
                                'required'          => false,
                                'inline'            => false,
                                'maxlength'         => 20,
                                'direction'         => 'ltr',
                                'status_removal'    => false
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'id'                => 'highest_area',
                                'name'              => 'highest_area',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.highest_area.label'),
                                'placeholder'       => __('backend::opportunity.fields.highest_area.placeholder'),
                                'help'              => __('backend::opportunity.fields.highest_area.help'),
                                'value'             => old('highest_area'),
                                'required'          => false,
                                'inline'            => false,
                                'maxlength'         => 20,
                                'direction'         => 'ltr',
                                'status_removal'    => false
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'id'                => 'lowest_price',
                                'name'              => 'lowest_price',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.lowest_price.label'),
                                'placeholder'       => __('backend::opportunity.fields.lowest_price.placeholder'),
                                'help'              => __('backend::opportunity.fields.lowest_price.help'),
                                'value'             => old('lowest_price'),
                                'required'          => false,
                                'inline'            => false,
                                'maxlength'         => 20,
                                'direction'         => 'ltr',
                                'status_removal'    => false
                            ]
                        ])
                    </div>
                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.text', [
                            'options' => [
                                'id'                => 'highest_price',
                                'name'              => 'highest_price',
                                'type'              => 'text',
                                'label'             => __('backend::opportunity.fields.highest_price.label'),
                                'placeholder'       => __('backend::opportunity.fields.highest_price.placeholder'),
                                'help'              => __('backend::opportunity.fields.highest_price.help'),
                                'value'             => old('highest_price'),
                                'required'          => false,
                                'inline'            => false,
                                'maxlength'         => 20,
                                'direction'         => 'ltr',
                                'status_removal'    => false
                            ]
                        ])

                    </div> -->

                </div>
                <div class="row kt-margin-b-20">
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-primary btn-sm btn-brand--icon">
                            <span>
                                <i class="la la-search"></i>
                                <span>{{ __('cms::global.filter') }}</span>
                            </span>
                        </button>
                        &nbsp;&nbsp;
                        <button id="reset_filter" class="btn btn-secondary btn-sm btn-secondary--icon">
                            <span>
                                <i class="la la-close"></i>
                                <span>{{ __('cms::global.reset') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
            </form>
        </div>
    </div>
@endpush

@push('filter.scripts')

    <script>
        $(function() {
            var scrollTop = new KTScrolltop('toggle_filter', {
                offset: 300,
                speed: 600,
                toggleClass: 'kt-scrolltop--on'
            });
            // Show/hide the filter form.
            $('#toggle_filter').click(function(e) {
                e.preventDefault();

                if($('#filter').css('display') == 'none')
                {
                    $('#filter').slideDown(400);
                    $(this).addClass('active');
                }
                else
                {
                    $('#filter').slideUp(400);
                    $(this).removeClass('active');
                    $('#filter_form').trigger("reset");
                }
            });

            // Reset all filter form fields.
            $('#reset_filter').click(function() {
                $('#filter_form').trigger("reset");
            });

            // Reload datatable and submit the filter data.
            $('#filter_form').submit(function () {
                dataTable.ajax.reload();
            });
        });
    </script>

@endpush


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
                            'selected'          => old('agent'),
                            'data'              => $agents,
                            'value'             => function($data, $key, $value){ return $key; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                            'select'            => function($data, $selected, $key, $value){ return $selected == $key; },
                            'required'          => true,
                            'searchable'        => false,
                            'inline'            => false,
                        ]
                    ])
                </div>
                <div class="col-md-6">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'offers',
                            'name'              => 'offers',
                            'type'              => 'text',
                            'multiple'          => true,
                            'label'             => __('backend::projects.fields.offers.label'),
                            'placeholder'       => __('backend::projects.fields.offers.placeholder'),
                            'help'              => __('backend::projects.fields.offers.help'),
                            'selected'          => old('offers'),
                            'data'              => $contracts,
                            'value'             => function($data, $key, $value){ return $key; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                            'select'            => function($data, $selected, $key, $value){ return $selected == $key; },
                            'required'          => true,
                            'searchable'        => false,
                            'inline'            => false,
                        ]
                    ])
                </div>
                <div class="col-md-6">

                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'categories_ids',
                            'name'              => 'categories_ids',
                            'label'             => __('cms::cruds.contents.category.label'),
                            'placeholder'       => __('cms::cruds.contents.category.placeholder'),
                            'help'              => __('cms::cruds.contents.category.help'),
                            'selected'          => old('categories_ids'),
                            'data'              => $property_classifications,
                            'value'             => function($data, $key, $value){ return $key; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                            'select'            => function($data, $selected, $key, $value){ return $selected == $key; },
                            'required'          => true,
                            'searchable'        => false,
                            'inline'            => false,
                        ]
                    ])
                </div>
                {{-- select2 --}}
                <div class="col-md-6">
                    @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'type',
                            'name'              => 'type',
                            'label'             => __('backend::projects.fields.type.label'),
                            'placeholder'       => __('backend::projects.fields.type.placeholder'),
                            'help'              => __('backend::projects.fields.type.help'),
                            'selected'          => [],
                            'required'          => true,
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
                <div class="col-md-6">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'stats',
                            'name'              => 'stats',
                            'label'             => __('backend::projects.fields.stats.label'),
                            'placeholder'       => __('backend::projects.fields.stats.placeholder'),
                            'help'              => __('backend::projects.fields.stats.help'),
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => true,
                            'data'              => $property_status,
                            'value'             => function($data, $key, $value){ return $key; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                            'select'            => function($data, $selected, $key, $value){ return $selected == $key; },
                            'required'          => true,
                            'searchable'        => false,
                            'inline'            => false,
                        ]
                    ])
                </div>
                <div class="col-md-6">
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'                => 'features',
                            'name'              => 'features',
                            'label'             => __('backend::projects.fields.features.label'),
                            'placeholder'       => __('backend::projects.fields.features.placeholder'),
                            'help'              => __('backend::projects.fields.features.help'),
                            'selected'          => [],
                            'required'          => true,
                            'multiple'          => true,
                            'data'              =>$property_features,
                            'value'             => function($data, $key, $value){ return $key; },
                            'text'              => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                            'select'            => function($data, $selected, $key, $value){ return $selected == $key; },
                            'required'          => true,
                            'searchable'        => true,
                            'inline'            => false,
                        ]
                    ])
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-last row">
                        <span class="col-form-label col-lg-12 col-sm-12"> {{ __('backend::projects.fields.tags.label')}}</span>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <input id="kt_tagify_1" name='tags' placeholder='{{__('backend::projects.fields.tags.placeholder')}}' value='' autofocus data-blacklist=''>

                            <div class="kt-margin-t-10">
                                <a href="javascript:;" style="    float: left;" id="kt_tagify_1_remove" class="btn btn-label-brand btn-bold"> {{ __('backend::projects.fields.tags.delet')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @include('cms::components.inputs.textarea', [
                        'options' => [
                            'id'                => 'keys',
                            'name'              => 'keys',
                            'type'              => 'text',
                            'label'             => __('backend::projects.fields.keys.label'),
                            'placeholder'       => __('backend::projects.fields.keys.placeholder'),
                            'help'              => __('backend::projects.fields.keys.help'),
                            'value'             => old('keys'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

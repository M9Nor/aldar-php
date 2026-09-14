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
                                                'id'            => 'blogs',
                                                'name'          => 'blogs',
                                                'type'          => 'text',
                                                'label'         => __('cms::landing_page.fields.blogs.label'),
                                                'placeholder'   => __('cms::landing_page.fields.blogs.placeholder'),
                                                'help'          => __('cms::landing_page.fields.blogs.help'),
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
                                                'selected'      => old('blogs','no'),
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
                                                'id'                => 'blogs_ids',
                                                'name'              => 'blogs_ids[]',
                                                'label'             => __('cms::landing_page.fields.blogs_ids.label'),
                                                'placeholder'       => __('cms::landing_page.fields.blogs_ids.placeholder'),
                                                'help'              => __('cms::landing_page.fields.blogs_ids.help'),
                                                'data'              => $blogs->get(),
                                                'selected'          => old('blogs_ids'),
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

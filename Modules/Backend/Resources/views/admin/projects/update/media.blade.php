
<div class="kt-section kt-section--first">
    <div class="kt-wizard-v4__form">
        <div class="row">
            <div class="col-md-12 ">
                <div class="kt-section__body">

                     @include('cms::components.inputs.dropzone', [
                        'options' => [
                            'id'                    => 'slider_images',
                            'name'                  => 'slider_images',
                            'label'                 => __('backend::projects.fields.slider_images.label'),
                            'placeholder'           => __('backend::projects.fields.slider_images.placeholder'),
                            'help'                  => __('backend::projects.fields.slider_images.help', [
                                'prefered_dimensions' => '1920x1280',
                                'mimes'               => 'png | jpeg'
                            ]),
                            'attachments'       => $model->attachments->where('input_name', 'slider_images')->map(function($item) {
                                $item->thumbnail = $item->getThumbnail('120x120');
                                return $item;
                            }),
                            'required'              => false,
                            'max_files'             => 1,
                            'inline'                => false,
                            'validation_rules'      => 'required|image|max:1024|mimes:jpeg,jpg,png',
                            'sub_folder'            => 'projects',
                        ]
                    ])

                    @include('cms::components.inputs.dropzone', [
                        'options' => [
                            'id'                    => 'featured_images',
                            'name'                  => 'featured_images',
                            'label'                 => __('backend::projects.fields.featured_images.label'),
                            'placeholder'           => __('backend::projects.fields.featured_images.placeholder'),
                            'help'                  => __('backend::projects.fields.featured_images.help', [
                                'prefered_dimensions' => '1920x1280',
                                'mimes'               => 'png | jpeg'
                            ]),
                            'attachments'       => $model->attachments->where('input_name', 'featured_images')->map(function($item) {
                                $item->thumbnail = $item->getThumbnail('120x120');
                                return $item;
                            }),
                            'required'              => false,
                            'inline'                => false,
                            'validation_rules'      => 'required|image|max:1024|mimes:jpeg,jpg,png',
                            'sub_folder'            => 'projects',
                        ]
                    ])
                    @include('cms::components.inputs.dropzone', [
                        'options' => [
                            'id'                    => 'image_internal',
                            'name'                  => 'image_internal',
                            'label'                 => __('backend::projects.fields.image_internal.label'),
                            'placeholder'           => __('backend::projects.fields.image_internal.placeholder'),
                            'help'                  => __('backend::projects.fields.image_internal.help'),
                            'attachments'       => $model->attachments->where('input_name', 'image_internal')->map(function($item) {
                                $item->thumbnail = $item->getThumbnail('120x120');
                                return $item;
                            }),
                            'required'              => false,
                            'inline'                => false,
                            'validation_rules'      => 'required|file|max:1024|mimes:jpeg,jpg,png,pdf',
                            'sub_folder'            => 'projects',
                        ]
                    ])
                    @include('cms::components.inputs.dropzone', [
                        'options' => [
                            'id'                    => 'image_external',
                            'name'                  => 'image_external',
                            'label'                 => __('backend::projects.fields.image_external.label'),
                            'placeholder'           => __('backend::projects.fields.image_external.placeholder'),
                            'help'                  => __('backend::projects.fields.image_external.help'),
                            'attachments'       => $model->attachments->where('input_name', 'image_external')->map(function($item) {
                                $item->thumbnail = $item->getThumbnail('120x120');
                                return $item;
                            }),
                            'required'              => false,
                            'inline'                => false,
                            'validation_rules'      => 'required|file|max:1024|mimes:jpeg,jpg,png,pdf',
                            'sub_folder'            => 'projects',
                        ]
                    ])
                    @include('cms::components.inputs.dropzone', [
                        'options' => [
                            'id'                    => 'image_layouts',
                            'name'                  => 'image_layouts',
                            'label'                 => __('backend::projects.fields.image_layouts.label'),
                            'placeholder'           => __('backend::projects.fields.image_layouts.placeholder'),
                            'help'                  => __('backend::projects.fields.image_layouts.help'),
                            'attachments'       => $model->attachments->where('input_name', 'image_layouts')->map(function($item) {
                                $item->thumbnail = $item->getThumbnail('120x120');
                                return $item;
                            }),
                            'required'              => false,
                            'inline'                => false,
                            'validation_rules'      => 'required|file|max:1024|mimes:jpeg,jpg,png,pdf',
                            'sub_folder'            => 'projects',
                        ]
                    ])
                    @include('cms::components.inputs.dropzone', [
                        'options' => [
                            'id'                    => 'ser_and_fac',
                            'name'                  => 'ser_and_fac',
                            'label'                 => __('backend::projects.fields.ser_and_fac.label'),
                            'placeholder'           => __('backend::projects.fields.ser_and_fac.placeholder'),
                            'help'                  => __('backend::projects.fields.ser_and_fac.help'),
                            'attachments'       => $model->attachments->where('input_name', 'ser_and_fac')->map(function($item) {
                                $item->thumbnail = $item->getThumbnail('120x120');
                                return $item;
                            }),
                            'required'              => false,
                            'inline'                => false,
                            'validation_rules'      => 'required|file|max:1024|mimes:jpeg,jpg,png,pdf',
                            'sub_folder'            => 'projects',
                        ]
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

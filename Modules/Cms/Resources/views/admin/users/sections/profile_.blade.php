
<div class="kt-section kt-section--first">
    <div class="kt-wizard-v4__form">
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                <div class="kt-section__body">
                    <div class="form-group row">
                        <div class="col-lg-9 col-xl-6">
                            <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">{{ __('cms::users.sections.profile.description') }}</h4>
                        </div>
                    </div>
                    @include('cms::components.inputs.image', [
                        'options' => [
                            'id'            => 'image',
                            'name'          => 'image',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.image.label'),
                            'placeholder'   => __('cms::users.fields.image.placeholder'),
                            'help'          => __('cms::users.fields.image.help', [
                                'prefered_dimensions' => '150×150 | 400×400',
                                'mimes'               => 'png | jpeg'
                            ]),
                            'default'       => old('image', $model->getImage('400x400')),
                            'required'      => false,
                            'inline'        => '3:9',
                            'browse'        => __('cms::users.fields.image.add_text'),
                            'remove'        => __('cms::users.fields.image.remove_text')
                        ]
                    ])
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'            => 'firstName',
                            'name'          => 'first_name',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.first_name.label'),
                            'placeholder'   => __('cms::users.fields.first_name.placeholder'),
                            'help'          => __('cms::users.fields.first_name.help'),
                            'value'         => old('first_name', $model->first_name),
                            'required'      => false,
                            'inline'        => '3:9',
                            'maxlength'     => 20,
                        ]
                    ])
                    <input type="hidden" name="model" id="model" value="{{$model->id}}">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'            => 'lastName',
                            'name'          => 'last_name',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.last_name.label'),
                            'placeholder'   => __('cms::users.fields.last_name.placeholder'),
                            'help'          => __('cms::users.fields.last_name.help'),
                            'value'         => old('last_name', $model->last_name),
                            'required'      => false,
                            'inline'        => '3:9',
                            'maxlength'     => 20,
                        ]
                    ])
                    {{-- {{dd($model->sex)}} --}}
                    {{-- @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'sex',
                            'name'          => 'sex',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.sex.label'),
                            'placeholder'   => __('cms::users.fields.sex.placeholder'),
                            'help'          => __('cms::users.fields.sex.help'),
                            'data'          => $sexOptions,
                            'selected'      => old('sex',  $model->sex),
                            'value'         => function($data, $key, $value){ return $key; },
                            'text'          => function($data, $key, $value){ return __($value['label']); },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $key; },
                            'required'      => false,
                            'searchable'    => false,
                            'inline'        => '3:9',
                        ]
                    ]) --}}
                    {{-- @include('cms::components.inputs.textarea', [
                        'options' => [
                            'id'            => 'address',
                            'name'          => 'address',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.address.label'),
                            'placeholder'   => __('cms::users.fields.address.placeholder'),
                            'help'          => __('cms::users.fields.address.help'),
                            'value'         => old('address', $model->address),
                            'required'      => false,
                            'inline'        => '3:9',
                            'maxlength'     => 256,
                        ]
                    ])
                    @include('cms::components.inputs.dropzone', [
                        'options' => [
                            'id'            => 'attachments',
                            'name'          => 'attachments',
                            'url'           => route('AttachmentController@store'),
                            'form_id'       => 'addNewForm',
                            'label'         => __('cms::users.fields.attachments.label'),
                            'placeholder'   => __('cms::users.fields.attachments.placeholder'),
                            'help'          => __('cms::users.fields.attachments.help'),
                            'attachments'   => [],
                            'selected'      => old('attachments', 'UNSPECIFIED'),
                            'required'      => false,
                            'searchable'    => false,
                            'inline'        => '3:9',
                        ]
                    ]) --}}
                </div>
            </div>
        </div>
    </div>
</div>

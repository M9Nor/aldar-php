<div class="kt-section kt-section--first">
    <div class="kt-wizard-v4__form">
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                <div class="kt-section__body">
                    <div class="form-group row">
                        <div class="col-lg-9 col-xl-6">
                            <h4 class="kt-section__title kt-section__title-sm kt-font-bolder">{{ __('cms::users.sections.account.description') }}</h4>
                        </div>
                    </div>
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'multiple'      => true,
                            'id'            => 'roles',
                            'name'          => 'roles[]',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.roles.label'),
                            'placeholder'   => __('cms::users.fields.roles.placeholder'),
                            'help'          => __('cms::users.fields.roles.help'),
                            'data'          => $roles,
                            'selected'      => old('roles', 'CLIENT'),
                            'value'         => function($data, $key, $value){ return $value->name; },
                            'text'          => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                            'sub_text'      => function($data, $key, $value){ return $value->translateOrFirst()->description; },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $value->name; },
                            'required'      => true,
                            'searchable'    => true,
                            'inline'        => '3:9',
                        ]
                    ])
                    {{-- {{  dd($model->status)}} --}}
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'statuses',
                            'name'          => 'status',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.statuses.label'),
                            'placeholder'   => __('cms::users.fields.statuses.placeholder'),
                            'help'          => __('cms::users.fields.statuses.help'),
                            'data'          => $statuses,
                            'selected'      => old('status'),
                            'value'         => function($data, $key, $value){ return $key; },
                            'text'          => function($data, $key, $value){ return __($value['label']); },
                            'select'        => function($data, $selected, $key, $value){ return $selected == $key; },
                            'required'      => false,
                            'searchable'    => false,
                            'inline'        => '3:9',
                        ]
                    ])
                    <div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'username',
                            'name'              => 'username',
                            'type'              => 'text',
                            'label'             => __('cms::users.fields.username.label'),
                            'placeholder'       => __('cms::users.fields.username.placeholder'),
                            'help'              => __('cms::users.fields.username.help'),
                            'value'             => old('username'),
                            'required'          => true,
                            'inline'            => '3:9',
                            'maxlength'         => 191,
                            'status_removal'    => false
                        ]
                    ])
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'phone',
                            'name'              => 'phone',
                            'type'              => 'text',
                            'label'             => __('cms::users.fields.phone.label'),
                            'placeholder'       => __('cms::users.fields.phone.placeholder'),
                            'help'              => __('cms::users.fields.phone.help'),
                            'value'             => old('phone'),
                            'required'          => false,
                            'inline'            => '3:9',
                            'maxlength'         => 20,
                            'direction'         => 'ltr',
                            'status_removal'    => false
                        ]
                    ])
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'            => 'email',
                            'name'          => 'email',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.email.label'),
                            'placeholder'   => __('cms::users.fields.email.placeholder'),
                            'help'          => __('cms::users.fields.email.help'),
                            'value'         => old('email'),
                            'required'      => true,
                            'inline'        => '3:9',
                            'maxlength'     => 256,
                            'direction'     => 'ltr'
                        ]
                    ])
                    <div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'            => 'password',
                            'name'          => 'password',
                            'type'          => 'password',
                            'label'         => __('cms::users.fields.password.label'),
                            'placeholder'   => __('cms::users.fields.password.placeholder'),
                            'help'          => __('cms::users.fields.password.help'),
                            'value'         => old('password'),
                            'required'      => true,
                            'inline'        => '3:9',
                            'showable'      => true,
                            'maxlength'     => 20,
                        ]
                    ])
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'            => 'password_confirmation',
                            'name'          => 'password_confirmation',
                            'type'          => 'password',
                            'label'         => __('cms::users.fields.password_confirmation.label'),
                            'placeholder'   => __('cms::users.fields.password_confirmation.placeholder'),
                            'help'          => __('cms::users.fields.password_confirmation.help'),
                            'value'         => old('password_confirmation'),
                            'required'      => true,
                            'inline'        => '3:9',
                            'showable'      => false,
                            'maxlength'     => 20,
                        ]
                    ])                 
                    {{-- @include('cms::components.inputs.select2', [
                        'options' => [
                            'id'                => 'users_test',
                            'name'              => 'users_test[]',
                            'label'             => 'test label',
                            'placeholder'       => 'test placeholder',
                            'help'              => 'test help',
                            'selected'          => [],
                            'required'          => true,
                            // 'selected'          => $products->map(function($product) {
                            //     return $product->formAjaxArray();
                            // }),                                                                                     
                            'multiple'          => true,
                            'clear_button'      => true,
                            'url'               => route('UserController@getUsersSelect2'),
                            'items_per_page'    => 20,
                            'dir'               => 'rtl',
                            'inline'        => '3:9',
                        ]
                    ]) --}}
                    {{-- @include('cms::components.inputs.dateTimePicker', [
                        'options' => [
                            'name'          => 'published_at',
                            'label'         => 'test label',
                            'help'          => 'test help',
                            'placeholder'   => 'test placeholder',
                            'value'         => old('published_at', \Carbon\Carbon::now()->format('Y/m/d H:i')),
                            'format'        => 'yyyy/mm/dd hh:ii',
                            'dir'           => 'rtl',
                            'inline'        => '3:9',
                        ]
                    ])
                    @include('cms::components.inputs.datePicker', [
                        'options' => [
                            'name'          => 'asdasd',
                            'label'         => 'test label',
                            'help'          => 'test help',
                            'placeholder'   => 'test placeholder',
                            'value'         => old('awd', \Carbon\Carbon::now()->format('Y/m/d')),
                            'format'        => 'yyyy/mm/dd',
                            'dir'           => 'rtl',
                            'inline'        => '3:9',
                        ]
                    ]) --}}
                    {{-- @include('cms::components.inputs.tinymce', [
                        'options' => [
                            'id'          => 'description',
                            'name'        => 'description',
                            'label'       => 'test label',
                            'placeholder' => 'test placeholder',
                            'help'        => 'test help',
                            'rows'        => 9,
                            'value'       => old('description'),
                            'base_script' => true,
                            'required'      => true,
                            'inline'        => '3:9',
                        ]
                    ]) --}}
                    {{-- <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div> --}}
                    {{-- @include('cms::components.inputs.file', [
                        'options' => [
                            'id'            => 'file',
                            'name'          => 'file',
                            'type'          => 'file',
                            'label'         => 'file label',
                            'placeholder'   => 'file placeholder',
                            'help'          => 'file help',
                            'value'         => old('file'),
                            'required'      => true,
                            'inline'        => '3:9',
                        ]
                    ]) --}}
                </div>
            </div>
        </div>
    </div>
</div>

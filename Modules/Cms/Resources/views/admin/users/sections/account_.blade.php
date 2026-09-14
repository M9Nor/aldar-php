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
                 {{-- {{ dd($model->roles->pluck('name'))}} --}}
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
                            'selected'      => old('roles', $model->roles->pluck('name')->toArray()),
                            'value'         => function($data, $key, $value){ return $value->name; },
                            'text'          => function($data, $key, $value){ return $value->translateOrFirst()->title; },
                            'sub_text'      => function($data, $key, $value){ return $value->translateOrFirst()->description; },
                            'select'        => function($data, $selected, $key, $value){ return in_array($value->name, $selected); },
                            'required'      => true,
                            'searchable'    => true,
                            'inline'        => '3:9',
                        ]
                    ])
                    @include('cms::components.inputs.select', [
                        'options' => [
                            'id'            => 'statuses',
                            'name'          => 'status',
                            'type'          => 'text',
                            'label'         => __('cms::users.fields.statuses.label'),
                            'placeholder'   => __('cms::users.fields.statuses.placeholder'),
                            'help'          => __('cms::users.fields.statuses.help'),
                            'data'          => $statuses,
                            'selected'      => old('status',$model->status),
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
                            'value'             => old('username', $model->username),
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
                            'value'             => old('phone', $model->phone),
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
                            'value'         => old('email', $model->email),
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
                </div>
            </div>
        </div>
    </div>
</div>

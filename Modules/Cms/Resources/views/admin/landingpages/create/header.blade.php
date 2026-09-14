<div class="kt-section kt-section--first">
    <div class="row">
        <div class="col-md-12">
            <div class="kt-wizard-v4__form">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="kt-section__body">
                            <div class="kt-section__content">
                                <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-danger" role="tablist">
                                    @foreach($supportedLangs as $locale => $properties)
                                        <li class="nav-item">
                                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" hreflang="{{ $locale }}" href="#tab_header_{{ $locale }}" role="tab">
                                                <img width="25" height="18" src="{{ Module::asset('cms:flags/' . $locale . '.svg') }}" alt="" /> {{ $properties['native'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" style="margin-top: 25px;">
                                    @foreach($supportedLangs as $locale => $properties)
                                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_header_{{ $locale }}" role="tabpanel">
                                            @include('cms::components.inputs.image', [
                                                'options' => [
                                                    'name'          => 'header_logo_'.$locale,
                                                    'id'            => 'header_logo_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.header_logo.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.header_logo.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.header_logo.help', [
                                                        'prefered_dimensions' => '1150x598',
                                                        'mimes'               => 'png | jpeg'
                                                    ]),
                                                    'default'       => old('header_logo_'.$locale, (new Modules\Cms\Entities\LandingPageTranslation)->headerLogo('150x150')),
                                                    'required'      => true,
                                                    // 'inline'        => '3:9',
                                                    'browse'        => __('cms::cruds.contents.image.add_text'),
                                                    'remove'        => __('cms::cruds.contents.image.remove_text'),
                                                    // 'width'         => '100px',
                                                    // 'height'        => '50px',
                                                ]
                                            ])
                                            @include('cms::components.inputs.image', [
                                                'options' => [
                                                    'name'          => 'header_background_'.$locale,
                                                    'id'            => 'header_background_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.header_background.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.header_background.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.header_background.help', [
                                                        'prefered_dimensions' => '1150x598',
                                                        'mimes'               => 'png | jpeg'
                                                    ]),
                                                    'default'       => old('header_background_'.$locale, (new Modules\Cms\Entities\LandingPageTranslation)->headerBackground('400x400')),
                                                    'required'      => true,
                                                    // 'inline'        => '3:9',
                                                    'browse'        => __('cms::cruds.contents.image.add_text'),
                                                    'remove'        => __('cms::cruds.contents.image.remove_text'),
                                                    // 'width'         => '500px',
                                                    // 'height'        => '300px',
                                                ]
                                            ])
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'header_h1_'.$locale,
                                                    'name'          => 'header_h1_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.header_h1.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.header_h1.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.header_h1.help'),
                                                    'value'         => old('header_h1.' . $locale),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'header_h2_'.$locale,
                                                    'name'          => 'header_h2_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.header_h2.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.header_h2.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.header_h2.help'),
                                                    'value'         => old('header_h2.' . $locale),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'header_h1_above_'.$locale,
                                                    'name'          => 'header_h1_above_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.header_h1_above.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.header_h1_above.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.header_h1_above.help'),
                                                    'value'         => old('header_h1_above.' . $locale),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'header_h2_above_'.$locale,
                                                    'name'          => 'header_h2_above_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.header_h2_above.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.header_h2_above.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.header_h2_above.help'),
                                                    'value'         => old('header_h2_above.' . $locale),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'header_h1_under_'.$locale,
                                                    'name'          => 'header_h1_under_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.header_h1_under.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.header_h1_under.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.header_h1_under.help'),
                                                    'value'         => old('header_h1_under.' . $locale),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'header_h2_under_'.$locale,
                                                    'name'          => 'header_h2_under_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.header_h2_under.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.header_h2_under.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.header_h2_under.help'),
                                                    'value'         => old('header_h2_under.' . $locale),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                                <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'form',
                                                'name'          => 'form',
                                                'type'          => 'text',
                                                'label'         => __('cms::landing_page.fields.form.label'),
                                                'placeholder'   => __('cms::landing_page.fields.form.placeholder'),
                                                'help'          => __('cms::landing_page.fields.form.help'),
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
                                                'selected'      => old('form','no'),
                                                'value'         => function($data, $key, $value){ return $value['value']; },
                                                'text'          => function($data, $key, $value){ return $value['text']; },
                                                'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                                                'required'      => false,
                                                'searchable'    => false,
                                                'multiple'      => false,
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'form_direction',
                                                'name'          => 'form_direction',
                                                'type'          => 'text',
                                                'label'         => __('cms::landing_page.fields.form_direction.label'),
                                                'placeholder'   => __('cms::landing_page.fields.form_direction.placeholder'),
                                                'help'          => __('cms::landing_page.fields.form_direction.help'),
                                                'data'          => [
                                                    [
                                                        'text'      => __('cms::landing_page.right'),
                                                        'value'     => 'right',
                                                    ],
                                                    [
                                                        'text'      => __('cms::landing_page.left'),
                                                        'value'     => 'left',
                                                    ],
                                                ],
                                                'selected'      => old('form_direction','right'),
                                                'value'         => function($data, $key, $value){ return $value['value']; },
                                                'text'          => function($data, $key, $value){ return $value['text']; },
                                                'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                                                'required'      => false,
                                                'searchable'    => false,
                                                'multiple'      => false,
                                            ]
                                        ])
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-group kt-form__group ">
                                            <label for="example-color-input" class="">
                                                <strong class="text-focus">السلاغ <span class="text-danger">*</span></strong>
                                            </label>
                                            <div class="">
                                                <div class="input-group">
                                                    <input name="slug" type="color" class="form-control" placeholder="ادخل نص انكليزي بحروف صغيرة فقط" value="#563d7c" id="example-color-input">
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'sort_order',
                                                'name'              => 'sort_order',
                                                'type'              => 'text',
                                                'label'             => __('cms::landing_page.fields.sort_order.label'),
                                                'placeholder'       => __('cms::landing_page.fields.sort_order.placeholder'),
                                                'help'              => __('cms::landing_page.fields.sort_order.help'),
                                                'value'             => old('sort_order'),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
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
</div>

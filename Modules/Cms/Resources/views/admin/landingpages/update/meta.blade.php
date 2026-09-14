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
                                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" hreflang="{{ $locale }}" href="#tab_meta_{{ $locale }}" role="tab">
                                                <img width="25" height="18" src="{{ Module::asset('cms:flags/' . $locale . '.svg') }}" alt="" /> {{ $properties['native'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" style="margin-top: 25px;">
                                    @foreach($supportedLangs as $locale => $properties)
                                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_meta_{{ $locale }}" role="tabpanel">
                                            @include('cms::components.inputs.image', [
                                                'options' => [
                                                    'name'          => 'meta_img_'.$locale,
                                                    'id'            => 'meta_img_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.meta_img.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.meta_img.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.meta_img.help', [
                                                        'prefered_dimensions' => '1150x598',
                                                        'mimes'               => 'png | jpeg'
                                                    ]),
                                                    'default'       => old('meta_img_'.$locale,$model->translate($locale) == NULL ? (new Modules\Cms\Entities\LandingPageTranslation)->getMetaImage('400x200') : $model->translate($locale)->getMetaImage('400x200') ),
                                                    'required'      => true,
                                                    // 'inline'        => '3:9',
                                                    'browse'        => __('cms::cruds.contents.image.add_text'),
                                                    'remove'        => __('cms::cruds.contents.image.remove_text'),
                                                    // 'width'         => '400px',
                                                    // 'height'        => '200px',
                                                ]
                                            ])
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'meta_title_'.$locale,
                                                    'name'          => 'meta_title_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.meta_title.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.meta_title.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.meta_title.help'),
                                                    'value'         => old('meta_title_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->meta_title),
                                                    'required'      => true,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                            @include('cms::components.inputs.textarea', [
                                                'options' => [
                                                    'id'                => 'meta_keywords_'.$locale,
                                                    'name'              => 'meta_keywords_'.$locale,
                                                    'type'              => 'text',
                                                    'label'             => __('cms::landing_page.fields.meta_keywords.label'),
                                                    'placeholder'       => __('cms::landing_page.fields.meta_keywords.placeholder'),
                                                    'help'              => __('cms::landing_page.fields.meta_keywords.help'),
                                                    'value'             => old('meta_keywords_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->meta_keywords),
                                                    'required'          => false,
                                                    'inline'            => false,
                                                    'direction'         => 'ltr',
                                                    'status_removal'    => false
                                                ]
                                            ])
                                            @include('cms::components.inputs.textarea', [
                                                'options' => [
                                                    'id'                => 'meta_desc_'.$locale,
                                                    'name'              => 'meta_desc_'.$locale,
                                                    'type'              => 'text',
                                                    'label'             => __('cms::landing_page.fields.meta_desc.label'),
                                                    'placeholder'       => __('cms::landing_page.fields.meta_desc.placeholder'),
                                                    'help'              => __('cms::landing_page.fields.meta_desc.help'),
                                                    'value'             => old('meta_desc_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->meta_desc),
                                                    'required'          => false,
                                                    'inline'            => false,
                                                    'direction'         => 'ltr',
                                                    'status_removal'    => false
                                                ]
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                                <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'slug',
                                                'name'              => 'slug',
                                                'type'              => 'text',
                                                'label'             => __('cms::landing_page.fields.slug.label'),
                                                'placeholder'       => __('cms::landing_page.fields.slug.placeholder'),
                                                'help'              => __('cms::landing_page.fields.slug.help'),
                                                'value'             => old('slug',$model->slug),
                                                'required'          => true,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'main_color',
                                                'name'              => 'main_color',
                                                'type'              => 'text',
                                                'label'             => __('cms::landing_page.fields.main_color.label'),
                                                'placeholder'       => __('cms::landing_page.fields.main_color.placeholder'),
                                                'help'              => __('cms::landing_page.fields.main_color.help'),
                                                'value'             => old('main_color',$model->main_color),
                                                'required'          => true,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'second_color',
                                                'name'              => 'second_color',
                                                'type'              => 'text',
                                                'label'             => __('cms::landing_page.fields.second_color.label'),
                                                'placeholder'       => __('cms::landing_page.fields.second_color.placeholder'),
                                                'help'              => __('cms::landing_page.fields.second_color.help'),
                                                'value'             => old('second_color',$model->second_color),
                                                'required'          => true,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
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

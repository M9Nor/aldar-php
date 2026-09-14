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
                                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" hreflang="{{ $locale }}" href="#tab_subject_text_{{ $locale }}" role="tab">
                                                <img width="25" height="18" src="{{ Module::asset('cms:flags/' . $locale . '.svg') }}" alt="" /> {{ $properties['native'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" style="margin-top: 25px;">
                                    @foreach($supportedLangs as $locale => $properties)
                                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_subject_text_{{ $locale }}" role="tabpanel">
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'subject_text_h2_'.$locale,
                                                    'name'          => 'subject_text_h2_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.subject_text_h2.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.subject_text_h2.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.subject_text_h2.help'),
                                                    'value'         => old('subject_text_h2_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->subject_text_h2),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @php
                                                        if($loop->first){
                                                            $baseScript = true;
                                                        }else{
                                                            $baseScript = false;
                                                        }
                                                    @endphp
                                                    @include('cms::components.inputs.tinymce', [
                                                        'options' => [
                                                            'id'                => 'subject_text_desc_'.$locale,
                                                            'name'              => 'subject_text_desc_'.$locale,
                                                            'label'             => __('cms::landing_page.fields.subject_text_desc.label'),
                                                            'placeholder'       => __('cms::landing_page.fields.subject_text_desc.placeholder'),
                                                            'help'              => __('cms::landing_page.fields.subject_text_desc.help'),
                                                            'value'             => old('subject_text_desc_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->subject_text_desc),
                                                            'rows'              => 6,
                                                            'base_script'       => $baseScript,
                                                            'required'          => false,
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'subject_text',
                                                'name'          => 'subject_text',
                                                'type'          => 'text',
                                                'label'         => __('cms::landing_page.fields.subject_text.label'),
                                                'placeholder'   => __('cms::landing_page.fields.subject_text.placeholder'),
                                                'help'          => __('cms::landing_page.fields.subject_text.help'),
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
                                                'selected'      => old('subject_text',$model->subject_text),
                                                'value'         => function($data, $key, $value){ return $value['value']; },
                                                'text'          => function($data, $key, $value){ return $value['text']; },
                                                'select'        => function($data, $selected, $key, $value){ return $selected == $value['value']; },
                                                'required'      => true,
                                                'searchable'    => false,
                                                'multiple'      => false,
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'subject_text_background',
                                                'name'              => 'subject_text_background',
                                                'type'              => 'text',
                                                'label'             => __('cms::landing_page.fields.subject_text_background.label'),
                                                'placeholder'       => __('cms::landing_page.fields.subject_text_background.placeholder'),
                                                'help'              => __('cms::landing_page.fields.subject_text_background.help'),
                                                'value'             => old('subject_text_background',$model->subject_text_background),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
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

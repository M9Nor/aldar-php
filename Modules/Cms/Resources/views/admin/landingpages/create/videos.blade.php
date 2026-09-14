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
                                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" hreflang="{{ $locale }}" href="#tab_videos_{{ $locale }}" role="tab">
                                                <img width="25" height="18" src="{{ Module::asset('cms:flags/' . $locale . '.svg') }}" alt="" /> {{ $properties['native'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" style="margin-top: 25px;">
                                    @foreach($supportedLangs as $locale => $properties)
                                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_videos_{{ $locale }}" role="tabpanel">
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'videos_title_'.$locale,
                                                    'name'          => 'videos_title_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.fields.videos_title.label'),
                                                    'placeholder'   => __('cms::landing_page.fields.videos_title.placeholder'),
                                                    'help'          => __('cms::landing_page.fields.videos_title.help'),
                                                    'value'         => old('videos_title.' . $locale),
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
                                                'id'            => 'videos',
                                                'name'          => 'videos',
                                                'type'          => 'text',
                                                'label'         => __('cms::landing_page.fields.videos.label'),
                                                'placeholder'   => __('cms::landing_page.fields.videos.placeholder'),
                                                'help'          => __('cms::landing_page.fields.videos.help'),
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
                                                'selected'      => old('videos','no'),
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
                                                'id'                => 'videos_link',
                                                'name'              => 'videos_link',
                                                'type'              => 'text',
                                                'label'             => __('cms::landing_page.fields.videos_link.label'),
                                                'placeholder'       => __('cms::landing_page.fields.videos_link.placeholder'),
                                                'help'              => __('cms::landing_page.fields.videos_link.help'),
                                                'value'             => old('videos_link'),
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

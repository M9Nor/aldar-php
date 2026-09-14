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
                                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" hreflang="{{ $locale }}" href="#tab_{{ $locale }}" role="tab">
                                                <img width="25" height="18" src="{{ Module::asset('cms:flags/' . $locale . '.svg') }}" alt="" /> {{ $properties['native'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" style="margin-top: 25px;">
                                    @foreach($supportedLangs as $locale => $properties)
                                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_{{ $locale }}" role="tabpanel">
                                            @include('cms::components.inputs.image', [
                                                'options' => [
                                                    'id'            => 'image_'.$locale,
                                                    'name'          => 'image_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('backend::projects.fields.image.label'),
                                                    'placeholder'   => __('backend::projects.fields.image.placeholder'),
                                                    'help'          => __('backend::projects.fields.image.help', [
                                                        'prefered_dimensions' => '1000x750',
                                                        'mimes'               => 'png | jpeg'
                                                    ]),
                                                    'default'       => old('image_'.$locale,$model->translate($locale) == NULL ? (new Modules\Backend\Entities\ProjectTranslation)->getImage('500x375') : $model->translate($locale)->getImage('500x375') ),
                                                    'required'      => true,
                                                    // 'inline'        => '3:9',
                                                    'browse'        => __('cms::cruds.contents.image.add_text'),
                                                    'remove'        => __('cms::cruds.contents.image.remove_text'),
                                                    'width'         => '500px',
                                                    'height'        => '375px',
                                                ]
                                            ])
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'title_'.$locale,
                                                    'name'          => 'title_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('backend::opportunity.fields.title.label'),
                                                    'placeholder'   => __('backend::opportunity.fields.title.placeholder'),
                                                    'help'          => __('backend::opportunity.fields.title.help'),
                                                    'value'         => old('title_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->title),
                                                    'required'      => true,
                                                    'inline'        => false,
                                                    'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                ]
                                            ])
                                            @if(auth()->user()->can('company_and_project', \Modules\Backend\Entities\Project::class))
                                                @include('cms::components.inputs.text', [
                                                    'options' => [
                                                        'id'            => 'second_title_'.$locale,
                                                        'name'          => 'second_title_'.$locale,
                                                        'type'          => 'text',
                                                        'label'         => __('backend::opportunity.fields.second_title.label'),
                                                        'placeholder'   => __('backend::opportunity.fields.second_title.placeholder'),
                                                        'help'          => __('backend::opportunity.fields.second_title.help'),
                                                        'value'         => old('second_title_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->second_title),
                                                        'required'      => false,
                                                        'inline'        => false,
                                                        'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                    ]
                                                ])
                                            @endif
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'tab_title_'.$locale,
                                                    'name'          => 'tab_title_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('backend::opportunity.fields.tab_title.label'),
                                                    'placeholder'   => __('backend::opportunity.fields.tab_title.placeholder'),
                                                    'help'          => __('backend::opportunity.fields.tab_title.help'),
                                                    'value'         => old('tab_title_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->tab_title),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                ]
                                            ])
                                            @include('cms::components.inputs.textarea', [
                                                'options' => [
                                                    'id'                => 'brief_'.$locale,
                                                    'name'              => 'brief_'.$locale,
                                                    'type'              => 'text',
                                                    'label'             => __('backend::opportunity.fields.brief.label'),
                                                    'placeholder'       => __('backend::opportunity.fields.brief.placeholder'),
                                                    'help'              => __('backend::opportunity.fields.brief.help'),
                                                    'value'             => old('brief_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->brief),
                                                    'required'          => false,
                                                    'inline'            => false,
                                                    'direction'         => 'ltr',
                                                    'status_removal'    => false
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
                                                            'id'                => 'about_'.$locale,
                                                            'name'              => 'about_'.$locale,
                                                            'label'             => __('backend::opportunity.summary'),
                                                            'placeholder'       => __('backend::opportunity.fields.about.placeholder'),
                                                            'help'              => __('backend::opportunity.fields.about.help'),
                                                            'rows'              => 6,
                                                            'value'             => old('about_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->about),
                                                            'base_script'       => $baseScript,
                                                            'required'          => true,
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                           <!--  <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'            => 'details_'.$locale,
                                                            'name'          => 'details_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => __('backend::opportunity.fields.details.label'),
                                                            'placeholder'   => __('backend::opportunity.fields.details.placeholder'),
                                                            'help'          => __('backend::opportunity.fields.details.help'),
                                                            'value'         => old('details_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->details),
                                                            'required'      => false,
                                                            'rows'          => 4,
                                                            'inline'        =>  false,
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            </div> -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'                => 'seo_description_'.$locale,
                                                            'name'              => 'seo_description_'.$locale,
                                                            'type'              => 'text',
                                                            'label'             => __('backend::opportunity.fields.seo_description.label'),
                                                            'placeholder'       => __('backend::opportunity.fields.seo_description.placeholder'),
                                                            'help'              => __('backend::opportunity.fields.seo_description.help'),
                                                            'value'             => old('seo_description_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->seo_description),
                                                            'required'          => false,
                                                            'inline'            => false,
                                                            'direction'         => 'ltr',
                                                            'status_removal'    => false
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.textarea', [
                                                        'options' => [
                                                            'id'                => 'trans_keywords_'.$locale,
                                                            'name'              => 'trans_keywords_'.$locale,
                                                            'type'              => 'text',
                                                            'label'             => __('backend::opportunity.fields.trans_keywords.label'),
                                                            'placeholder'       => __('backend::opportunity.fields.trans_keywords.placeholder'),
                                                            'help'              => __('backend::opportunity.fields.trans_keywords.help'),
                                                            'value'             => old('trans_keywords_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->trans_keywords),
                                                            'required'          => false,
                                                            'inline'            => false,
                                                            'direction'         => 'ltr',
                                                            'status_removal'    => false
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'                => 'video_'.$locale,
                                                            'name'              => 'video_'.$locale,
                                                            'type'              => 'text',
                                                            'label'             => __('backend::opportunity.fields.video.label'),
                                                            'placeholder'       => __('backend::opportunity.fields.video.placeholder'),
                                                            'help'              => __('backend::opportunity.fields.video.help'),
                                                            'value'             => old('video_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->video),
                                                            'required'          => false,
                                                            'inline'            => false,
                                                            'maxlength'         => 191,
                                                            'direction'         => 'ltr',
                                                            'status_removal'    => false
                                                        ]
                                                    ])
                                                </div>
                                                <div class="col-md-6">
                                                    @include('cms::components.inputs.image', [
                                                        'options' => [
                                                            'id'            => 'project_image1_'.$locale,
                                                            'name'          => 'project_image1_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => __('backend::projects.fields.project_image1.label'),
                                                            'placeholder'   => __('backend::projects.fields.project_image1.placeholder'),
                                                            'help'          => __('backend::projects.fields.project_image1.help', [
                                                                'prefered_dimensions' => '698x500',
                                                                'mimes'               => 'png | jpeg'
                                                            ]),
                                                            'default'       => old('project_image1_'.$locale,$model->translate($locale) == NULL ? (new Modules\Backend\Entities\ProjectTranslation)->getImageForVideo1('349x250') : $model->translate($locale)->getImageForVideo1('349x250') ),
                                                            'required'      => false,
                                                            'browse'        => __('backend::projects.fields.project_image1.add_text'),
                                                            'remove'        => __('backend::projects.fields.project_image1.remove_text'),
                                                            'width'         => '349px',
                                                            'height'        => '250px',
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                           <!--  <div class="row">
                                                <div class="col-md-6">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'                => 'video2_'.$locale,
                                                            'name'              => 'video2_'.$locale,
                                                            'type'              => 'text',
                                                            'label'             => __('backend::projects.fields.video2.label'),
                                                            'placeholder'       => __('backend::projects.fields.video2.placeholder'),
                                                            'help'              => __('backend::projects.fields.video2.help'),
                                                            'value'             => old('video2_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->video2),
                                                            'required'          => false,
                                                            'inline'            => false,
                                                            'maxlength'         => 191,
                                                            'direction'         => 'ltr',
                                                            'status_removal'    => false
                                                        ]
                                                    ])
                                                </div>
                                                <div class="col-md-6">
                                                    @include('cms::components.inputs.image', [
                                                        'options' => [
                                                            'id'            => 'project_image2_'.$locale,
                                                            'name'          => 'project_image2_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => __('backend::projects.fields.project_image2.label'),
                                                            'placeholder'   => __('backend::projects.fields.project_image2.placeholder'),
                                                            'help'          => __('backend::projects.fields.project_image2.help', [
                                                                'prefered_dimensions' => '698x500',
                                                                'mimes'               => 'png | jpeg'
                                                            ]),
                                                            'default'       => old('project_image2_'.$locale,$model->translate($locale) == NULL ? (new Modules\Backend\Entities\ProjectTranslation)->getImageForVideo2('349x250') : $model->translate($locale)->getImageForVideo2('349x250') ),
                                                            'required'      => false,
                                                            'browse'        => __('backend::projects.fields.project_image2.add_text'),
                                                            'remove'        => __('backend::projects.fields.project_image2.remove_text'),
                                                            'width'         => '349px',
                                                            'height'        => '250px',
                                                        ]
                                                    ])
                                                </div>
                                            </div> -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'                => 'delivery_date_'.$locale,
                                                            'name'              => 'delivery_date_'.$locale,
                                                            'type'              => 'text',
                                                            'label'             => __('backend::opportunity.fields.delivery_date.label'),
                                                            'placeholder'       => __('backend::opportunity.fields.delivery_date.placeholder'),
                                                            'help'              => __('backend::opportunity.fields.delivery_date.help'),
                                                            'value'             => old('delivery_date_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->delivery_date),
                                                            'required'          => false,
                                                            'inline'            => false,
                                                            'maxlength'         => 191,
                                                            'direction'         => 'ltr',
                                                            'status_removal'    => false
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                         
                                           <!--  <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.tinymce', [
                                                        'options' => [
                                                            'id'            => 'description_'.$locale,
                                                            'name'          => 'description_'.$locale,
                                                            'label'         => __('backend::opportunity.fields.description.label'),
                                                            'placeholder'   => __('backend::opportunity.fields.description.placeholder'),
                                                            'help'          => __('backend::opportunity.fields.description.help'),
                                                            'rows'          => 6,
                                                            'value'         => old('description_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->description),
                                                            'base_script'   => false,
                                                            'required'      => false,
                                                        ]
                                                    ])
                                                </div>
                                            </div> -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.taggable', [
                                                        'options' => [
                                                            'id'            => 'tags_'.$locale,
                                                            'name'          => 'tags_'.$locale.'[]',
                                                            'label'         => __('backend::opportunity.fields.tags.label'),
                                                            'placeholder'   => __('backend::opportunity.fields.tags.placeholder'),
                                                            'help'          => __('backend::opportunity.fields.tags.help'),
                                                            'locale'        => $locale,
                                                            'data'          => $model->tags->filter(function($item) use ($locale) {
                                                                return $item->translations->pluck('locale')->contains($locale);
                                                            }),
                                                            'selected'      => $model->tags->filter(function($item) use ($locale) {
                                                                return $item->translations->pluck('locale')->contains($locale);
                                                            })->pluck('id')->toArray(),
                                                            'value'         => function($data, $key, $value){ return $value->id; },
                                                            'text'          => function($data, $key, $value) use ($locale) { return $value->translate($locale)->text; },
                                                            'select'        => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                         <!--    <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.text', [
                                                        'options' => [
                                                            'id'            => 'landing_page_title_'.$locale,
                                                            'name'          => 'landing_page_title_'.$locale,
                                                            'type'          => 'text',
                                                            'label'         => __('backend::opportunity.fields.landing_page_title.label'),
                                                            'placeholder'   => __('backend::opportunity.fields.landing_page_title.placeholder'),
                                                            'help'          => __('backend::opportunity.fields.landing_page_title.help'),
                                                            'value'         => old('landing_page_title_' . $locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->landing_page_title),
                                                            'required'      => false,
                                                            'inline'        => false,
                                                            'direction'     => $locale == 'ar' ? 'rtl' : 'ltr'
                                                        ]
                                                    ])
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('cms::components.inputs.tinymce', [
                                                        'options' => [
                                                            'id'            => 'landing_page_desc_'.$locale,
                                                            'name'          => 'landing_page_desc_'.$locale,
                                                            'label'         => __('backend::opportunity.fields.landing_page_desc.label'),
                                                            'placeholder'   => __('backend::opportunity.fields.landing_page_desc.placeholder'),
                                                            'help'          => __('backend::opportunity.fields.landing_page_desc.help'),
                                                            'rows'          => 6,
                                                            'value'         => old('landing_page_desc_'.$locale,$model->translate($locale) == NULL ? NULL : $model->translate($locale)->landing_page_desc),
                                                            'base_script'   => false,
                                                            'required'      => false,
                                                        ]
                                                    ])
                                                </div>
                                            </div> -->
                                        </div>
                                    @endforeach
                                </div>
                                <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'code',
                                                'name'              => 'code',
                                                'type'              => 'text',
                                                'label'             => __('backend::opportunity.fields.code.label'),
                                                'placeholder'       => __('backend::opportunity.fields.code.placeholder'),
                                                'help'              => __('backend::opportunity.fields.code.help'),
                                                'value'             => old('code',$model->code),
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
                                                'id'                => 'slug',
                                                'name'              => 'slug',
                                                'type'              => 'text',
                                                'label'             => __('backend::opportunity.fields.slug.label'),
                                                'placeholder'       => __('backend::opportunity.fields.slug.placeholder'),
                                                'help'              => __('backend::opportunity.fields.slug.help'),
                                                'value'             => old('slug',$model->slug),
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
                                                'id'                => 'sort_order',
                                                'name'              => 'sort_order',
                                                'type'              => 'text',
                                                'label'             => __('backend::opportunity.fields.sort_order.label'),
                                                'placeholder'       => __('backend::opportunity.fields.sort_order.placeholder'),
                                                'help'              => __('backend::opportunity.fields.sort_order.help'),
                                                'value'             => old('sort_order',$model->sort_order),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                    @if(auth()->user()->can('company_and_project', \Modules\Backend\Entities\Project::class))
                                        <input type="hidden" name="company_and_project" value="yes">
                                        <div class="col-md-6">
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'                => 'link',
                                                    'name'              => 'link',
                                                    'type'              => 'text',
                                                    'label'             => __('backend::opportunity.fields.link.label'),
                                                    'placeholder'       => __('backend::opportunity.fields.link.placeholder'),
                                                    'help'              => __('backend::opportunity.fields.link.help'),
                                                    'value'             => old('link',$model->link),
                                                    'required'          => false,
                                                    'inline'            => false,
                                                    'maxlength'         => 255,
                                                    'status_removal'    => false
                                                ]
                                            ])
                                        </div>
                                    @endif
                                </div>
                                {{-- <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'pro_image',
                                                'name'              => 'pro_image',
                                                'type'              => 'text',
                                                'label'             => __('backend::opportunity.fields.pro_image.label'),
                                                'placeholder'       => __('backend::opportunity.fields.pro_image.placeholder'),
                                                'help'              => __('backend::opportunity.fields.pro_image.help'),
                                                'value'             => old('pro_image',$model->pro_image),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'direction'         => 'ltr',
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'pro_image2',
                                                'name'              => 'pro_image2',
                                                'type'              => 'text',
                                                'label'             => __('backend::opportunity.fields.pro_image2.label'),
                                                'placeholder'       => __('backend::opportunity.fields.pro_image2.placeholder'),
                                                'help'              => __('backend::opportunity.fields.pro_image2.help'),
                                                'value'             => old('pro_image2',$model->pro_image2),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'direction'         => 'ltr',
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                </div> --}}
                                {{-- <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'bulding_date',
                                                'name'              => 'bulding_date',
                                                'type'              => 'text',
                                                'label'             => __('backend::opportunity.fields.bulding_date.label'),
                                                'placeholder'       => __('backend::opportunity.fields.bulding_date.placeholder'),
                                                'help'              => __('backend::opportunity.fields.bulding_date.help'),
                                                'value'             => old('bulding_date',$model->establishment_date),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'direction'         => 'ltr',
                                                'status_removal'    => false
                                            ]
                                        ])
                                        @include('cms::components.inputs.dateTimePicker', [
                                            'options' => [
                                                'name'          => 'bulding_date',
                                                'label'         => __('backend::opportunity.fields.bulding_date.label'),
                                                'placeholder'   => __('backend::opportunity.fields.bulding_date.placeholder'),
                                                'help'          => __('backend::opportunity.fields.bulding_date.help'),
                                                'value'         => old('bulding_date',\Carbon\Carbon::now()->format('Y/m/d H:i')),
                                                'format'        => 'yyyy/mm/dd hh:ii',
                                                'dir'           => 'rtl',
                                                'required'      => true,
                                                // 'inline'        => '3:9',
                                            ]
                                        ])
                                    </div>
                                </div> --}}
                                <div class="row">
                                    <div class="col-md-6">
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

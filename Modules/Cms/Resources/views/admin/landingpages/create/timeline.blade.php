@push('styles')
    <style>
        [data-repeater-item]:last-child {
            border: unset;
        }
        [data-repeater-item] {
            border-bottom: 1px dashed #ebedf2;
            margin: 20px 0;
        }
    </style>
@endpush
@php
    use Modules\Cms\Entities\Timeline;
@endphp
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
                                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" hreflang="{{ $locale }}" href="#timeline_{{ $locale }}" role="tab">
                                                <img width="25" height="18" src="{{ Module::asset('cms:flags/' . $locale . '.svg') }}" alt="" /> {{ $properties['native'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" style="margin-top: 25px;">
                                    @foreach($supportedLangs as $locale => $properties)
                                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="timeline_{{ $locale }}" role="tabpanel">
                                            @include('cms::components.inputs.text', [
                                                'options' => [
                                                    'id'            => 'timeline_title_'.$locale,
                                                    'name'          => 'timeline_title_'.$locale,
                                                    'type'          => 'text',
                                                    'label'         => __('cms::landing_page.timeline.timeline_title.label'),
                                                    'placeholder'   => __('cms::landing_page.timeline.timeline_title.placeholder'),
                                                    'help'          => __('cms::landing_page.timeline.timeline_title.help'),
                                                    'value'         => old('timeline_title.' . $locale),
                                                    'required'      => false,
                                                    'inline'        => false,
                                                    'direction'     => LaravelLocalization::getCurrentLocaleDirection()
                                                ]
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="kt_repeater_2">
    <div class=" row">
        <div data-repeater-list="timeline" class="col-md-12">
            <div data-repeater-item class=" row align-items-center">
                <div class="col-md-4">
                    <div class="form-group kt-form__group">
                        <label for="language" class="">
                            <strong class="text-focus">
                                {{__('cms::landing_page.timeline.language.label')}}
                                <span class="text-danger">*</span>
                            </strong>
                        </label>
                        <div>
                            <select class="form-control" name="language" id="">
                                <option value="ar" selected>{{__('cms::landing_page.ar')}}</option>
                                <option value="en">{{__('cms::landing_page.en')}}</option>
                                <option value="fa">{{__('cms::landing_page.fa')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'title',
                            'name'              => 'title',
                            'type'              => 'text',
                            'label'             => __('cms::landing_page.timeline.title.label'),
                            'placeholder'       => __('cms::landing_page.timeline.title.placeholder'),
                            'help'              => __('cms::landing_page.timeline.title.help'),
                            'value'             => old('title'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
                            'direction'         => 'ltr',
                            'status_removal'    => false,
                        ]
                    ])
                </div>
                <div class="col-md-4">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'date',
                            'name'              => 'date',
                            'type'              => 'text',
                            'label'             => __('cms::landing_page.timeline.date.label'),
                            'placeholder'       => __('cms::landing_page.timeline.date.placeholder'),
                            'help'              => __('cms::landing_page.timeline.date.help'),
                            'value'             => old('date'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
                            'direction'         => 'ltr',
                            'status_removal'    => false,
                        ]
                    ])
                </div>
                <div class="col-md-8">
                    @include('cms::components.inputs.textarea', [
                        'options' => [
                            'id'                => 'description',
                            'name'              => 'description',
                            'label'             => __('cms::landing_page.timeline.description.label'),
                            'placeholder'       => __('cms::landing_page.timeline.description.placeholder'),
                            'help'              => __('cms::landing_page.timeline.description.help'),
                            'value'             => old('description'),
                            'required'          => true,
                            'inline'            => false,
                            'direction'         => 'ltr',
                            'status_removal'    => false,
                        ]
                    ])
                </div>
                <div class="col-md-4">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'sort_order',
                            'name'              => 'sort_order',
                            'type'              => 'number',
                            'label'             => __('cms::landing_page.timeline.sort_order.label'),
                            'placeholder'       => __('cms::landing_page.timeline.sort_order.placeholder'),
                            'help'              => __('cms::landing_page.timeline.sort_order.help'),
                            'value'             => old('sort_order'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
                            'direction'         => 'ltr',
                            'status_removal'    => false,
                        ]
                    ])
                </div>
                <div class="col-md-4">
                    @include('cms::components.inputs.text', [
                        'options' => [
                            'id'                => 'link',
                            'name'              => 'link',
                            'type'              => 'text',
                            'label'             => __('cms::landing_page.timeline.link.label'),
                            'placeholder'       => __('cms::landing_page.timeline.link.placeholder'),
                            'help'              => __('cms::landing_page.timeline.link.help'),
                            'value'             => old('link'),
                            'required'          => true,
                            'inline'            => false,
                            'maxlength'         => 191,
                            'direction'         => 'ltr',
                            'status_removal'    => false,
                        ]
                    ])
                </div>
                <div class="col-md-4">
                    <div class="form-group kt-form__group">
                        <label for="icon" class="">
                            <strong class="text-focus">
                                {{__('cms::landing_page.timeline.icon.label')}}
                            </strong>
                        </label>
                        <div>
                            <input type="file" name="icon" id="">
                        </div>
                        <span class="form-text text-muted">{{__('cms::landing_page.timeline.icon.help')}}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group kt-form__group">
                        <label for="image" class="">
                            <strong class="text-focus">
                                {{__('cms::landing_page.timeline.image.label')}}
                            </strong>
                        </label>
                        <div>
                            <input type="file" name="image" id="">
                        </div>
                        <span class="form-text text-muted">{{__('cms::landing_page.timeline.image.help')}}</span>
                    </div>
                </div>
                <div class="col-md-12" style="margin-bottom: 10px">
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                            <i class="la la-trash-o"></i>
                            {{__('cms::cruds.contents.external_attachments.delete')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class=" row">
        <div class="col-md-12">
           <div class="col-md-4">
                <a href="javascript:;" data-repeater-create="" class="btn btn-label-success btn-bold">
                    <i class="la la-plus"></i>{{__('backend::projects.fields.pay.add')}}
                </a>
            </div>
        </div>
    </div>
</div>


@extends('cms::layouts.master')

@section('title', __('cms::landing_page.landing_pages'))

@push('styles')
    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.rtl.css') }}" rel="stylesheet" type="text/css">
    @else
        <link href="{{ Module::asset('cms:metronic/css/pages/wizard/wizard-4.css') }}" rel="stylesheet" type="text/css">
    @endif
    <style>
        .select2-container--default{
            width: 100% !important;
        }
    </style>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('cms::landing_page.update'),
            'items' => [
                [
                    'label' => __('cms::landing_page.title'),
                    'link'  => route('LandingPageController@index')
                ], [
                    'label' => __('cms::landing_page.update'),
                    'link'  => 'javascript:;'
                ]
            ]
        ]
    ])
    @slot('main')
    @endslot
        @slot('toolbar')
            <a href="{{ url()->previous() }}" class="btn btn-default btn-bold">
                {{ __('cms::global.back') }}
            </a>
            <div class="btn-group">
                <a href="javascript:;" data-redirect-url="javascript:;" class="submit_form btn btn-success btn-bold">
                    {{ __('cms::global.submit') }}
                </a>
                <button type="button" class="btn btn-success btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu dropdown-menu-{{ $langDirection == 'rtl' ? 'left' : 'right' }}">
                    <ul class="kt-nav">
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('LandingPageController@create') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('LandingPageController@index') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-indent-dots"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_exit') }}</span> <!-- Save &amp; exit -->
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        @endslot
    @endcomponent
@endsection
@section('content')
    <div class="kt-portlet kt-portlet--tabs kt-portlet--last kt-portlet--responsive-mobile" id="kt_page_portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-toolbar">
                <ul class="nav nav-tabs nav-tabs-space-xl nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#add_tab_1" role="tab" aria-selected="false">
                            </span>{{ __('cms::landing_page.sections.meta.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_2" role="tab" aria-selected="false">
                            {{ __('cms::landing_page.sections.header.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_3" role="tab" aria-selected="false">
                            </span>{{ __('cms::landing_page.sections.subject_text.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_4" role="tab" aria-selected="false">
                            {{ __('cms::landing_page.sections.projects.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_5" role="tab" aria-selected="false">
                           {{ __('cms::landing_page.sections.blogs.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_6" role="tab" aria-selected="false">
                           {{ __('cms::landing_page.sections.services.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_7" role="tab" aria-selected="false">
                           {{ __('cms::landing_page.sections.videos.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_8" role="tab" aria-selected="false">
                           {{ __('cms::landing_page.sections.subject_text2.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_9" role="tab" aria-selected="false">
                           {{ __('cms::landing_page.sections.footer.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_10" role="tab" aria-selected="false">
                            Timeline
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-12">
                    <form onsubmit="onFormSubmit(event);" class="kt-form" id="addNewForm" action="{{ route('LandingPageController@update',['model' => $model->id]) }}" method="POST">
                        <div class="tab-content">
                            @csrf
                            <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
                            <div class="tab-pane active" id="add_tab_1" role="tabpanel">
                                @include('cms::admin.landingpages.update.meta')
                            </div>
                            <div class="tab-pane" id="add_tab_2" role="tabpanel">
                                @include('cms::admin.landingpages.update.header')
                            </div>
                            <div class="tab-pane" id="add_tab_3" role="tabpanel">
                                @include('cms::admin.landingpages.update.subject_text')
                            </div>
                            <div class="tab-pane" id="add_tab_4" role="tabpanel">
                                @include('cms::admin.landingpages.update.projects')
                            </div>
                            <div class="tab-pane" id="add_tab_5" role="tabpanel">
                                @include('cms::admin.landingpages.update.blogs')
                            </div>
                            <div class="tab-pane" id="add_tab_6" role="tabpanel">
                                @include('cms::admin.landingpages.update.services')
                            </div>
                            <div class="tab-pane" id="add_tab_7" role="tabpanel">
                                @include('cms::admin.landingpages.update.videos')
                            </div>
                            <div class="tab-pane" id="add_tab_8" role="tabpanel">
                                @include('cms::admin.landingpages.update.subject_text2')
                            </div>
                            <div class="tab-pane" id="add_tab_9" role="tabpanel">
                                @include('cms::admin.landingpages.update.footer')
                            </div>
                            <div class="tab-pane" id="add_tab_10" role="tabpanel">
                                @include('cms::admin.landingpages.update.timeline')
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
@endsection
@push('scripts')

<script>
    // window.onbeforeunload = function() {
    //    return "Do you really want to leave our brilliant application?";
    // };
</script>

<script>
    var KTFormRepeater = function() {
    var demo1 = function() {
    var repeater = $('#kt_repeater_1').repeater({
        initEmpty: true,

        defaultValues: {
            'text-input': 'foo'
        },
        show: function () {
            var reoeater_item = $(this);
            reoeater_item.slideDown();

            let key = reoeater_item.find('.pay-id').last().attr('name');
            key = key.replace(/\[/g, '_').replace(/\]/g, '');
            reoeater_item.find('.pay-id').last().attr('id', key);
            reoeater_item.find('.pay-id').last().select2(pay_id_config);
            //
            let key_2 = reoeater_item.find('.number-pays-id').last().attr('name');
            key_2 = key_2.replace(/\[/g, '_').replace(/\]/g, '');
            reoeater_item.find('.number-pays-id').last().attr('id', key_2);
            reoeater_item.find('.number-pays-id').last().select2(number_pays_config);
            //
            reoeater_item.find('.number-pays-id').last().attr('disabled',true);

            reoeater_item.find('.pay-id').on('change', function() {
                if($(this).val() != '' && $(this).val() != null){
                    reoeater_item.find('.number-pays-id').last().attr('disabled',false);
                    reoeater_item.find('.number-pays-id').last().val('').trigger('change');
                    number_pays_parameters.additional_params.category_parent_id = $(this).val();
                    // console.log(number_pays_parameters.additional_params.category_parent_id);
                }
            });
        },
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        }
    });
    console.log('first intilize');
    $('#kt_repeater_2').repeater({
        initEmpty: true,
        defaultValues: {
            'text-input': 'foo'
        },
        show: function () {
            console.log('on add');
            $(this).slideDown();
            // $(this).find('.kt-bootstrap-select').children().eq(1).remove();
            // $(this).find('.kt-bootstrap-select').selectpicker();
            // $(document).find('.kt-bootstrap-select').selectpicker('refresh');

            // let key_3 = $(this).find('.balance').last().attr('name');
            // key_3 = key_3.replace(/\[/g, '_').replace(/\]/g, '');
            // $(this).find('.balance').last().attr('id', key_3);
            // $(this).find('.balance').last().select2(balance_config);
        },
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        }
        });
    }
    return {
        // public functions
        init: function() {
            demo1();
        }
    };
    }();
    jQuery(document).ready(function() {
        KTFormRepeater.init();
    });
    // Submits the form whenever a button with the class .submit_form is clicked.
    $('.submit_form').click(function() {
        $('#redirectUrl').val($(this).data('redirectUrl'));
        $('#addNewForm').submit();
    });
    $('.nav-link').click(function() {
        KTUtil.scrollTop();
    });

    $(".delete-timeline").click(function() {
        var timeline_id = $(this).data('timelineid');
        swal.fire({
            title: '{!! __('cms::confirmations.confirm.delete.title') !!}',
            text: '{!! __('cms::confirmations.confirm.delete.text') !!}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '{!! __('cms::confirmations.yes') !!}',
            cancelButtonText: '{!! __('cms::confirmations.cancel') !!}',
            reverseButtons: true
        }).then(function(result){
            if (result.value) {
                $.ajax({
                    url: "{{ route('LandingPageController@deleteTimeline')}}",
                    type: "POST",
                    data: {
                        timeline_id : timeline_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if(response.success){
                            $('#delete_timeline_'+ timeline_id).remove();
                            show_toastr(
                                response.type,
                                response.title,
                                response.description
                            );
                        }
                        else{
                            show_toastr(
                                response.type,
                                response.title,
                                response.description
                            );
                        }
                    }
                })
            } else if (result.dismiss === 'cancel') {
                swal.fire(
                    '{{ __('cms::confirmations.confirm.delete.canceled.title') }}',
                    '{{ __('cms::confirmations.confirm.delete.canceled.text') }}',
                    'error'
                );
            }
        });
    });
</script>

@endpush



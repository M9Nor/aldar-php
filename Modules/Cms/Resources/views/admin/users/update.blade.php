@extends('cms::layouts.master')

@section('title', __('cms::users.title'))

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('cms::users.update_user'),
            'items' => [
                [
                    'label' => __('cms::users.title'),
                    'link'  => route('UserController@index')
                ], [
                    'label' => __('cms::users.update_user'),
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
                            <a href="javascript:;" data-redirect-url="{{ route('UserController@create') }}" class="submit_form kt-nav__link">
                                <i class="kt-nav__link-icon flaticon2-add-square"></i>
                                <span class="kt-nav__link-text">{{ __('cms::global.save_and_add_new') }}</span> <!-- Save &amp; add new -->
                            </a>
                        </li>
                        <li class="kt-nav__item">
                            <a href="javascript:;" data-redirect-url="{{ route('UserController@index') }}" class="submit_form kt-nav__link">
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
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                </g>
                            </svg> {{ __('cms::users.sections.account.title') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add_tab_2" role="tab" aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg> {{ __('cms::users.sections.profile.title') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form onsubmit="onFormSubmit(event);" class="kt-form" id="addNewForm" action="{{ route('UserController@update',['model'=>$model->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="tab-content">
                    @csrf
                    <input type="hidden" id="redirectUrl" name="redirect_url" value="save_and_exit">
                    <div class="tab-pane active" id="add_tab_1" role="tabpanel">
                        @include('cms::admin.users.sections.account_')
                    </div>
                    <div class="tab-pane" id="add_tab_2" role="tabpanel">
                        @include('cms::admin.users.sections.profile_')
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Submits the form whenever a button with the class .submit_form is clicked.
        $('.submit_form').click(function() {
            $('#redirectUrl').val($(this).data('redirectUrl'));
            $('#addNewForm').submit();
        });

        $('.nav-link').click(function() {
            KTUtil.scrollTop();
        });

        // Checks if the entered username or email is already exists in the database.
        $('[name="username"], [name="email"]').on('input', function() {
            let validatedInput = $(this),
                keyword = validatedInput.val();

            // Removes valid/invalid status indicators.
            validatedInput.removeClass('is-valid is-invalid');

            if(validatedInput.closest('.input-group').length)
            {
                validatedInput.closest('.input-group').siblings('.invalid-feedback, .valid-feedback').remove();
            }
            else
            {
                validatedInput.siblings('.invalid-feedback, .valid-feedback').remove();
            }

            // Starts checking after entering at least 3 characters.
            if(keyword.length > 2)
            {
                // Adds spinner to indicate that the input is currently being checked.
                validatedInput.parent()
                .addClass('kt-spinner kt-spinner--sm kt-spinner--success kt-spinner--right kt-spinner--input');

                $.ajax({
                    data: {
                        name: validatedInput.attr('name'),
                        keyword: keyword
                    },
                    url: "{{ route('UserController@validateIdentity_') }}",
                    type: 'GET',
                    success: function (data) {
                        validatedInput.addClass(data.is_valid ? 'is-valid' : 'is-invalid');

                        if(data.error)
                        {
                            if(Array.isArray(data.error))
                            {
                                invalidFeedback = `<div class="invalid-feedback d-block">${data.error[0]}</div>`;
                            }
                            else
                            {
                                invalidFeedback = `<div class="invalid-feedback d-block">${data.error}</div>`;
                            }

                            if(validatedInput.closest('.input-group').length)
                            {
                                $(invalidFeedback).insertAfter(validatedInput.closest('.input-group'));
                            }
                            else
                            {
                                validatedInput.closest('.form-group').append(invalidFeedback);
                            }
                        }
                    },
                    complete: function (data) {
                        validatedInput.parent()
                        .removeClass('kt-spinner kt-spinner--sm kt-spinner--success kt-spinner--right kt-spinner--input');
                    }
                });
            }
        });
    </script>
@endpush


@push('filter.toolbar')
    <a href="javascript:;" id="toggle_filter" class="btn btn-outline-warning btn-sm btn-icon btn-icon-md btn-elevate datatable-custom-tools" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="{{ __('cms::global.filter') }}">
        <i class="fa fa-filter"></i>
    </a>
@endpush

@push('filter.form')
    <div id="filter" class="kt-section" style="display: none;">
        <div class="kt-section__title">
            {{ __('backend::requests.filter.title') }}
        </div>
        <div class="kt-section__desc">
            {{ __('backend::requests.filter.description') }}
        </div>
        <div class="kt-section__content">
            <form id="filter_form" class="kt-form kt-form--fit" action="javascript:;" method="POST">
                <div class="row kt-margin-b-20">
                    <div class="col-lg-6 kt-margin-b-10-tablet-and-mobile">
                        @include('cms::components.inputs.datepicker', [
                            'options' => [
                                'name'          => 'daterange',
                                'type'          => 'text',
                                'label'         => __('backend::requests.fields.daterange.label'),
                                'placeholder'   => __('backend::requests.fields.daterange.placeholder'),
                                'help'          => __('backend::requests.fields.daterange.help'),
                                'value'         => old('daterange'),
                                'required'      => false,
                                'icon'          => 'la la-calendar-check-o',
                            ]
                        ])
                    </div>
                </div>
                <div class="row kt-margin-b-20">
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-primary btn-sm btn-brand--icon">
                            <span>
                                <i class="la la-search"></i>
                                <span>{{ __('cms::global.filter') }}</span>
                            </span>
                        </button>
                        &nbsp;&nbsp;
                        <button id="reset_filter" class="btn btn-secondary btn-sm btn-secondary--icon">
                            <span>
                                <i class="la la-close"></i>
                                <span>{{ __('cms::global.reset') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
            </form>
        </div>
    </div>
@endpush

@push('filter.scripts')

    <script>
        $(function() {
            var scrollTop = new KTScrolltop('toggle_filter', {
                offset: 300,
                speed: 600,
                toggleClass: 'kt-scrolltop--on'
            });
            // Show/hide the filter form.
            $('#toggle_filter').click(function(e) {
                e.preventDefault();

                if($('#filter').css('display') == 'none')
                {
                    $('#filter').slideDown(400);
                    $(this).addClass('active');
                }
                else
                {
                    $('#filter').slideUp(400);
                    $(this).removeClass('active');
                    $('#filter_form').trigger("reset");
                }
            });

            // Reset all filter form fields.
            $('#reset_filter').click(function() {
                $('#filter_form').trigger("reset");
            });

            // Reload datatable and submit the filter data.
            $('#filter_form').submit(function () {
                dataTable.ajax.reload();
            });
        });
    </script>

@endpush

@extends('cms::layouts.master')

@section('title', __('backend::requests.title'))

@include('backend::admin.filter')

@push('styles')
    <style>
        /* .dataTables_filter{
            display: none;
        } */
    </style>
    @if(app()->getLocale() == 'ar')
        <link href="{{ Module::asset('cms:metronic/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{ Module::asset('cms:metronic/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    @endif
@endpush

@push('scripts')
   <script src="{{ Module::asset('cms:metronic/plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>  __('backend::requests.title'),
            'items' => [
                [
                    'label' => __('backend::requests.title'),
                    'link'  => 'javascript:;'
                ]
            ]
        ]
    ])

        @slot('main')

        @endslot
        @slot('toolbar')
            @stack('filter.toolbar')
            <a href="javascript:;" id="reload_datatable" class="btn btn-outline-brand btn-sm btn-icon btn-icon-md btn-elevate datatable-custom-tools" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="{{ __('cms::global.reload') }}">
                <i class="fa fa-sync-alt"></i>
            </a>
        @endslot
    @endcomponent
@endsection

@section('content')
    @component('cms::components.partials.portlet')
        @stack('filter.form')

        <table class="table table-bordered table-striped table-hover table-checkable" id="datatable">
            <thead>
                <tr class="cms-font">
                    <th width="5%"></th>
                    <th>{{ __('backend::requests.datatable.request') }}</th>
                    <th>{{ __('backend::requests.datatable.email') }}</th>
                    <th>{{ __('backend::requests.datatable.phone') }}</th>
                    <th>{{ __('backend::requests.datatable.breef') }}</th>
                    <th>{{ __('backend::requests.datatable.date') }}</th>
                    <th>{{ __('backend::requests.datatable.link') }}</th>

               </tr>
            </thead>
        </table>
    @endcomponent
    <div class="modal fade" id="request_summary_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">
                        {{ __('backend::requests.title') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div id="request_summary_body" style="transform-origin: top; transition: transform 1s ease;" class="modal-body">
                    @include('backend::admin.summary', [
                        'model' => new \Modules\Backend\Entities\ContactUs,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        {{ __('cms::global.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $('#request_summary_modal').on('shown.bs.modal', function() {
                KTApp.block('#request_summary_modal  .modal-content', {
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'success',
                    message: "{{ __('cms::global.please_wait') }}"
                });
            });

            var getRequestData = function(id) {
                $('#request_summary_body').css({
                    "-webkit-filter": "blur(3px)",
                    "-moz-filter": "blur(3px)",
                    "-o-filter": "blur(3px)",
                    "-ms-filter": "blur(3px)",
                    "filter": "blur(3px)"
                });

                $.ajax({
                    url: "{{ route('ProjectController@request_summary') }}",
                    type: "GET",
                    data: {
                        model: id
                    },
                    dataType: "json",
                    success: function(data) {
                        setTimeout(() => {
                            KTApp.unblock('#request_summary_modal  .modal-content');
                            $('#request_summary_body').html(data.summary).css({
                                "transition": "filter 0.3s ease",
                                "-webkit-filter": "blur(0px)",
                                "-moz-filter": "blur(0px)",
                                "-o-filter": "blur(0px)",
                                "-ms-filter": "blur(0px)",
                                "filter": "blur(0px)"
                            });
                        }, 200);
                    }
                });
            };
        </script>
    @endpush

@endsection

@push('scripts')
    <script>
        var dataTable;

        $(function() {
            $.extend(options, {
                dom: `<'row'<'col-sm-12 col-md-5'<"toolbar">B><'col-sm-12 col-md-7 dataTables_pager'lp>>
                <'row'<'col-sm-12'tr>>
                <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
				buttons: [
                    {
                        extend:    'excel',
                        text:      '<i class="fa fa-file-excel"></i>',
                        titleAttr: "{{ __('cms::global.excel') }}",
                        className: 'btn btn-outline-brand btn-icon'
                    },
                    {
                        extend:    'print',
                        text:      '<i class="fa fa-print"></i>',
                        titleAttr: "{{ __('cms::global.print') }}",
                        className: 'btn btn-outline-brand btn-icon'
                    }
                ],
                lengthMenu: [[50, 100, 200, 500, -1], [50, 100, 200, 500, "{{ __('cms::global.all') }}"]],
                ajax: {
                    url: "{!! is_null(request()->type) ? route('ProjectController@data_requests') : route('ProjectController@data', ['type' => mb_strtolower(request()->type)]) !!}",
                    type: "GET",
                    data : function (d) {
                        d._token    = '{!! csrf_token() !!}';
                        d.trashed   = $('[name="trashed"]').is(':checked') ? 'show' : 'hide';
                        d.filter    = {};
                        // Get all inputs within the filter form and send them to controller after pressing the filter submit button.
                        $.extend(d.filter, $('#filter_form').serializeArray());

                        return d;
                    }
                },
                select: {
                    style: 'multi',
                    selector: 'td:first-child .kt-checkable',
                },
                // Render the checkbox column and replace the first column.
                headerCallback: function(thead, data, start, end, display) {
                    thead.getElementsByTagName('th')[0].innerHTML = `
                        <label class="kt-checkbox kt-checkbox--single kt-checkbox--solid kt-checkbox--brand">
                            <input type="checkbox" value="" class="kt-group-checkable">
                            <span></span>{{ __('backend::requests.datatable.id') }}
                        </label>`;
                },
                order: [[ 5, "desc" ]],
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            return `
                            <label class="kt-checkbox kt-checkbox--single kt-checkbox--solid kt-checkbox--brand">
                                <input type="checkbox" value="" class="kt-checkable">
                                <span></span>${full.DT_RowIndex}
                            </label>`;
                        },
                    },



                    {
                        data: 'sender',
                        name: 'sender',
                        orderable  : true,
                        render: function(data, type, row, meta) {
                            if(row.sender != null){
                                return `
                                <a onclick="getRequestData(${row.id});" data-target="#request_summary_modal" data-toggle="modal" href="javascript:;">

                                    <span class="kt-user-card-v2__name">
                                        ${row.sender}
                                    </span>
                                </a>`;
                            }else{
                                return `
                                <a onclick="getRequestData(${row.id});" data-target="#request_summary_modal" data-toggle="modal" href="javascript:;">
                                    <span class="kt-user-card-v2__name">
                                        
                                    </span>
                                </a>`;
                            }
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable  : true,
                        render: function(data, type, row, meta) {
                            return `<span class="kt-badge kt-badge--brand kt-badge--inline">${row.email}</span>`;
                        }
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        orderable  : true,
                        render: function(data, type, row, meta) {
                            return `<span class="kt-badge kt-badge--danger kt-badge--inline" dir="ltr">${row.phone}</span>`;
                        }
                    },
                    {
                        data: 'breef',
                        name: 'breef',
                        orderable  : true,

                    },
                    {
                        data: 'date',
                        name: 'created_at',
                        orderable  : true,

                    },
                    {
                        data: 'link',
                        name: 'link',
                        orderable  : true,
                        render: function(data, type, row, meta) {
                            {{-- The link is the visitor's Referer header: only http(s) becomes a link (S5). --}}
                            return row.link ? (/^https?:\/\//i.test(row.link) ? `<a href="${row.link}" target="_blank" rel="noopener">${row.link}</span>` : row.link) : '';
                        }
                    },

                ]
            });

            // Init datatable.
            dataTable = $('#datatable').DataTable(options);

            // @if(auth()->user()->can('viewDeleted', \App\User::class))
            //     // Render the trash items toggler.
            //     $("div.toolbar").html(`<input id="trashed" data-switch="true" type="checkbox" name="trashed" value="show" data-size="small" data-on-text="{{ __('cms::global.lang.datatable.deleted') }}" data-off-text="{{ __('cms::global.lang.datatable.hide_deleted') }}" data-on-color="danger" data-off-color="success">`);
            //     $('[data-switch=true]').bootstrapSwitch();
            // @endif

            // Reload datatable on button press.
            $("#reload_datatable").on("click", function (e) {
                e.preventDefault(), dataTable.ajax.reload();
            });

            // Delete selected records.
            $("#delete_all").on("click", function (e) {
                var selected = [];

                dataTable.rows().every(function (rowIdx, tableLoop, rowLoop) {
                    var data = this.node();
                    if($(data).find('input').prop('checked'))
                    {
                        selected.push(this.data().id);
                    }
                });

                $('[name="ids"]').val(selected);

                massDeleteConfirmation(null, function(){ $('#massDeleteForm').submit(); })
            });

            // Restore selected records.
            $("#restore_all").on("click", function (e) {
                var selected = [];

                dataTable.rows().every(function (rowIdx, tableLoop, rowLoop) {
                    var data = this.node();
                    if($(data).find('input').prop('checked'))
                    {
                        selected.push(this.data().id);
                    }
                });

                $('[name="ids"]').val(selected);

                massRestoreConfirmation(null, function(){ $('#massRestoreForm').submit(); })
            });

            // Show/hide trashed items.
            $(document).on("switchChange.bootstrapSwitch", '#trashed', function (e) {
                e.preventDefault(), dataTable.ajax.reload();
            });

            // Check/uncheck all checkboxes within the table rows.
            dataTable.on('change', '.kt-group-checkable', function() {
                var set = $(this).closest('table').find('td:first-child .kt-checkable');
                var checked = $(this).is(':checked');

                $(set).each(function() {
                    if (checked) {
                        $(this).prop('checked', true);
                        dataTable.rows($(this).closest('tr')).select();
                    }
                    else {
                        $(this).prop('checked', false);
                        dataTable.rows($(this).closest('tr')).deselect();
                    }
                });
            });

            // Show additional tools if at least one checkbox is checked.
            function toggleDeleteAllButton()
            {
                var checked = $('.kt-checkable:checked').length;

                if (checked) {
                    $('#delete_all').removeClass('kt-hidden');
                }
                else {
                    $('#delete_all').addClass('kt-hidden');
                }

                if (checked && $('[name="trashed"]').is(':checked')) {
                    $('#restore_all').removeClass('kt-hidden');
                }
                else {
                    $('#restore_all').addClass('kt-hidden');
                }
            }

            // Listen to a fired 'change' event.
            dataTable.on('change', function() {
                toggleDeleteAllButton();
            });

            // Listen to a fired 'draw' event.
            dataTable.on('draw', function() {
                toggleDeleteAllButton();
            });
        });
    </script>

    @stack('filter.scripts')
@endpush


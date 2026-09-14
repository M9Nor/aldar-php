@php
    use Modules\Cms\Entities\Tag;
@endphp

@extends('cms::layouts.master')
@push('styles')
    <style>
        .sorting_desc:before , .sorting_desc:after{
            content: '' !important;
        }
    </style>
@endpush
@section('title' , __('cms::areas.tags.tags'))

@include('cms::admin.tags.filter')

@push('styles')
    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link href="{{ Module::asset('cms:metronic/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ Module::asset('cms:metronic/plugins/custom/jstree/jstree.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{ Module::asset('cms:metronic/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ Module::asset('cms:metronic/plugins/custom/jstree/jstree.bundle.css') }}" rel="stylesheet" type="text/css" />
    @endif
@endpush

@push('scripts')
   <script src="{{ Module::asset('cms:metronic/plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
   <script src="{{ Module::asset('cms:metronic/plugins/custom/jstree/jstree.bundle.js') }}" type="text/javascript"></script>
@endpush

@section('subheader')
    @component('cms::includes.subheader', [
        'options' => [
            'title' =>   __('cms::areas.tags.tags'),
            'items' => [
                [
                    'label' => '',
                    'link'  => 'javascript:;'
                ],
            ]
        ]
    ])
        @slot('main')

        @endslot
        @slot('toolbar')
        @if(auth()->user()->can('deleteMultiple', Tag::class))
            <a href="javascript:;" id="delete_all" class="btn btn-outline-danger btn-sm btn-icon btn-icon-md btn-elevate datatable-custom-tools kt-hidden" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="{{ __('cms::global.actions.delete_selected') }}">
                <i class="fa fa-trash"></i>
            </a>
            <form id="massDeleteForm" onsubmit="onFormSubmit(event);" method="POST" action="{{ route('TagController@massDestroy') }}" style="display: none;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="ids">
            </form>
        @endif
        @if(auth()->user()->can('restoreMultiple', Tag::class))
            <a href="javascript:;" id="restore_all" class="btn btn-outline-brand btn-sm btn-icon btn-icon-md btn-elevate datatable-custom-tools kt-hidden" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="{{ __('cms::global.actions.restore_selected') }}">
                <i class="fa fa-undo"></i>
            </a>
            <form id="massRestoreForm" onsubmit="onFormSubmit(event);" method="POST" action="{{ route('TagController@massRestore') }}" style="display: none;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="ids">
            </form>
        @endif
        @stack('filter.toolbar')
        <a href="javascript:;" id="reload_datatable" class="btn btn-outline-brand btn-sm btn-icon btn-icon-md btn-elevate datatable-custom-tools" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="{{ __('cms::global.reload') }}">
            <i class="fa fa-sync-alt"></i>
        </a>
        @if(auth()->user()->can('create', Tag::class))
            <a href="{{ route('TagController@create') }}" class="btn btn-outline-success btn-sm btn-icon btn-icon-md btn-elevate datatable-custom-tools" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="{{ __('cms::global.add_new') }}">
                <i class="fa fa-plus"></i>
            </a>
        @endif
    @endslot
    @endcomponent
@endsection

@section('content')
{{-- <img src="{{ GlideImage::create('uploads/image.jpg')->modify(['w'=> 50, 'filt'=>'greyscale']) }}" /> --}}
    @component('cms::components.partials.portlet')
        @stack('filter.form')

        <table class="table table-bordered table-striped table-hover table-checkable" id="datatable">
            <thead>
                <tr class="cms-font">
                    <th width="5%"></th>
                    <th>{{ __('cms::areas.datatable.tags') }}</th>
                    <th>AR</th>
                    <th>EN</th>
                    {{-- <th>TR</th> --}}
                    <th>FA</th>
                    <th>{{ __('cms::areas.datatable.actions') }}</th>
                </tr>
            </thead>
        </table>
    @endcomponent
@endsection

@push('scripts')
    <script>
        var dataTable;
        $(function() {
            $.extend(options, {
                ajax: {
                    url: "{!! route('TagController@data') !!}",
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
                            <span></span>{{ __('permissions::roles.datatable.id') }}
                        </label>`;
                },
                drawCallback: function( settings, json ) {
                    $(".kt-tree").jstree({
                        core: {
                            themes: {
                                responsive: !1
                            }
                        },
                        types: {
                            default: {
                                icon: "fa fa-shield-alt kt-font-primary"
                            },
                            file: {
                                icon: "fa fa-check kt-font-success"
                            }
                        },
                        plugins: ["types"]
                    });
                },
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
                        data: 'translated_name',
                        name: 'translated_name',
                        orderable  : true,

                    },
                    {
                        data       : "arabic",
                        orderable  : false,
                        searchable : false
                    },
                    {
                        data       : "english",
                        orderable  : false,
                        searchable : false
                    },
                    // {
                    //     data       : "turkish",
                    //     orderable  : false,
                    //     searchable : false
                    // },
                    {
                        data       : "persian",
                        orderable  : false,
                        searchable : false
                    },


                    {
                        data       : "actions",
                        orderable  : false,
                        searchable : false,
                        className  : "text-{{ $langDirection == 'rtl' ? 'right' : 'left' }}",
                        render     : function(data, type, row, meta) {
                            return dataTableActions(data, type, row, meta);
                        }
                    }
                ],
                order: [ [0,'desc'] ],
            });

            // Init datatable.
            dataTable = $('#datatable').DataTable(options);

@if(auth()->user()->can('viewDeleted', Tag::class))
    // Render the trash items toggler.
    $("div.toolbar").html(`<input id="trashed" data-switch="true" type="checkbox" name="trashed" value="show" data-size="small" data-on-text="{{ __('cms::global.lang.datatable.deleted') }}" data-off-text="{{ __('cms::global.lang.datatable.hide_deleted') }}" data-on-color="danger" data-off-color="success">`);
    $('[data-switch=true]').bootstrapSwitch();
@endif

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

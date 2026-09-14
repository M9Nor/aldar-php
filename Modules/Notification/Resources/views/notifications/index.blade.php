@extends('admin::layouts.metronic')

@section('SubHeader')
    @component('admin::components.partials.subHeader', [
        'options' => [
            'title' => __('admin::strings.notifications'),
            'items' => [
                [
                    'label' => trans_choice('admin::strings.list_all', 0),
                    'link'  => route('NotificationsController@index'),
                ]
            ]
        ]
    ])

        @if (auth()->user()->isAn('ROOT') OR auth()->user()->can('CREATE_NOTIFICATION'))
            <a href="{!! route('NotificationsController@create') !!}" class="btn btn-label-warning btn-bold btn-sm btn-icon-h kt-margin-l-10">
                {!! __('admin::strings.add_new') !!}
            </a>
        @endif
        
        @slot('toolbar')
            <div class="kt-input-icon kt-input-icon--right kt-subheader__search">
                <input type="text" class="form-control datatable-search" placeholder="{{ __('admin::strings.search') }}" id="generalSearch">
                <span class="kt-input-icon__icon kt-input-icon__icon--right">
                    <span>
                        <i class="flaticon2-search-1"></i>
                    </span>
                </span>
            </div>
            <button type="button" class="advanced-search btn btn-label-success btn-bold btn-sm btn-icon-h kt-margin-l-10" data-toggle="modal" data-target="#advanced-search">
                <i class="flaticon2-search-1"></i> 
                {!! __('admin::strings.advanced_search') !!}
            </button>
        @endslot
    @endcomponent
@endsection

@section('MainContent')

    <form class="kt-form kt-form--label-right users-advanced-search-form" action="javascript:;" method="POST">
        {!! csrf_field() !!}
        <div class="modal modal-dark fade" id="advanced-search" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="advanced-search-title">{!! __('admin::strings.advanced_search') !!}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('admin::components.inputs.select', [
                            'options' => [
                                'size'        => 4,
                                'searchable'  => true,
                                'view'        => 'INLINE',
                                'name'        => 'roles',
                                'label'       => __('admin::inputs.roles.label'),
                                'nullable'    => __('admin::strings.all'),
                                'nullable_v'  => null,
                                'options'     => $Roles,
                                'text'        => function($K, $V){
                                    return $V->__('title', app()->getLocale());
                                },
                                'values'      => function($K, $V){
                                    return $V->name;
                                },
                                'select'      => function($K, $V, $value){
                                    return $V->name == $value;
                                },
                                'value'       => old('roles')
                            ]
                        ])
                        
                        <div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>
                                                
                        @include('admin::components.inputs.text', [
                            'options' => [
                                'view'        => 'INLINE',
                                'name'        => 'title',
                                'label'       => __('admin::inputs.notification_title.label'),
                                'placeholder' => __('admin::inputs.notification_title.placeholder'),
                                'value'       => old('title')
                            ]
                        ])
                        @include('admin::components.inputs.text', [
                            'options' => [
                                'view'        => 'INLINE',
                                'name'        => 'body',
                                'label'       => __('admin::inputs.notification_body.label'),
                                'placeholder' => __('admin::inputs.notification_body.placeholder'),
                                'value'       => old('body')
                            ]
                        ])                        
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-label-success btn-bold">
                            {!! __('admin::strings.search') !!}
                        </button>
                        <button type="reset" class="btn btn-label-warning btn-bold">
                            {!! __('admin::strings.reset') !!}
                        </button>
                        <button type="button" class="btn btn-label-danger btn-bold pull-left modal-close" data-dismiss="modal">
                            {!! __('admin::strings.close') !!}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="kt-portlet">
                {{-- <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {!! trans_choice('admin::strings.list_all', 0) !!}
                        </h3>
                    </div>
                </div> --}}
                
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('admin::inputs.id.label') }}</th>
                                    <th>{{ __('admin::inputs.details.label') }}</th>
                                    <th class="fit">{{ __('admin::inputs.roles.label') }}</th>
                                    <th class="fit">{{ __('admin::inputs.created_at.label') }}</th>
                                    <th class="fit">{{ __('admin::inputs.updated_at.label') }}</th>
                                    <th class="fit">{{ __('admin::strings.actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>

        var dataTable = $('#data-table').DataTable({
            language: {
                url: "{!! __('admin::strings.datatable_translations') !!}"
            },
            rowCallback: function(row, data){
                if( data["deleted_at"] ){
                    $(row).css('background-color', "rgb(201, 76, 76, 0.1)");
                }
            },
            ajax : {
                url  : "{!! route('NotificationsController@postIndex') !!}",
                type : "POST",
                data : function(data){
                    data._token = `{!! csrf_token() !!}`;
                    data.advanced_search = $('.users-advanced-search-form').first().serializeObject();
                    return data;
                }
            },
            searching    : true,
            bLengthChange: false,
            processing   : true,
            serverSide   : true,
            order        :  [
                [3, "desc"]
            ],
            columns: [
                {
                    data       : 'id',
                    name       : 'id',
                    orderable  : true,
                    searchable : true,
                    visible    : false,
                },
                {
                    data       : 'details',
                    name       : 'details',
                    defaultContent : '',
                    orderable  : false,
                    searchable : false,
                    render     : function(data,type,Row,meta){
                        return `
                        <span>
                            <div class="kt-user-card-v2">
                                <div class="kt-user-card-v2__details">
                                    <a class="kt-user-card-v2__name" href="javascript:;">${Row.title}</a>
                                    <span class="kt-user-card-v2__desc">${(Row.body)}</span>
                                </div>
                            </div>
                        </span>
                        `;
                    }
                },
                {
                    data       : "group",
                    name       : 'group',
                    render     : function(data,type,Row,meta) {
                        return `
                            <span class="btn btn-bold btn-sm btn-font-sm btn-label-${(Row.type_data ? (Row.type_data.label_color ? Row.type_data.label_color : '-------') : '-------')} text-center" style="width: 100%;">
                                ${(Row.type_data ? (Row.type_data.label_text ? Row.type_data.label_text : '-------') : '-------')}
                            </span>
                        `;
                    }
                },
                {
                    data       : "created_at",
                    name       : 'created_at',
                    render     : function(data,type,Row,meta) {
                        return `${(Row.new_created_at ? Row.new_created_at : (Row.created_at ? Row.created_at : '____/__/__'))}`;
                    }
                },
                {
                    data       : "updated_at",
                    name       : 'updated_at',
                    visible    : false,
                    render     : function(data,type,Row,meta) {
                        return `${(Row.new_updated_at ? Row.new_updated_at : (Row.updated_at ? Row.updated_at : '____/__/__'))}`;
                    }
                },
                {
                    data           : "actions",
                    defaultContent : '',
                    orderable      : false,
                    searchable     : false,
                    visible    : false,
                    render         : function(data,type,Row,meta) {
                        return BS_H.PARSE_DATATABLE_ACTIONS(Row, Row.actions);
                    }
                }
            ]
        });

        $(document).on('submit', '.users-advanced-search-form', function(e){
            e.preventDefault();
            dataTable.draw();
            $(this).find('.close').click();
        });
        
        $(document).on('submit', '.datatable-advanced-search-form', function(e){
            e.preventDefault();
            dataTable.draw();
        });
        
        $(document).on('keyup', '.datatable-search', function(e){
            dataTable.search(
                $(this).val()
            ).draw();
        });
    </script> 
@endpush
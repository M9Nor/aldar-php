<script>
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#5d78ff",
                "dark": "#282a3c",
                "light": "#ffffff",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": [
                    "#c5cbe3",
                    "#a1a8c3",
                    "#3d4465",
                    "#3e4466"
                ],
                "shape": [
                    "#f0f3ff",
                    "#d9dffa",
                    "#afb4d4",
                    "#646c9a"
                ]
            }
        }
    };
</script>

<script src="{{ Module::asset('cms:metronic/plugins/global/plugins.bundle.js') }}" type="text/javascript"></script>
<script src="{{ Module::asset('cms:metronic/js/scripts.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
<script src="{{ Module::asset('cms:js/master.js') }}" type="text/javascript"></script>
<script>
    var formUpdated = false;
    var options = {
        dom: `<'row'<'col-sm-12 col-md-5'<"toolbar">><'col-sm-12 col-md-7 dataTables_pager'lp>>
                <'row'<'col-sm-12'tr>>
                <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
        responsive: true,
        bLengthChange: true,
        lengthMenu: [[50, 100, 200, 500], [50, 100, 200, 500]],
        pageLength: 50,
        language: {!! json_encode(__('cms::global.lang.datatable')) !!},
        // This will be called before the datatable rows are drawn.
        preDrawCallback: function(settings) {

        },
        rowCallback: function(row, data) {
            if( data["deleted_at"] ){
                $(row).css('background-color', "rgb(201, 76, 76, 0.1)");
            }
            if( data["disabled_at"] ){
                $(row).css('background-color', "rgba(86, 86, 86, 0.15)");
            }
            if( data["is_special"] == 1 ){
                $(row).css('background-color', "rgba(177 240 162)");
            }
            if( data["is_sold"] == 1 ){
                $(row).css('background-color', "rgb(179 202 255)");
            }
        },
        // This will be called after the datatable rows are drawn.
        drawCallback: function(settings) {
            // Tooltip initialization must be after the datatable is drown.
            $('[data-toggle="kt-tooltip"]').tooltip();
        },
        searching: true,
        processing: true,
        serverSide: true,
        bLengthChange: true,
        searchDelay: 1000,
        // Prevent sorting arrows from appearing on the first column even after setting orderable to false.
        order: [],
    };

    function dataTableActions(data, type, row, meta) {
        var actions = '<span>';
        $.each(row.actions.icons, function(k, action) {
            switch (action.type) {
                case 'form':
                    actions += `
                        <a href="javascript:;" onclick="${action.action}Confirmation(null, function(){ $('#action_${action.action}_${row.id}').submit(); })" id="${action.id}" class="btn btn-clean btn-icon btn-icon-md" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="${action.label}">
                            <i class="${action.icon}"></i>
                        </a>
                        <form id="action_${action.action}_${row.id}" onsubmit="onFormSubmit(event);" method="POST" action="${action.url}" style="display: none;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    `;
                    break;
                default:
                    actions += `
                    <a href="${action.url}" id="${action.id}" class="btn  btn-icon btn-icon-lg btn-clean" data-toggle="kt-tooltip" data-placement="right" title="" data-original-title="${action.label}">
                        <i class="${action.icon}"></i>
                    </a>
                    `;
                    break;
            }
        });
        actions += '</span>';

        if(row.actions.dropdown.length > 0)
        {
            actions += `
                <span class="dropdown">
                    <a href="#" class="btn btn-clean btn-icon btn-icon-lg" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-fw fa-cog"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-{{ $langDirection == 'rtl' ? 'left' : 'right' }}" x-placement="bottom-end">
            `;
            $.each(row.actions.dropdown, function(k, dropdownAction) {
                switch (dropdownAction.type) {
                    case 'form':
                        actions += `${dropdownAction.divider ? '<div class="dropdown-divider"></div>' : ''}
                            <a class="dropdown-item" href="javascript:;" id="${dropdownAction.id}" onclick="${dropdownAction.action}Confirmation(null, function(){ $('#action_${dropdownAction.action}_${row.id}').submit(); })">
                                <i class="${dropdownAction.icon} text-${dropdownAction.color}"></i>
                                ${dropdownAction.label}
                            </a>
                            <form id="action_${dropdownAction.action}_${row.id}" onsubmit="onFormSubmit(event);" method="POST" action="${dropdownAction.url}" style="display: none;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        `;
                        break;
                    default:
                        actions += `${dropdownAction.divider ? '<div class="dropdown-divider"></div>' : ''}
                            <a class="dropdown-item" href="${dropdownAction.url}" id="${dropdownAction.id}">
                                <i class="${dropdownAction.icon} text-${dropdownAction.color}"></i>
                                ${dropdownAction.label}
                            </a>
                        `;
                        break;
                }
            });
            actions += `</div></span>`;
        }
        return actions;
    }

    $(document).ready(function() {
        // Display 'Loading' box indicating that an AJAX request is being made.
        var loading = new KTDialog({
            'type': 'loader',
            'placement': 'top center',
            'message': "{{ __('cms::global.loading') }}"
        });

        $(document).ajaxStart(() => {
            loading.show();
        });

        $(document).ajaxStop(() => {
            loading.hide();
        });

        // Initialize all selectpickers
        $(".m_selectpicker").selectpicker();

        $('input,select,textarea').change(function(e) {
            if(formUpdated !== undefined) formUpdated = true;
        });

        $('a').click(function(e) {
            var clickedToGoLinkHref = $(e.currentTarget).attr('href');
            if($('a.submit_form, button[type="submit"]').length > 0 && formUpdated && clickedToGoLinkHref != 'javascript:;' && !clickedToGoLinkHref.startsWith("#"))
            {
                e.preventDefault();
                swal.fire({
                    title: '{!! __('cms::confirmations.confirm.leave_without_saving.title') !!}',
                    text: '{!! __('cms::confirmations.confirm.leave_without_saving.text') !!}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{!! __('cms::confirmations.yes') !!}',
                    cancelButtonText: '{!! __('cms::confirmations.cancel') !!}',
                    reverseButtons: true
                }).then(function(result){
                    if(result.value)
                    {
                        formUpdated = false;
                        // $(e.currentTarget).trigger('click');
                        window.location.href = $(e.currentTarget).attr('href');
                    }
                    else if(result.dismiss === 'cancel')
                    {
                        swal.fire(
                            '{{ __('cms::confirmations.confirm.leave_without_saving.canceled.title') }}',
                            '{{ __('cms::confirmations.confirm.leave_without_saving.canceled.text') }}',
                            'error'
                        );
                    }
                });
            }
        });

        $('form').submit(function(e) {
            e.preventDefault();
            e.stopPropagation();
            if(formUpdated !== undefined) formUpdated = false;
            return false;
        });
    });

    // Removes validation states from any changed input.
    $(':input:not(".disable-status-removal")').on('input change', function() {
        $(this).removeClass('is-valid is-invalid');
        // For form widgets such as Bootstrap Select.
        $(this).parent('.form-control').removeClass('is-valid is-invalid');

        $(this).closest('.form-group').find('.invalid-feedback, .valid-feedback').remove();
    });
</script>

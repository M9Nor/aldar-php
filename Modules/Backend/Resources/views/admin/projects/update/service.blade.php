<div class="kt-section kt-section--first">
    <div class="row">
        <div class="col-md-12">
            <div class="kt-wizard-v4__form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="kt-section__body">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('cms::components.inputs.dual_listbox', [
                                        'options' => [
                                            'id'                => 'facilities',
                                            'name'              => 'facilities[]',
                                            'searchable'        => true,
                                            'icons'             => true,
                                            'data'              => $facilities->where('type','facilities'),
                                            'selected'          => $modelFacilities->where('type','facilities')->get()->pluck('id')->toArray(),
                                            'option_disabled'   => function($data, $selected, $key, $value){ return false; },
                                            'label'             => null,
                                            'help'              => null,
                                            'available_items'   => __('cms::dual_listbox.available_items'),
                                            'selected_items'    => __('cms::dual_listbox.selected_items'),
                                            'value'             => function($data, $key, $value){ return $value->id; },
                                            'text'              => function($data, $key, $value){ return $value->translateOrFirst(app()->getLocale())->title; },
                                            'select'            => function($data, $selected, $key, $value){ return in_array($value->id, $selected); },
                                            'required'          => false,
                                            'error_bag'         => null,
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
@push('scripts')
    <script>
        // Submits the form whenever a button with the class .submit_form is clicked.
        $('.submit_form').click(function() {
            $('[name="facilities[]"]').remove();
            $('.dual-listbox__selected .dual-listbox__item').each(function(i, e) {
                $('#addNewForm').append(`
                    <input type="hidden" name="facilities[]" value="${$(this).data('id')}">
                `);
            });
        });
        // Selects all/none of accordion items items.
        $('.select_all').on('click', function() {
            $(this).closest('.multi-select-btn-group').siblings('.switch-group').find('input[name*="dual_listbox"]').prop('checked', true);
        });
        $('.select_none').on('click', function() {
            $(this).closest('.multi-select-btn-group').siblings('.switch-group').find('input[name*="dual_listbox"]').prop('checked', false);
        });
        $('.global-select-all').on('click', function() {
            $('.collapse').collapse('show');
            $('input[name*="dual_listbox"]').prop('checked', true);
        });
        $('.global-select-none').on('click', function() {
            $('.collapse').collapse('hide');
            $('input[name*="dual_listbox"]').prop('checked', false);
        });
    </script>
@endpush
<div class="modal fade" id="user_summary_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">
                    {{ __('cms::users.modal.title') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div id="user_summary_body" style="transform-origin: top; transition: transform 1s ease;" class="modal-body">
                @include('cms::admin.users.summary', [
                    'model' => (new \App\User)->toArray()
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
        $('#user_summary_modal').on('shown.bs.modal', function() {
            KTApp.block('#user_summary_modal  .modal-content', {
                overlayColor: '#000000',
                type: 'v2',
                state: 'success',
                message: "{{ __('cms::global.please_wait') }}"
            });
        });

        var getUserData = function(id) {
            $('#user_summary_body').css({
                "-webkit-filter": "blur(3px)",
                "-moz-filter": "blur(3px)",
                "-o-filter": "blur(3px)",
                "-ms-filter": "blur(3px)",
                "filter": "blur(3px)"
            });

            $.ajax({
                url: "{{ route('UserController@summary') }}",
                type: "GET",
                data: {
                    model: id
                },
                dataType: "json",
                success: function(data) {
                    setTimeout(() => {
                        KTApp.unblock('#user_summary_modal  .modal-content');
                        $('#user_summary_body').html(data.summary).css({
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

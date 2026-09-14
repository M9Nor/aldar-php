<script>
    // Ask for confirmation using SweetAlert plugin.
    @foreach([
        'delete', 'forceDelete', 'restore', 'updateStatus', 'massDelete', 'massRestore' , 'disable' , 'enable', 'deleteTranslation', 'not_special', 'special', 'not_sold', 'sold', 'copy'
    ] as $type)
    var {{ $type }}Confirmation = function(url, approved, canceled){
        swal.fire({
            title: '{!! __('cms::confirmations.confirm.' . $type . '.title') !!}',
            text: '{!! __('cms::confirmations.confirm.' . $type . '.text') !!}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '{!! __('cms::confirmations.yes') !!}',
            cancelButtonText: '{!! __('cms::confirmations.cancel') !!}',
            reverseButtons: true
        }).then(function(result){
            if(result.value)
            {
                if(approved)
                {
                    approved();
                }

            }
            else if(result.dismiss === 'cancel')
            {
                if (canceled)
                {
                    canceled();
                }
                else
                {
                    swal.fire(
                        '{{ __('cms::confirmations.confirm.' . $type . '.canceled.title') }}',
                        '{{ __('cms::confirmations.confirm.' . $type . '.canceled.text') }}',
                        'error'
                    );
                }
            }
        });
    }
    @endforeach
</script>

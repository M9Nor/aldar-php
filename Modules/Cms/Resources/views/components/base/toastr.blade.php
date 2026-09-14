@push('scripts')
<script>
    function show_toastr(type = 'success', title, description, timeOut = "5000"){
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": timeOut,
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        switch (type) {
            case 'info':
                toastr.info(description, title);
                break;
            case 'warning':
                toastr.warning(description, title);
                break;
            case 'danger':
                toastr.error(description, title);
                break;
            default:
                toastr.success(description, title);
                break;
        }
    }
</script>
@endpush

@if(session()->has('toastr'))
    @php
        $toastr = session('toastr');
    @endphp
@endif

@if(isset($toastr))
    @push('scripts')
        <script>
            show_toastr('{{ $toastr['type'] }}', '{{ $toastr['title'] }}', '{!! $toastr['description'] !!}');
        </script>
    @endpush
@endif

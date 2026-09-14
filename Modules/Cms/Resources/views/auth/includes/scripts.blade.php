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
<script src="{{ Module::asset('cms:metronic/js/pages/custom/login/login-general.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ Module::asset('cms:js/auth.js') }}"></script>
<script>
    // Removes validation states from any changed input.
    $(':input').on('input change', function() {
        $(this).removeClass('is-valid is-invalid').closest('form').find('.invalid-feedback, .valid-feedback').remove();
    });
</script>
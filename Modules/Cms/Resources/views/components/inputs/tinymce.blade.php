@if ( isset( $options ) )
    @php
        $opt = array_merge([
            'id'                => null,
            'rows'              => 10,
            'name'              => 'tinymce',
            'value'             => null,
            'help'              => null,
            'label'             => null,
            'uploader'          => true,
            'base_script'       => true,
            'required'          => false,
            'inline'            => false,
            'error_bag'         => null,
            'input_class'       => '',

        ], $options);

        if(! $opt['id']) $opt['id'] = $opt['name'];

        $errorName = str_replace('][', '.', $opt['name']);
        $errorName = str_replace('[', '.', $errorName);
        $errorName = str_replace(']', '', $errorName);

        $opt['error_bag'] = (! $opt['error_bag']) ? $errors : $errors->{$opt['error_bag']};
    @endphp

    <div class="form-group kt-form__group {{ $opt['inline'] ? 'row' : '' }}">
        @if($opt['label'])
            <label for="{{ $opt['id'] }}" class="{{ $opt['inline'] ? 'col-form-label col-lg-'.Str::before($opt['inline'], ':') : '' }}">
                <strong class="text-focus">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif
        <div class="{{ $opt['inline'] ? 'col-lg-'.Str::after($opt['inline'], ':') : '' }}">
            <div class="input-group {{ $opt['input_class'] }}">
                <textarea
                    name="{{ $opt['name'] }}"
                    class="form-control kt-input text-left"
                    id="{{ $opt['name'] }}"
                    rows="{{ $opt['rows'] }}">
                    {{ old($opt['name'], $opt['value']) }}
                </textarea>
            </div>
            @if($opt['error_bag']->has($errorName))
                <div class="form-control-feedback">
                    {{ $opt['error_bag']->first( $errorName ) }}
                </div>
            @endif
            @if($opt['help'])
                <span class="form-text text-muted">{{ $opt['help'] }}</span>
            @endif
        </div>
    </div>

    @push('scripts')
        @if($opt['base_script'])
            <script src="https://cdn.tiny.cloud/1/m0o34gfgl0cvz5d972izmnlqebgfnyy3h1hchs0uq2iqqda3/tinymce/5/tinymce.min.js" type="text/javascript"></script>
        @endif
        <script>
            var uploader_error = function( response ){
                var _strong = ( response.strong ) ? response.strong : '{{ __('cms::app.crud_messages.upload_error.title') }}',
                    msg = ( response.msg ) ? response.msg : '{{ __('cms::app.crud_messages.upload_error.description') }}';
                swal(_strong, msg, 'error');
            };
            // function myCustomOnInit() {
            //     $('form *:input[type!=hidden]:first').focus();
            // }
            var editore_{{ $opt['id'] }} = tinymce.init({
                // oninit : myCustomOnInit,
                setup: function (editor) {
                    editor.on('change', function () {
                        if(formUpdated !== undefined) formUpdated = true;
                        tinymce.triggerSave();
                    });
                },
                fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
                directionality : '{{ $langDirection }}',
                language: '{{ app()->getLocale() }}',
                selector:'#{{ $opt['name'] }}',
                convert_urls:true,
                relative_urls:false,
                remove_script_host:false,
                plugins: [
                    "advlist autolink lists link image{{ ( $opt['uploader'] ) ? ' media ' : ' ' }}charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor" , "toc"
                ],
                // paste_block_drop: true,
                // paste_data_images: true,
                // paste_as_text: true,
                // paste_enable_default_filters: true,
                // paste_filter_drop: true,
                // paste_merge_formats: true,
                // paste_convert_word_fake_lists: true,
                // smart_paste: true,
                toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code image | forecolor backcolor emoticons | visualblocks | sizeselect | fontselect |  fontsizeselect | toc",
                menubar: "insert",
                // content_css: [
                //     '//fonts.googleapis.com/css?family=Tajawal:300,300i,400,400i',
                // ],
                // setup: function (ed)
                // {
                //     ed.on('init', function ()
                //     {
                //         this.execCommand("fontName", false, "Tajawal, arial, sans-serif");
                //         this.execCommand("fontSize", false, "12pt");
                //     });
                // },
                @if ( $opt['uploader'] )
                    document_base_url: '{{ url('/') }}/',
                    images_upload_url: '{{ route('TinymceController@uploader') }}',
                    images_upload_handler: function (blobInfo, success, failure) {
                        $.ajax({
                            method: "POST",
                            url: '{{ route('TinymceController@uploader') }}',
                            data: { _token: "{{ csrf_token() }}", tinymce: blobInfo }
                        })
                        .done(function( response ) {
                            if ( ! response.success ) {
                                var _strong = ( response.strong ) ? response.strong : '{{ __('cms::app.crud_messages.upload_error.title') }}',
                                    msg = ( response.msg ) ? response.msg : '{{ __('cms::app.crud_messages.upload_error.description') }}';
                                failure( _strong + msg );
                            } else {
                                success( response.location );
                            }
                        })
                        .fail(function() {
                            failure('{{ __('cms::app.crud_messages.upload_error.description') }}');
                        });
                    },
                @endif
            });

            // $('#{{ $opt['id'] }}').closest('form').submit(function(){
            //     tinymce.triggerSave(true,true);
            // });
            // $( document ).ready(function() {
            //     window.scrollTo(30, 30);
            // });
         </script>
    @endpush
@endif

@if(isset($options))
    @php
        $opt = array_merge([
            'id'                    => null,
            'name'                  => 'attachments',
            'max_files'             => 50,
            'url'                   => route('AttachmentController@store'),
            'remove_url'            => route('AttachmentController@destroy'),
            'label'                 => 'Text Input',
            'placeholder'           => null,
            'help'                  => null,
            'required'              => false,
            'validation_rules'      => 'required|file|max:2048|mimes:jpeg,jpg,png,pdf',
            'sub_folder'            => null,
            'attachments'           => [],
            'error_bag'             => null,
            'model'                 => [],
            /**
             * Format: 3:9. The number before ':' represents the number of columns the label will take, while the number after represents
             * the number of columns the input field will take.
             */
            'inline'                => false,
        ], $options);

        if(! $opt['id']) $opt['id'] = $opt['name'];

        $errorName = str_replace('][', '.', $opt['name']);
        $errorName = str_replace('[', '.', $errorName);
        $errorName = str_replace(']', '', $errorName);

        $opt['model'] = array_merge([
            'id'    => null,
            'type'  => ''
        ], $opt['model']);

        $opt['error_bag'] = (! $opt['error_bag']) ? $errors : $errors->{$opt['error_bag']};
    @endphp
    <div class="form-group kt-form__group {{ $opt['inline'] ? 'row' : '' }}">

        @if($opt['label'])
            <label for="{{ $opt['id'] }}" class="{{ $opt['inline'] ? 'col-form-label col-lg-'.Str::before($opt['inline'], ':') : '' }}">
                <strong class="text-focus">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif

        <div class="{{ $opt['inline'] ? 'col-lg-'.Str::after($opt['inline'], ':') : '' }}">
            <div class="input-group">
                <div class="dropzone dropzone-default dropzone-success dz-clickable w-100" id="{{ $opt['id'] }}" name="attachments">
                    <div class="dropzone-msg dz-message needsclick">
                        <h3 class="dropzone-msg-title">{{ $opt['label'] }}</h3>
                        <span class="dropzone-msg-desc">{{ $opt['placeholder'] }}</span>
                    </div>
                </div>
            </div>
            <div id="attachment_ids_{{ $opt['name'] }}"></div>

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

    @push('styles')
        <style>
            .dropzone .dz-preview .dz-image {
                width: 100%;
                height: unset;
            }
            .dropzone .dz-preview .dz-image img {
                width: 100%;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            var model = {!! json_encode($opt['model']) !!},
                dropzone_{{ $opt['name'] }} = new Dropzone("#{{ $opt['id'] }}", {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                paramName: "attachment",
                url: "{{ $opt['url'] }}",
                addRemoveLinks: true,
                autoProcessQueue: true,
                uploadMultiple: false,
                parallelUploads: 1,
                maxFiles: {{ $opt['max_files'] }},
                timeout: 50000,
                dictCancelUpload: "{{ __('cms::global.cancel_upload') }}",
                dictCancelUploadConfirmation: "{!! __('cms::confirmations.confirm.cancel_upload.text') !!}",
                dictRemoveFile: "{{ __('cms::global.remove_file') }}",
                previewTemplate: `<div class="dz-preview dz-file-preview">
                    <div class="dz-image"><img data-dz-thumbnail /></div>
                    <div class="dz-details">
                        <div class="dz-size"><span data-dz-size></span></div>
                        <div class="dz-filename"><span data-dz-name></span></div>
                    </div>
                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    <input type="text" name="" class="form-control form-control-sm my-2 dz-title" placeholder="ادخل العنوان">
                    <textarea name="" class="form-control form-control-sm my-2 dz-description" placeholder="ادخل الوصف"></textarea>
                </div>`,
                init: function() {
                    this.on("complete", function(file) {
                        $(".dz-remove").html(`<button type="button" class="btn btn-sm btn-outline-danger mt-2">${this.options.dictRemoveFile}</button>`);
                        let dzTitle = file.previewElement.querySelector(".dz-title")
                        let dzDescription = file.previewElement.querySelector(".dz-description")
                        dzTitle.name = 'attachment_title['+file.id+']';
                        dzDescription.name = 'attachment_description['+file.id+']';
                        dzTitle.value = file.title ? file.title :  file.name.split('.')[0];
                        dzDescription.value = file.description ? file.description : '';
                    });
                    // uploaded files
                    var attachments = [];
                    var self = this;
                    @if(isset($opt['attachments']) && count($opt['attachments']))
                        // show already uploaded files
                        attachments = {!! json_encode($opt['attachments']) !!};

                        $.each(attachments, function (i, file) {
                            // Create a mock uploaded file:
                            var uploadedFile = {
                                id: file.id,
                                name: file.filename,
                                size: file.size,
                                type: file.mime,
                                title: file.title,
                                description: file.description
                            };

                            $('<input>').attr({
                                "type"  : 'hidden',
                                "id"    : 'attachment_' + file.id,
                                "name"  : "{{ $opt['name'] }}[]",
                                "value" : file.id
                            }).appendTo("#attachment_ids_{{ $opt['name'] }}");

                            // Call the default addedfile event
                            self.emit("addedfile", uploadedFile);

                            // Image? lets make thumbnail
                            if( file.mime.indexOf('image') !== -1) {
                                self.emit("thumbnail", uploadedFile, file.thumbnail);

                            } else {
                                // we can get the icon for file type
                                self.emit("thumbnail", uploadedFile, file.thumbnail);
                            }

                            // fire complete event to get rid of progress bar etc
                            self.emit("complete", uploadedFile);
                        });
                    @endif

                    self.on('sending', function(file, xhr, formData) {
                        $(".dz-remove").html(`<button type="button" class="btn btn-sm btn-outline-danger mt-2">${this.options.dictRemoveFile}</button>`);

                        formData.append('input_name', "{{ $opt['name'] }}");
                        formData.append('validation_rules', "{{$opt['validation_rules']}}");file.id
                        formData.append('sub_folder', "{{$opt['sub_folder']}}");
                        formData.append('attachable_id', model.id);
                        formData.append('attachable_type', model.type);
                        formData.append('title', file.title);
                        formData.append('description', file.description);
                    });

                    self.on("error", function(file, response) {
                        self.removeFile(file);
                        var fileRef;
                        return (fileRef = file.previewElement) != null ? fileRef.parentNode.removeChild(file.previewElement) : void 0;
                    });

                    self.on("canceled", function(file) {
                        self.removeFile(file);
                    });

                    Dropzone.confirm = function(question, accepted, rejected) {
                        swal.fire({
                            title: '{!! __('cms::confirmations.confirm.procedure.title') !!}',
                            text: question,
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '{!! __('cms::confirmations.yes') !!}',
                            cancelButtonText: '{!! __('cms::confirmations.cancel') !!}',
                            reverseButtons: true
                        }).then(function(result){
                            if(result.value) {
                                accepted();
                            } else if (result.dismiss === 'cancel') {
                                rejected();
                            }
                        });
                    }
                },
                reset: function reset() {
                    if($(`[name="{{ $opt['name'] }}[]"]`).length == 0) this.element.classList.remove("dz-started");
                },
                removedfile: function(file)
                {
                    var _this = this;
                    var id = file.id;
                    if(id != undefined)
                    {
                        swal.fire({
                            title: '{!! __('cms::confirmations.confirm.remove_file.title') !!}',
                            text: '{!! __('cms::confirmations.confirm.remove_file.text') !!}',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '{!! __('cms::confirmations.yes') !!}',
                            cancelButtonText: '{!! __('cms::confirmations.cancel') !!}',
                            reverseButtons: true
                        }).then(function(result){
                            if(result.value) {

                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    type: 'POST',
                                    url: "{{ $opt['remove_url'] }}",
                                    data: {file_id: id},
                                    success: function (response){
                                        $('#attachment_' + id).remove();
                                        _this.emit("reset");

                                        show_toastr(response.type, response.title, response.description);

                                        var fileRef;
                                        return (fileRef = file.previewElement) != null ? fileRef.parentNode.removeChild(file.previewElement) : void 0;

                                    },
                                    error: function(e) {
                                        response = e.responseJSON;
                                        show_toastr(response.type, response.title, response.description);
                                    }
                                });

                            } else if (result.dismiss === 'cancel') {
                                swal.fire(
                                    '{{ __('cms::confirmations.confirm.remove_file.canceled.title') }}',
                                    '{{ __('cms::confirmations.confirm.remove_file.canceled.text') }}',
                                    'error'
                                );
                            }
                        });
                    }
                },
                error: function(file, response) {
                    let errors = '';

                    if(response.errors)
                    {
                        $.each(response.errors, function(key, value) {
                            errors += `<br><span>- ${Array.isArray(value) ? value[0] : value}</span>`
                        });
                    }

                    if(response.type) show_toastr(
                        response.type,
                        response.title,
                        response.description + errors,
                        false
                    );
                },
                success: function(file, response) {
                    let attachment = response.attachment;
                    file.id = attachment;
                    $('<input>').attr({
                        "type"  : 'hidden',
                        "id"    : 'attachment_' + attachment,
                        "name"  : "{{ $opt['name'] }}[]",
                        "value" : attachment
                    }).appendTo("#attachment_ids_{{ $opt['name'] }}");

                    show_toastr(response.type, response.title, response.description);
                }
            });
        </script>
    @endpush
@endif

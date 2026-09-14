<script>
    // Ajax Magic Function to submit a form and create or update records.
    function onFormSubmit(e, onSuccess, onFailure) {

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        e.preventDefault();
        e.stopPropagation();

        // Adds spinner to submit buttons while an ajax request is being made.
        $('.submit_form').each(function(i, e) {
            $(this).prop('disabled', true);
            $(this).addClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');
        });

        var form            = $(e.target),
            action          = form.attr('action'),
            method          = form.attr('method'),
            data            = new FormData(),
            submitBtn       = form.find('[type=submit]'),
            submitBtnHtml   = submitBtn.html();
            submitBtn.attr('disabled', true);
            onLoadingText = submitBtn.data('onLoadingText') ?? "@lang('cms::global.saving')";
            submitBtn.html(onLoadingText);

        var files = $("form input[type='file']");

        // Append form files to data array to send them with the request.
        $.each(files, function (key, input) {
            if(input.files[0] != undefined)
            {
                data.append(input.name, input.files[0]);
            }
        });

        // Append all form inputs to data array to send them with the request.
        $.each(form.serializeArray(), function (key, input) {
            data.append(input.name, input.value);
        });

        $.ajax({
            data:   data,
            url:    action,
            type:   method,
            processData: false,
            contentType: false,
            statusCode: {
                403: function(XHR) {
                    var data = JSON.parse(XHR.responseText);
                    show_toastr(data.type, data.title, data.description);
                }
            },
            success: function (data) {
                // Check complete function.
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // var data = JSON.parse(xhr.responseText);
                // switch (xhr.status) {
                //     case 403:

                //         break;
                //     case 500:
                //         if ( data.type && (data.title || data.description) ) {
                //             swal(data.title, data.description, data.type);
                //         }
                //         break;
                //     case 501:
                //         if ( data.type && (data.title || data.description) ) {
                //             swal(data.title, data.description, data.type);
                //         }
                //         break;
                //     default:
                //         show_toastr('danger', "@lang('cms::messages.general_error.title')", "@lang('cms::messages.general_error.description')");
                //         break;
                // }
            },
            complete : function(response) {
                var responseType = 'danger',
                    response = response.responseJSON,
                    firstInput = true;

                $(`.is-invalid`).removeClass('is-invalid');
                $(`.is-valid`).removeClass('is-valid');

                $('.valid-feedback, .invalid-feedback').remove();

                // To display inputs in red when a validation error occurs.
                if(response.type === 'validation_error')
                {

                    $.each(response.errors, function(key, value) {

                        // Transform dotted array to [] array to help select the arrays by their name attribute.
                        key = key.replace(/\.(.+?)(?=\.|$)/g, (m, s) => `[${s}]`);
                        var validatedInput = $(`[name^='${key}']:not([type='checkbox'])`),
                        invalidFeedback = '';

                        if(!validatedInput.length)
                        {
                            validatedInput = $(`.${key}:not([type='checkbox'])`);
                        }

                        // Moves the curser to the first invalid input.
                        if(firstInput)
                        {
                            $(`[name^='${key}']`).focus();
                            firstInput = false;
                        }

                        validatedInput.removeClass('is-valid').addClass('is-invalid');
                        validatedInput.parent('.form-control').removeClass('is-valid').addClass('is-invalid');

                        invalidFeedback += `<div class="invalid-feedback d-block">${Array.isArray(value) ? value[0] : value}</div>`;

                        if(validatedInput.closest('.input-group').length)
                        {
                            $(invalidFeedback).insertAfter(validatedInput.closest('.input-group'));
                        }
                        else
                        {
                            validatedInput.closest('.form-group').append(invalidFeedback);
                        }
                    });
                }
                // Displays inputs in green when form submit suceeds.
                else if(response.type === 'validation_success')
                {
                    responseType = 'success';

                    $('.is-invalid').removeClass('is-invalid').addClass('is-valid');

                    // Removes .is-valid class after a period of time.
                    setTimeout(
                        function()
                        {
                            $('.is-valid').removeClass('is-valid');
                        }, 2000);

                    // Closes open modals.
                    $('.modal').modal('hide');
                }
                else
                {
                    responseType = response.type;
                }

                // Redirects to a url if present.
                if(response.redirect_url)
                {
                    window.location = response.redirect_url;
                }

                // Refresh datatables if there are.
                if($('#datatable').length)
                {
                    $('#datatable').DataTable().ajax.reload(function() {
                        show_toastr(responseType, response.title, response.description);
                    }, false);

                    if(response.type === 'validation_success')
                    {
                        $('.modal').modal('hide');
                    }
                }
                else
                {
                    show_toastr(responseType, response.title, response.description);
                }

                // Optional functions that could be triggered after
                if(responseType == 'validation_success' || responseType == 'success')
                {
                    if (onSuccess) onSuccess(response);
                }
                else
                {
                    if (onFailure) onFailure(response);
                }

                // Checks which first tab of .nav-tabs has an invalid input and adds .active class.
                $('.tab-content :input').each(function(i, e) {
                    if($(this).hasClass('is-invalid'))
                    {
                        let tab = $(this).closest('.tab-pane');

                        tab.addClass('active')
                            .siblings('.tab-pane')
                            .removeClass('active');

                        let navLink = $(`[href="#${tab.attr('id')}"]`);

                        navLink.addClass('active')
                            .parent('.nav-item')
                            .siblings('.nav-item')
                            .children('.nav-link')
                            .removeClass('active');

                        // Breaks out of the loop once this condition is met.
                        return false;
                    }
                });

                submitBtn.html( submitBtnHtml );
                submitBtn.attr('disabled', false);

                $('.submit_form').each(function(i, e) {
                    $(this).removeClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');
                    $(this).prop('disabled', false);
                });
            }
        });
        return false;
    };
</script>

<script>
    var lazyLoadInstance = new LazyLoad({
        // Your custom settings go here
    });
    
    $(".change-currency").click(function(){
        var curr = $(this).data().name;
        $( "#currency-form" ).find("input[name=currency]").val( curr );
        $( "#currency-form" ).submit();
    });
    $(document).on("click",".click-image", function () {
        var full_route = $(this).find('.image-href-val').attr('href');
        window.location = full_route;
    });
    // $(function() {
    //     var _isRtl = $('html').attr('dir') == 'rtl';
    //     $(".price-slider").slider({
    //         range: true,
    //         isRTL: _isRtl,
    //         min: 5000,
    //         max: 20000000,
    //         step: 1000,
    //         values: [$("input[name='min_price']").length < 1 || $("input[name='min_price']").val() == '' ? 5000 : $("input[name='min_price']").val(), $("input[name='max_price']").length < 1 || $("input[name='max_price']").val() == '' ? 20000000 : $("input[name='max_price']").val()],
    //         slide: function (event, ui) {
    //             $(".slider_amount").val("{{$selectedCurrency->currency_symbol}} " + ui.values[0].toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + " - {{$selectedCurrency->currency_symbol}} " + ui.values[1].toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
    //             $("input[name='min_price']").val(ui.values[0]);
    //             $("input[name='max_price']").val(ui.values[1]);
    //         }
    //     });
    // });


    function cookies(){
        let c_token = "{{csrf_token()}}";
        $.ajax({
            url: '{!! route('FrontendController@cookies') !!}' ,
            type: "POST",
            data: {_token:c_token},
            cache: true,
            // dataType: 'html',
            beforeSend: function(data){
                
            },
            success: function(data){
                // $('.cookies').hide('slow');
                $('.cookies').fadeOut(800);
            }
        });
    }
    var _isRtl = $('html').attr('dir') == 'rtl';
    if(_isRtl){
        setTimeout(function(){ 
            $('.mm-title').text('القائمة');
        }, 1000);
    }
    
    function isValid(input_name){
        $(`input[name=${input_name}]`).css('border','1px solid #ddd');
        $(`textarea[name=${input_name}]`).css('border','1px solid #ddd');
        $(`#${input_name}`).text(' ');
        if(input_name == 'phone')
        {
            if(_isRtl)
            {
                $('.btn-select').css('border-right','1px solid #ddd');
            }
            else{
                $('.btn-select').css('border-left','1px solid #ddd');
            }
            $('.btn-select').css('border-top','1px solid #ddd');
                $('.btn-select').css('border-bottom','1px solid #ddd');
        }
    }
    function showMessage(message,status){
        if(status == true){
            $('#success_message_icon').addClass("fa fa-check");
        }else{
            $('#success_message_icon').addClass("fa fa-times");
        }
        $('.message-span').text(message);
        $('.success-message').fadeIn('show');
        setTimeout(function(){
            $('.success-message').fadeOut('slow');
        },3000);
    }
    function submitFroms(e){
        
        e.preventDefault();
        e.stopPropagation();
        $('.save-btn').prop('disabled', true);
        $('.save-btn').css('opacity', 0.7);
        var update_text     = $('.update-text').text();

        $('.update-text').text(' ');
        $('#spinner_div').addClass('spinner');
        $('.validate-form-message').text(' ');

        var form            = $(e.target),
            action          = form.attr('action'),
            method          = form.attr('method'),
            data            = new FormData(),
            submitBtn       = form.find('[type=submit]'),
            submitBtnHtml   = submitBtn.html();
            submitBtn.attr('disabled', true);

            // onLoadingText = submitBtn.data('onLoadingText') ?? "@lang('frontend::main.saving')";
            // submitBtn.html(onLoadingText);

        $.each(form.serializeArray(), function (key, input) {
            data.append(input.name, input.value);
            let replace         = input.name.replace("[]", "");
            if(replace != 'property_images'){
                $(`input[name=${input.name}]`).css('border','1px solid #ddd');
                $(`textarea[name=${input.name}]`).css('border','1px solid #ddd');
                if(input.name == 'phone')
                {
                    if(_isRtl)
                    {
                        $('.btn-select').css('border-right','1px solid #ddd');
                    }
                    else{
                        $('.btn-select').css('border-left','1px solid #ddd');
                    }
                    $('.btn-select').css('border-top','1px solid #ddd');
                        $('.btn-select').css('border-bottom','1px solid #ddd');
                }
            }
        });
        
        $.ajax({
            data:   data,
            url:    action,
            type:   method,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#spinner_div').removeClass('spinner');
                $('.update-text').text(update_text);
                $('.save-btn').prop('disabled', false);
                $('.save-btn').css('opacity', 1);
                if(data.success){
                    if(data.disabled){
                        $('.save-btn').prop('disabled', true);
                    }
                    if(data.redirect_url)
                    {
                        window.location = data.redirect_url;
                    }
                    showMessage(data.message,true);
                    
                }else if(data.success == false){
                    showMessage(data.message,false);
                }else{
                    showMessage("{!!trans('frontend::main.worng_inputs')!!}",false);
                    $.each(data, function(key, value) {
                        $(`input[name=${key}]`).css('border','1px solid red');
                        $(`textarea[name=${key}]`).css('border','1px solid red');
                        $(`#${key}`).text(value);
                        if(key == 'phone')
                        {
                            $('.btn-select').css('border-top','1px solid red');
                            $('.btn-select').css('border-bottom','1px solid red');
                            if(_isRtl)
                            {
                                $('.btn-select').css('border-right','1px solid red');
                            }
                            else{
                                $('.btn-select').css('border-left','1px solid red');
                            }
                        }
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
            },
            complete : function(response) {
                
            }
        });
        return false;
    }

    function submitFromsEmails(e){
        
        e.preventDefault();
        e.stopPropagation();
        $('.save-btn-emails').prop('disabled', true);
        $('.save-btn-emails').css('opacity', 0.7);
        var update_text     = $('.update-text-emails').text();

        $('.update-text-emails').text(' ');
        $('#spinner_div_emails').addClass('spinner-emails');
        $('.validate-form-message-emails').text(' ');

        var form            = $(e.target),
            action          = form.attr('action'),
            method          = form.attr('method'),
            data            = new FormData(),
            submitBtn       = form.find('[type=submit]'),
            submitBtnHtml   = submitBtn.html();
            submitBtn.attr('disabled', true);
            // onLoadingText = submitBtn.data('onLoadingText') ?? "@lang('frontend::main.saving')";
            // submitBtn.html(onLoadingText);

        $.each(form.serializeArray(), function (key, input) {
            data.append(input.name, input.value);
            let replace         = input.name.replace("[]", "");
            if(replace != 'property_images'){
                $(`input[name=${input.name}]`).css('border','1px solid #ddd');
                $(`textarea[name=${input.name}]`).css('border','1px solid #ddd');
            }
        });
        $.ajax({
            data:   data,
            url:    action,
            type:   method,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#spinner_div_emails').removeClass('spinner-emails');
                $('.update-text-emails').text(update_text);
                $('.save-btn-emails').prop('disabled', false);
                $('.save-btn-emails').css('opacity', 1);
                if(data.success){
                    showMessage(data.message,true);
                    $('.save-btn-emails').prop('disabled', true);
                    if(data.redirect_url)
                    {
                        window.location = data.redirect_url;
                    }
                }else if(data.success == false){
                    showMessage(data.message,false);
                }else{
                    showMessage("{!!trans('frontend::main.worng_inputs')!!}",false);
                    $.each(data, function(key, value) {
                        $(`input[name=${key}]`).css('border','1px solid red');
                        $(`textarea[name=${key}]`).css('border','1px solid red !important');
                        $(`#${key}`).text(value);
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
            },
            complete : function(response) {
                
            }
        });
        return false;
    }
    var langArray = [];
    $('.vodiapicker option').each(function(){
        var img = $(this).attr("data-thumbnail");
        var text = this.innerText;
        var value = $(this).val();
        var item = '<li><img src="'+ img +'" alt="" value="'+value+'"/><span>'+ text +'</span></li>';
        langArray.push(item);
    })
    $('#a-item').html(langArray);
    //Set the button value to the first el of the array
    $('.btn-select').html(langArray[0]);
    $('.btn-select').attr('value', 'en');
    //change button stuff on click
    $('#a-item li').click(function(){
        var img = $(this).find('img').attr("src");
        var value = $(this).find('img').attr('value');
        var text = this.innerText;
        var item = '<li><img src="'+ img +'" alt="" /><span>'+ text +'</span></li>';
        $('.btn-select').html(item);
        $('.btn-select').attr('value', value);
        $(".b-item").toggle();
        //console.log(value);
        // edit
        $('.op-val').val(value);
    });
    $(".btn-select").click(function(){
        $(".b-item").toggle();
    });
    //check local storage for the lang
    var sessionLang = localStorage.getItem('lang');
    if (sessionLang){
    //find an item with value of sessionLang
        var langIndex = langArray.indexOf(sessionLang);
        $('.btn-select').html(langArray[langIndex]);
        $('.btn-select').attr('value', sessionLang);
    } else {
        var langIndex = langArray.indexOf('ch');
        console.log(langIndex);
        $('.btn-select').html(langArray[langIndex]);
        //$('.btn-select').attr('value', 'en');
    }
</script>
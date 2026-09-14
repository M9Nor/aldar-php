<script>
    var _isRtl = $('html').attr('dir') == 'rtl';
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
    $(document).ready(function(){
        $(".more-filter").click(function(){
            $(".filter-div").toggle(400);
        });
    });
    $(".payment-select").change(function() {
        var id      = $(this).children(":selected").data().id;
        var url     = '{{ route("ListingController@installments", ":id") }}';
        url         = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                if (response.success){
                    $('select.installment-select').empty().append($(`<option disabled value=""> {{__('frontend::main.filter_section.dropdown.item_10')}} </option>`));;
                    $('select.installment-select').selectpicker('refresh');

                    if (response.data.length == 0)
                        return
                    $.each(response.data, function(key, value) {
                        $('select.installment-select')
                            .append($("<option></option>")
                                .val(value.slug).data('id',value.id)
                                .text(value.title));
                    });

                    $('select.installment-select').selectpicker('refresh');
                }
            },
            error: (errors) => {
                console.log(error)
            }
        })
    });
    $(".city-select").change(function () {
        if ($(this).val() == 'turkey' || $(this).val() == 'all') {
                  $('select.region-select').empty();
                        $('select.region-select').append($("<option @if(!$area)selected @endif></option>").val('').text("{{__('frontend::main.filter_section.dropdown.select_area')}}"));
                         $('select.region-select').append($("<option  @if($area == 'all')selected @endif></option>").val('all').text("{{__('frontend::main.filter_section.dropdown.all_area')}}"));
            var id = '';
        } else {
            var id = $(this).children(":selected").data().id;
        }

        var url = '{{ route("ListingController@regions", ":id") }}';

        url = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                if (response.success) {
                    $('select.region-select').empty();
                        $('select.region-select').append($("<option @if(!$area)selected @endif></option>").val('').text("{{__('frontend::main.filter_section.dropdown.select_area')}}"));
                         $('select.region-select').append($("<option  @if($area == 'all')selected @endif></option>").val('all').text("{{__('frontend::main.filter_section.dropdown.all_area')}}"));

                    if (response.data.length == 0)
                        return
                    $.each(response.data, function (key, value) {
                        $('select.region-select')
                            .append($(`<option ${value.native_name == "{{$area}}" ? 'selected' : ''}></option>`)
                                .val(value.native_name).data('id', value.id)
                                .text(value.name));
                    });
                }
            },
            error: (errors) => {
                console.log(error)
            }
        })
    });
    $(".city-select").trigger('change');
    // side features
    $('.slick-lancers.slick-lancers-4').slick({
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        rtl: _isRtl,
        arrows: false,
        adaptiveHeight: true,
        responsive: [
        {
            breakpoint: 1292,
            settings: {
                dots: true,
                arrows: false,
                items: 1,
            }
        }, 
        {
            breakpoint: 993,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                arrows: false
            }
        }, 
        {
            breakpoint: 769,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                arrows: false
            }
        }]
    });
    // sort
    $("select[name=sort]").change(function () {
        var sort = $(this).children(":selected").val();
        var url = window.location.href;
        if (url.indexOf('?') == -1)
            url = url + '?sort=' + sort
        else if (url.indexOf('sort=') == -1)
            url = url + '&sort=' + sort
        else if (url.indexOf('sort=') != -1)
            url = url.replace(/(sort=)[^\&]+/, '$1' + sort)
        $('#result_sort').val(sort);
        window.location.href = url;
    });
    
</script>
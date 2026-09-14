<section class="">
    <div class="custom-container" style="padding-top:25px">
        <div class="row">
            <div class="col-md-3 mob-center">
                <img src="{{$model->translate(app()->getLocale())->headerLogo('original')}}" alt="" style="max-width: 200px">
            </div>
            <div class="col-md-8" style="text-align: center">
                <h1 class="" style="color: {{$main_color}}">{!! $model->translateOrFirst()->header_h1 !!}</h1>
                <h2 class="" style="color: {{$second_color}}"> {!! $model->translateOrFirst()->header_h2 !!} </h2> 
            </div>
        </div>
    </div>
</section>
<section class="hero-section " style="padding-bottom: 50px">
    <div class="custom-container">
        <div class="row align-items-center justify-content-between">
            @if($model->form_direction == 'left')
                <div class="{{$model->form == 'yes' ? 'col-md-6 col-lg-8 col-sm-12' : 'col-sm-12'}} min-image"
                    style="background: url('{{$model->translate(app()->getLocale())->headerBackground('original')}}')">
                </div>
                @if($model->form == 'yes')
                    <div class="col-md-6 col-lg-4">
                        <div class="sign-up-form-wrap position-relative rounded gray-light-bg">
                            <div class="sign-up-form-header text-center mb-4">
                                @if(!empty($model->header_h1_above))
                                    <h2 style="color: {{$main_color}}" class="mb-0 header-above">{{$model->header_h1_above}}</h2>
                                @endif
                                @if(!empty($model->header_h2_above))
                                    <p style="color: {{$second_color}}">{{$model->header_h2_above}}</p>
                                @endif
                            </div>
                            <div class="message-box d-none">
                                <div class="alert alert-danger"></div>
                            </div>
                            <form id="form_contact" onsubmit="submitFroms(event);" action="{{route('ContactController@store')}}" method="POST" class="sign-up-form">
                                @csrf
                                <div class="form-group input-group">
                                    <input required onchange="isValid('fullname')" type="text" name="fullname" class="form-control input-form" placeholder="{{__('frontend::main.contact_form.name')}} *">
                                    <p class="validate-form-message" id="fullname"></p>
                                </div>
                                <div class="form-group input-group">
                                    @if(app()->getLocale() == 'ar' || app()->getLocale() == 'fa')
                                        <input required onchange="isValid('phone')" type="number" name="phone" class="form-control input-form number-flag" placeholder="{{__('frontend::main.contact_form.phone')}} *">
                                        <select class="vodiapicker" style="display: none">
                                            @foreach($all_countries as $key=>$country)
                                                <option class="{{$key == 0 ? 'test' : ''}}" data-thumbnail="{{$country->flag}}" value="{{$country->code}}">{{$country->code}}</option>
                                            @endforeach
                                        </select>
                                        <div class="lang-select lang-select2">
                                            <input class="op-val" type="hidden" name="country_code" value="+90">
                                            <button class="btn-select" value="" type="button"></button>
                                            <div class="b-item">
                                                <ul id="a-item" style="background:#fff"></ul>
                                            </div>
                                        </div>
                                    @else
                                        <div class="lang-select lang-select2">
                                            <input class="op-val" type="hidden" name="country_code" value="+90">
                                            <button class="btn-select" value="" type="button"></button>
                                            <div class="b-item">
                                                <ul id="a-item" style="background:#fff"></ul>
                                            </div>
                                        </div>
                                        <input required onchange="isValid('phone')" type="number" name="phone" class="form-control input-form number-flag" placeholder="{{__('frontend::main.contact_form.phone')}} *">
                                        <select class="vodiapicker" style="display: none">
                                            @foreach($all_countries as $key=>$country)
                                                <option class="{{$key == 0 ? 'test' : ''}}" data-thumbnail="{{$country->flag}}" value="{{$country->code}}">{{$country->code}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    {{-- <input required onchange="isValid('phone')" type="number" name="phone" class="form-control input-form" placeholder="{{__('frontend::main.contact_form.phone')}} *">
                                    <p class="validate-form-message" id="phone"></p> --}}
                                </div>
                                <div class="form-group input-group">
                                    <input required onchange="isValid('email')" class="form-control input-form" type="text" name="email" placeholder="{{__('frontend::main.contact_form.email')}} *">
                                    <p class="validate-form-message" id="email"></p>
                                </div>
                                <div class="form-group input-group">
                                    <textarea onchange="isValid('description')" name="description" placeholder="{{__('frontend::main.contact_form.description')}} *" 
                                        class="form-control input-form" cols="30" rows="4" ></textarea>
                                    <p class="validate-form-message" id="description"></p>
                                </div>
                                <div class="form-group">
                                    {{-- <input type="submit" name="submit" id="submit" class="btn solid-btn btn-block" value="Send"> --}}
                                    <button required class="multiple-send-message save-btn">
                                        <div id="spinner_div" class="">
                                            <div class="update-text" style="color: #fff;font-weight:bold">
                                                {{__('frontend::main.contact_form.send')}}
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                {{-- <div class="form-check d-flex align-items-center text-center">
                                    <input type="checkbox" class="form-check-input mt-0 mr-3" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">I agree your <a
                                            href="#">terms & conditions</a></label>
                                </div> --}}
                            </form>
                            @if(!empty($model->header_h1_under))
                                <h2 class="mb-0 header-under" style="text-align: center ; color: {{$main_color}}">{{$model->header_h1_under}}</h2>
                            @endif
                            @if(!empty($model->header_h2_under))
                                <p class="" style="text-align: center;color: {{$second_color}}">{{$model->header_h2_under}}</p>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                @if($model->form == 'yes')
                    <div class="col-md-6 col-lg-4">
                        <div class="sign-up-form-wrap position-relative rounded gray-light-bg">
                            <div class="sign-up-form-header text-center mb-4">
                                @if(!empty($model->header_h1_above))
                                    <h2 style="color: {{$main_color}}" class="mb-0 header-above">{{$model->header_h1_above}}</h2>
                                @endif
                                @if(!empty($model->header_h2_above))
                                    <p class="" style="color: {{$second_color}}">{{$model->header_h2_above}}</p>
                                @endif
                            </div>
                            <div class="message-box d-none">
                                <div class="alert alert-danger"></div>
                            </div>
                            <form id="form_contact" onsubmit="submitFroms(event);" action="{{route('ContactController@store')}}" method="POST" class="sign-up-form">
                                @csrf
                                <div class="form-group input-group">
                                    <input required onchange="isValid('fullname')" type="text" name="fullname" class="form-control input-form" placeholder="{{__('frontend::main.contact_form.name')}} *">
                                    <p class="validate-form-message" id="fullname"></p>
                                </div>
                                <div class="form-group input-group">
                                    @if(app()->getLocale() == 'ar' || app()->getLocale() == 'fa')
                                        <input required onchange="isValid('phone')" type="number" name="phone" class="form-control input-form number-flag" placeholder="{{__('frontend::main.contact_form.phone')}} *">
                                        <select class="vodiapicker" style="display: none">
                                            @foreach($all_countries as $key=>$country)
                                                <option class="{{$key == 0 ? 'test' : ''}}" data-thumbnail="{{$country->flag}}" value="{{$country->code}}">{{$country->code}}</option>
                                            @endforeach
                                        </select>
                                        <div class="lang-select lang-select2">
                                            <input class="op-val" type="hidden" name="country_code" value="+90">
                                            <button class="btn-select" value="" type="button"></button>
                                            <div class="b-item">
                                                <ul id="a-item" style="background:#fff"></ul>
                                            </div>
                                        </div>
                                    @else
                                        <div class="lang-select lang-select2">
                                            <input class="op-val" type="hidden" name="country_code" value="+90">
                                            <button class="btn-select" value="" type="button"></button>
                                            <div class="b-item">
                                                <ul id="a-item" style="background:#fff"></ul>
                                            </div>
                                        </div>
                                        <input required onchange="isValid('phone')" type="number" name="phone" class="form-control input-form number-flag" placeholder="{{__('frontend::main.contact_form.phone')}} *">
                                        <select class="vodiapicker" style="display: none">
                                            @foreach($all_countries as $key=>$country)
                                                <option class="{{$key == 0 ? 'test' : ''}}" data-thumbnail="{{$country->flag}}" value="{{$country->code}}">{{$country->code}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <p class="validate-form-message" id="phone"></p>
                                </div>
                                <div class="form-group input-group">
                                    <input required onchange="isValid('email')" class="form-control input-form" type="text" name="email" placeholder="{{__('frontend::main.contact_form.email')}} *">
                                    <p class="validate-form-message" id="email"></p>
                                </div>
                                <div class="form-group input-group">
                                    <textarea onchange="isValid('description')" name="description" placeholder="{{__('frontend::main.contact_form.description')}} *" 
                                        class="form-control input-form" cols="30" rows="4" ></textarea>
                                    <p class="validate-form-message" id="description"></p>
                                </div>
                                <div class="form-group">
                                    {{-- <input type="submit" name="submit" id="submit" class="btn solid-btn btn-block" value="Send"> --}}
                                    <button required class="multiple-send-message save-btn">
                                        <div id="spinner_div" class="">
                                            <div class="update-text" style="color: #fff;font-weight:bold">
                                                {{__('frontend::main.contact_form.send')}}
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                {{-- <div class="form-check d-flex align-items-center text-center">
                                    <input type="checkbox" class="form-check-input mt-0 mr-3" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">I agree your <a
                                            href="#">terms & conditions</a></label>
                                </div> --}}
                            </form>
                            @if(!empty($model->header_h1_under))
                                <h2 class="mb-0 header-under" style="text-align: center;color: {{$main_color}}">{{$model->header_h1_under}}</h2>
                            @endif
                            @if(!empty($model->header_h2_under))
                                <p  style="text-align: center;color: {{$second_color}}">{{$model->header_h2_under}}</p>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="{{$model->form == 'yes' ? 'col-md-6 col-lg-8 col-sm-12' : 'col-sm-12'}} min-image"
                    style="background: url('{{$model->translate(app()->getLocale())->headerBackground('original')}}')">
                </div>
            @endif

        </div>
    </div>
</section>
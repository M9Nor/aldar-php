<section class="contact home-contact">
    <div class="custom-container">
        <div class="row">
            <div class="col-md-4 col-12">
                <div class="title">
                    <h2>{{__("frontend::main.contact")}}</h2>
                </div>
                <div class="we-desc">
                    {!!__("frontend::main.contact_desc")!!}
                </div>
                <br>
                <div class="first-footer">
                    <div class="contactus">
                        <ul>
                            <li>
                                @if(isset($contents['address']))
                                    @php
                                        $address = '';
                                        if(isset($contents['address']) && !is_null($contents['address']))
                                        {
                                            if(!empty($contents['address']->translateOrFirst()->description))
                                            {
                                                $address = $contents['address']->translateOrFirst()->description;
                                            }
                                            else
                                            {
                                                $address = $contents['address']->value;
                                            }
                                        }
                                    @endphp
                                    <div class="info">
                                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                                        <p class="in-p">{{$address}}</p>
                                    </div>
                                @endif
                            </li>
                            <li>
                                @if(isset($contents['mobile_number_1']))
                                    @php
                                        $mobile_number_1 = '';
                                        if(isset($contents['mobile_number_1']) && !is_null($contents['mobile_number_1']))
                                        {
                                            if(!empty($contents['mobile_number_1']->translateOrFirst()->description))
                                            {
                                                $mobile_number_1 = $contents['mobile_number_1']->translateOrFirst()->description;
                                            }
                                            else
                                            {
                                                $mobile_number_1 = $contents['mobile_number_1']->value;
                                            }
                                        }
                                    @endphp
                                    <div class="info">
                                        <i class="fa fa-phone" aria-hidden="true"></i>
                                        <p class="in-p">{{$mobile_number_1}}</p>
                                    </div>
                                @endif
                            </li>
                            <li>
                                @if(isset($contents['email']))
                                    @php
                                        $email = '';
                                        if(isset($contents['email']) && !is_null($contents['email']))
                                        {
                                            if(!empty($contents['email']->translateOrFirst()->description))
                                            {
                                                $email = $contents['email']->translateOrFirst()->description;
                                            }
                                            else
                                            {
                                                $email = $contents['email']->value;
                                            }
                                        }
                                    @endphp
                                    <div class="info">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                        <p class="in-p ti">{{$email}}</p>
                                    </div>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-12">
                <div class="row index-contact">
                    <div class="col-md-6 col-12">
                        @if(isset($contents['map']))
                            @php
                                $map = '';
                                if(isset($contents['map']) && !is_null($contents['map']))
                                {
                                    if(!empty($contents['map']->translateOrFirst()->description))
                                    {
                                        $map = $contents['map']->translateOrFirst()->description;
                                    }
                                    else
                                    {
                                        $map = $contents['map']->value;
                                    }
                                }
                            @endphp
                            <iframe src="{{$map}}" style="border:0;width:100%;height:300px" allowfullscreen="" loading="lazy"></iframe>
                        @endif
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="recent-post">
                            <div class="contact-form">
                                <form method="POST" onsubmit="submitFroms(event);" action="{{route('ContactController@store')}}" name="contact_form">
                                    @include('frontend::partials.honeypot', ['id' => 'home'])
                                    @csrf
                                    <div class="form-group">
                                        <input class="form-control" onchange="isValid('fullname')" type="text" name="fullname" placeholder="{{__('frontend::main.contact_form.name')}} *">
                                        <p class="validate-form-message" id="fullname"></p>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control number-flag" onchange="isValid('phone')" type="number" name="phone" placeholder="{{__('frontend::main.contact_form.phone')}} *" autocomplete="off">
                                        <span>
                                            <select class="vodiapicker" style="display: none">
                                                @foreach($all_countries as $key=>$country)
                                                    <option class="{{$key == 0 ? 'test' : ''}}" data-thumbnail="{{$country->flag}}" value="{{$country->code}}">{{$country->code}}</option>
                                                @endforeach
                                            </select>
                                            <div class="lang-select lang-select2 lang-select3">
                                                <input class="op-val" type="hidden" name="country_code" value="+90">
                                                <button class="btn-select" value="" type="button" style="height: 100%"></button>
                                                <div class="b-item">
                                                    <ul id="a-item" style="background:#fff"></ul>
                                                </div>
                                            </div>
                                        </span>
                                        <p class="validate-form-message" id="phone"></p>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" onchange="isValid('email')" type="text" name="email" placeholder="{{__('frontend::main.contact_form.email')}} *">
                                        <p class="validate-form-message" id="email"></p>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control asdasd" onchange="isValid('description')" 
                                        name="description" placeholder="{{__('frontend::main.contact_form.description')}} *"></textarea>
                                        <p class="validate-form-message" id="description"></p>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg save-btn">
                                            <div id="spinner_div" class="">
                                                <div class="update-text" >
                                                    {{__('frontend::main.contact_form.send')}}
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
                    <div class="col-md-12 col-12">
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
                </div>
            </div>
        </div>
    </div>
</section>
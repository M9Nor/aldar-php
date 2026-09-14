<div class="recent-post">
    <h5 class="font-weight-bold mb-4 widget-boxed-header">{{__('frontend::main.contact_form.title')}}</h5>
    <div class="contact-form">
        <form method="POST" onsubmit="submitFroms(event);" action="{{route('ContactController@store')}}" name="contact_form">
            @include('frontend::partials.honeypot', ['id' => 'side'])
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
<div class="kt-widget kt-widget--user-profile-3">
    <div class="kt-widget__top">
        
        <div class="kt-widget__pic kt-widget__pic--danger kt-font-danger kt-font-boldest kt-font-light kt-hidden">
            JM
        </div>
        <div class="kt-widget__content">
            <div class="kt-widget__head">
                
            </div>
            <div class="kt-widget__subhead">
                <h5 class="text-danger">
                    {{$model->sender}}
                </h5> 
                <p class="lead">
                    {{$model->description}}
                </p>
                @if(!empty($model->type_of_visit))
                    <p class="lead">
                        {{__('frontend::main.type_of_visit')}}: 
                        <br>
                        <a>{{__('frontend::main.'.$model->type_of_visit)}}</a>
                    </p>
                @endif
                @if(!empty($model->phone))
                    <p class="lead">
                        {{__('frontend::main.phone_number')}}: 
                        <br>
                        <a style="direction: ltr">{{$model->phone}}</a>
                    </p>
                @endif
                @if(!empty($model->residency_address))
                    <p class="lead">
                        {{__('frontend::main.residency_address')}}: 
                        <br>
                        <a>{{$model->residency_address}}</a>
                    </p>
                @endif
                @if(!empty($model->nationality))
                    <p class="lead">
                        {{__('frontend::main.nationality')}}: 
                        <br>
                        <a>{{$model->nationality}}</a>
                    </p>
                @endif
                @if(!empty($model->native_language))
                    <p class="lead">
                        {{__('frontend::main.native_language')}}: 
                        <br>
                        <a>{{$model->native_language}}</a>
                    </p>
                @endif
                @if(!empty($model->budget))
                    <p class="lead">
                        {{__('frontend::main.budget')}}: 
                        <br>
                        <a>{{$model->budget}}</a>
                    </p>
                @endif

                @if(!empty($model->type_of_residency))
                    <p class="lead">
                        {{__('frontend::main.type_of_residency')}}: 
                        <br>
                        <a>{{$model->type_of_residency}}</a>
                    </p>
                @endif
            
            
            </div>
        </div>
    </div>
</div>

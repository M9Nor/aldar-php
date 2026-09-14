<section class="promo-section" style="padding-top: 30px;margin-bottom:50px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-8">
                <div class="section-heading text-center mb-5">
                    <h2 style="color: {{$main_color}}">{!!trans('frontend::landing_page.services')!!}</h2>
                    <p style="color: {{$second_color}}" class="lead">
                        {!!trans('frontend::landing_page.services_desc')!!}
                    </p>
                </div>
            </div>
        </div>
        <div class="row equal">
            @foreach($services as $service)
                <div class="col-md-4 col-lg-4" style="margin-top: 25px">
                    <a href="{{route('ListingController@getContentBySlug', ['slug' => $service->slug])}}" target="_blank">
                        <div class="single-promo single-promo-hover single-promo-1 rounded text-center white-bg p-5 h-100">
                            <div class="circle-icon mb-5">
                                {{-- <span class="ti-vector text-white"></span> --}}
                                <img src="{{$service->getIconImage($service,'75x75', 'services')}}" alt="{{$service->translateOrFirst()->title}}">
                            </div>
                            <h5>{{$service->translateOrFirst()->title}}</h5>
                            <p style="color: #000">{{ Str::limit($service->translateOrFirst()->brief, 100) }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
<section id="blog" class="our-blog-section ptb-100 gray-light-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-12" style="text-align: center">
                <div class="section-heading mb-5">
                    <h2 style="color: {{$main_color}}">{!!trans('frontend::landing_page.blogs')!!}</h2>
                    <p style="color: {{$second_color}}">
                        {!!trans('frontend::landing_page.blogs_desc')!!}
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($blogs as $blog)
                @php
                    if(!isset($route)) {
                        $route = 'ListingController@articles';
                    }
                    if($route == 'ListingController@articles') {
                        $variables  = ['slug' => $blog->slug];
                    }
                @endphp
                <div class="col-md-4">
                    <div class="single-blog-card card border-0 shadow-sm">
                        {{-- <span class="category position-absolute badge badge-pill badge-primary">Lifestyle</span> --}}
                        <a href="{{route($route , $variables)}}">
                            <img src="{{ $blog->getTranslatedImage('1000x750')}}" class="card-img-top position-relative" alt="blog">
                            <div class="card-body">
                                <h3 class="h5 card-title"><a href="{{route($route , $variables)}}">{{$blog->translateOrFirst()->title}}</a></h3>
                                <p class="card-text">{{\Illuminate\Support\Str::limit((string) $blog->translateOrFirst()->brief, 160)}}</p>
                                <a href="{{route($route , $variables)}}" class="detail-link">{!!trans('frontend::landing_page.read_more')!!}
                                    {{-- <span class="ti-arrow-right"></span> --}}
                                </a>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
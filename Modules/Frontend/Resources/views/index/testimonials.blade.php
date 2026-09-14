<section class="testimonials homepage-3">
    <div class="custom-container">
        <div class="row">
            <div class="col-12">
                <div class="title">
                    <h2>{{__("frontend::main.testimonials")}}</h2>
                </div>
                <div class="desc">
                    {{__("frontend::main.testimonials_desc")}}
                </div>
            </div>
            <div class="col-12">
                <div class="slick-lancers slick-lancers-3">
                    @php
                        $defaultLangImage = route('image', ['size' => '270x270', 'path' => 'defaults/base.png']);
                    @endphp
                    @foreach($testimonials[app()->getLocale()] as $testimonial)
                        <div>
                            <div class="singleJobClinet">
                                <div class="detailJC">
                                    <span>
                                        <img class="lazy" src="{{$defaultLangImage}}" alt="{{$testimonial->translateOrFirst()->title}}" data-src="{{ $testimonial->getTranslatedImage('270x270') }}">
                                    </span>
                                    <h5>{!! $testimonial->translateOrFirst()->title !!}</h5>
                                    <div>{!! $testimonial->translateOrFirst()->brief !!}</div>
                                </div>
                                <div class="test-div">
                                    {!! $testimonial->translateOrFirst()->description !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
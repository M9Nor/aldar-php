<section class="stories">
    <div class="custom-container">
        {{-- <div class="section-title col-md-5">
            <h3>Meet Our</h3>
            <h2>Agents</h2>
        </div> --}}
        <div class="row">
            <div class="col-12">
                <div class="owl-carousel stories_slider customized-slider {{ $langDirection == 'rtl' ? 'owl-rtl' : '' }}">
                    @php
                        $defaultLangImage = route('image', ['size' => '180x180', 'path' => 'defaults/base.png']);
                    @endphp
                    @foreach ($stories[app()->getLocale()] as $story)
                        <div class="story-item">
                            <a target="_blank" rel="noopener" href="{{route('ListingController@getContentBySlug', ['slug' => $story->slug])}}" class="story-link">
                                <div class="story-circle">
                                    <img class="lazy" src="{{ $defaultLangImage }}" data-src="{{ $story->getTranslatedImage('180x180') }}" alt="{{$story->translateOrFirst()->title}}" />
                                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="enable-background:new -580 439 577.9 194;"
                                       xml:space="preserve">
                                      <circle cx="50" cy="50" r="40" />
                                    </svg>
                                </div>
                                <div class="story-details">
                                    <strong>{{$story->translateOrFirst()->title}}</strong>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
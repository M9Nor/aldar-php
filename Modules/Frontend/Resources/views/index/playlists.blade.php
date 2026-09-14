<section class="chanel">
    <div class="custom-container">
        <div class="row">
            <div class="col-12">
                <div class="title">
                    <h2>{{__("frontend::main.dar_chanel")}}</h2>
                </div>
                <div class="desc">
                    {{__("frontend::main.featured_projects_desc")}}
                </div>
            </div>
            <div class="col-12">
                @php
                    $defaultLangImage = route('image', ['size' => '861x825', 'path' => 'defaults/base.png']);
                    $defaultLangImage2 = route('image', ['size' => '795x259', 'path' => 'defaults/base.png']);
                @endphp
                <div class="row">
                    @foreach($playlists[app()->getLocale()] as $key => $item)
                        @if($key == 0)
                            <div class="col-lg-6 col-md-12 big-video">
                                <div class="property wprt-image-video">
                                    <img class="lazy" src="{{ $defaultLangImage }}" data-src="{{ $item->getTranslatedImage('861x825') }}" alt="{{$item->translateOrFirst()->title}}">
                                    <a class="icon-wrap popup-video popup-youtube single-video" href="{{$item->link}}">
                                        <i class="fa fa-play"></i>
                                    </a>
                                    <div class="iq-waves single-waves">
                                        <div class="waves wave-1"></div>
                                        <div class="waves wave-2"></div>
                                        <div class="waves wave-3"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <div class="col-lg-6 col-md-12">
                        @foreach($playlists[app()->getLocale()] as $key => $item)
                            @if($key != 0)
                                <div class="col-md-12 chanel-side-item">
                                    <div class="property wprt-image-video">
                                        <img class="lazy" src="{{ $defaultLangImage2 }}" data-src="{{ $item->getTranslatedImage('795x259') }}" alt="{{$item->translateOrFirst()->title}}">
                                        <a class="icon-wrap popup-video popup-youtube multiple-video" href="{{$item->link}}">
                                            <i class="fa fa-play"></i>
                                        </a>
                                        <div class="iq-waves multiple-waves">
                                            <div class="waves wave-1"></div>
                                            <div class="waves wave-2"></div>
                                            <div class="waves wave-3"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
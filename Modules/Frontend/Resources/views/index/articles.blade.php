<section class="blogs inner-pages">
    <div class="custom-container blog-section">
        <div class="row">
            <div class="col-12">
                <div class="title">
                    <h2>{{__("frontend::main.blogs")}}</h2>
                </div>
                <div class="desc">
                    {{__("frontend::main.blogs_desc")}}
                </div>
            </div>
            @php
                $defaultArticleImage = route('image', ['size' => '1000x750', 'path' => 'defaults/base.png']);
            @endphp
            @foreach ($articles[app()->getLocale()]->take(3) as $article)
                @php
                    if(!isset($route)) {
                        $route = 'ListingController@articles';
                    }
                    if($route == 'ListingController@articles') {
                        $variables  = ['slug' => $article->slug];
                    }
                @endphp
                <div class="col-lg-4 col-md-12 col-xs-12">
                    <div class="news-item">
                        <a href="{{route($route , $variables)}}" class="news-img-link">
                            <div class="news-item-img">
                                <img class="img-responsive lazy" data-src="{{ $article->getTranslatedImage('1000x750') }}" src="{{ $defaultArticleImage }}" alt="{{ $article->translateOrFirst()->title }}">
                            </div>
                        </a>
                        <div class="news-item-text">
                            <span>{{\Modules\Cms\Classes\DateHelper::parseDate($article->created_at, 'dS F Y')}}</span>
                            <a href="{{route($route , $variables)}}"><h3>{{ $article->translateOrFirst()->title }}</h3></a>
                            <div class="news-item-descr big-news">
                                <p>{{\Illuminate\Support\Str::limit((string) $article->translateOrFirst()->brief, 160)}}</p>
                            </div>
                            <div class="news-item-bottom">
                                <a href="{{route($route , $variables)}}" class="news-link">{{__("frontend::main.read_more")}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-12" style="margin-top: 30px;text-align:center">
                <a class="btn btn-primary btn-lg" href="{{ route('ListingController@articles')}}">
                    {{__("frontend::main.show_more")}}
                </a>
            </div>
        </div>
    </div>
</section>
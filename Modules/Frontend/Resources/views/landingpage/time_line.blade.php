@if($model->timeline->where('language','en')->isNotEmpty())
    <header>
        <div class="container text-center">
            <h1 style="text-align: center !important;color: {{$main_color}}">{{$model->timeline_title}}</h1>
        </div>
    </header>

    <section class="timeline" style="direction: ltr">
        <div class="container">
            @foreach($model->timeline->sortBy('sort_order') as $key => $t)
                @if($t->language == app()->getLocale())
                    @php
                        $key++;
                        $even   = 0;
                        $odd    = 0;
                        if($key % 2 == 0){
                            $even   = 1;
                        }else{
                            $odd    = 1;
                        }
                    @endphp
                        <div class="timeline-item">
                            @if(!empty($t->icon))
                                <div class="timeline-img">
                                    <img src="{{$t->getIconImage($t, '100x100')}}" alt="">
                                </div>
                            @else
                                <div class="timeline-img" style="background: {{$main_color}}">

                                </div>
                            @endif
                            <div 
                                class="timeline-content {{$odd == 1 ? 'js--fadeInLeft' : ' '}} {{$even == 1 ? 'timeline-card js--fadeInRight' : ' '}}" 
                                style="height: {{is_null($t->image) ? '' : '400px'}}"
                            >
                                @if(empty($t->image))
                                    <h2>{{$t->title}}</h2>
                                @else
                                    <div class="timeline-img-header" 
                                    style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.4)), url('{{$t->getImage($t, '698x500')}}') center center no-repeat;background-size: cover">
                                        <h2>{{$t->title}}</h2>
                                    </div>
                                @endif
                                <div class="date-timeline" style="background: {{$second_color}}">{{$t->date}}</div>
                                <p>{{$t->description}}</p>
                                <a target="_blank" class="bnt-more a-timeline" href="{{$t->link}}" style="background: {{$main_color}}">{!!trans('frontend::landing_page.more')!!}</a>
                            </div>
                        </div>
                    {{-- @if($even == 1)
                        <div class="timeline-item">
                            <div class="timeline-img"></div>
                            <div class="timeline-content timeline-card js--fadeInRight" >
                                @if(empty($t->image))
                                    <h2>{{$t->title}}</h2>
                                @else
                                    <div class="timeline-img-header" 
                                    style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.4)), url('{{$t->getImage($t, '698x500')}}') center center no-repeat">
                                        <h2>{{$t->title}}</h2>
                                    </div>
                                @endif
                                <div class="date-timeline">{{$t->date}}</div>
                                <p>
                                    {{$t->description}}
                                </p>
                                <a class="bnt-more a-timeline" href="{{$t->link}}">{!!trans('frontend::landing_page.more')!!}</a>
                            </div>
                        </div>
                    @endif --}}
                @endif
            @endforeach
        </div>
    </section>
@endif
{{-- <section class="timeline" style="direction: ltr">
    <div class="container">
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content js--fadeInLeft">
                <h2>Title</h2>
                <div class="date-timeline">1 MAY 2016</div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime ipsa ratione omnis alias cupiditate saepe atque totam aperiam sed nulla voluptatem recusandae dolor, nostrum excepturi amet in dolores. Alias, ullam.</p>
                <a class="bnt-more a-timeline" href="javascript:void(0)">More</a>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content timeline-card js--fadeInRight">
                <div class="timeline-img-header">
                    <h2>Card Title</h2>
                </div>
                <div class="date-timeline">25 MAY 2016</div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime ipsa ratione omnis alias cupiditate saepe atque totam aperiam sed nulla voluptatem recusandae dolor, nostrum excepturi amet in dolores. Alias, ullam.</p>
                <a class="bnt-more a-timeline" href="javascript:void(0)">More</a>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content js--fadeInLeft">
                <div class="date-timeline">3 JUN 2016</div>
                <h2>Quote</h2>
                <blockquote>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta explicabo debitis omnis dolor iste fugit totam quasi inventore!</blockquote>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content js--fadeInRight">
                <h2>Title</h2>
                <div class="date-timeline">22 JUN 2016</div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime ipsa ratione omnis alias cupiditate saepe atque totam aperiam sed nulla voluptatem recusandae dolor, nostrum excepturi amet in dolores. Alias, ullam.</p>
                <a class="bnt-more a-timeline" href="javascript:void(0)">More</a>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content timeline-card js--fadeInLeft">
                <div class="timeline-img-header">
                    <h2>Card Title</h2>
                </div>
                <div class="date-timeline">10 JULY 2016</div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime ipsa ratione omnis alias cupiditate saepe atque totam aperiam sed nulla voluptatem recusandae dolor, nostrum excepturi amet in dolores. Alias, ullam.</p>
                <a class="bnt-more a-timeline" href="javascript:void(0)">More</a>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content timeline-card js--fadeInRight">
                <div class="timeline-img-header">
                    <h2>Card Title</h2>
                </div>
                <div class="date-timeline">30 JULY 2016</div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime ipsa ratione omnis alias cupiditate saepe atque totam aperiam sed nulla voluptatem recusandae dolor, nostrum excepturi amet in dolores. Alias, ullam.</p>
                <a class="bnt-more a-timeline" href="javascript:void(0)">More</a>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content js--fadeInLeft">
                <div class="date-timeline">5 AUG 2016</div>
                <h2>Quote</h2>
                <blockquote>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta explicabo debitis omnis dolor iste fugit totam quasi inventore!</blockquote>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content timeline-card js--fadeInRight">
                <div class="timeline-img-header">
                    <h2>Card Title</h2>
                </div>
                <div class="date-timeline">19 AUG 2016</div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime ipsa ratione omnis alias cupiditate saepe atque totam aperiam sed nulla voluptatem recusandae dolor, nostrum excepturi amet in dolores. Alias, ullam.</p>
                <a class="bnt-more a-timeline" href="javascript:void(0)">More</a>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-img"></div>
            <div class="timeline-content js--fadeInLeft">
                <div class="date-timeline">1 SEP 2016</div>
                <h2>Title</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime ipsa ratione omnis alias cupiditate saepe atque totam aperiam sed nulla voluptatem recusandae dolor, nostrum excepturi amet in dolores. Alias, ullam.</p>
                <a class="bnt-more a-timeline" href="javascript:void(0)">More</a>
            </div>
        </div>
    </div>
</section> --}}
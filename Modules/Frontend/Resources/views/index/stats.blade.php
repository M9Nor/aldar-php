<!-- START SECTION COUNTER UP -->
<section class="counterup">
    <div class="container">
        <div class="row">
            @if(isset($contents['years_of_experience']))
                <div class="col-lg-4 col-md-6 col-xs-12">
                    <div>
                        <img class="stats-icons" src="{{\Module::asset('frontend:assets/years_of_experience.svg')}}" alt="{{ $contents['years_of_experience']->translateOrFirst()->title }}">
                    </div>
                    <div class="countr">
                        @php
                            $years_of_experience = '';
                            if(isset($contents['years_of_experience']) && !is_null($contents['years_of_experience']))
                            {
                                if(!empty($contents['years_of_experience']->translateOrFirst()->description))
                                {
                                    $years_of_experience = $contents['years_of_experience']->translateOrFirst()->description;
                                }
                                else
                                {
                                    $years_of_experience = $contents['years_of_experience']->value;
                                }
                            }
                        @endphp
                        <div class="count-me">
                            <p class="counter">{{ $years_of_experience }}</p>
                            <h3>{{ $contents['years_of_experience']->translateOrFirst()->title }}</h3>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($contents['project_count']))
                <div class="col-lg-4 col-md-6 col-xs-12">
                    <div class="countr">
                        <div>
                            <img class="stats-icons" src="{{\Module::asset('frontend:assets/project_count.svg')}}" alt="{{ $contents['project_count']->translateOrFirst()->title }}">
                        </div>
                        @php
                            $project_count = '';
                            if(isset($contents['project_count']) && !is_null($contents['project_count']))
                            {
                                if(!empty($contents['project_count']->translateOrFirst()->description))
                                {
                                    $project_count = $contents['project_count']->translateOrFirst()->description;
                                }
                                else
                                {
                                    $project_count = $contents['project_count']->value;
                                }
                            }
                        @endphp
                        <div class="count-me">
                            <p class="counter">{{ $project_count }}</p>
                            <h3>{{ $contents['project_count']->translateOrFirst()->title }}</h3>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($contents['happy_clients']))
                <div class="col-lg-4 col-md-6 col-xs-12">
                    <div class="countr mb-0">
                        <div>
                            <img class="stats-icons" src="{{\Module::asset('frontend:assets/happy_clients.svg')}}" alt="{{ $contents['happy_clients']->translateOrFirst()->title }}">
                        </div>
                        @php
                            $happy_clients = '';
                            if(isset($contents['happy_clients']) && !is_null($contents['happy_clients']))
                            {
                                if(!empty($contents['happy_clients']->translateOrFirst()->description))
                                {
                                    $happy_clients = $contents['happy_clients']->translateOrFirst()->description;
                                }
                                else
                                {
                                    $happy_clients = $contents['happy_clients']->value;
                                }
                            }
                        @endphp
                        <div class="count-me">
                            <p class="counter">{{ $happy_clients }}</p>
                            <h3>{{ $contents['happy_clients']->translateOrFirst()->title }}</h3>
                        </div>
                    </div>
                </div>
            @endif
            {{-- @if(isset($contents['sales']))
                <div class="col-lg-3 col-md-6 col-xs-12">
                    <div class="countr mb-0 last">
                        <div>
                            <img class="stats-icons" src="{{\Module::asset('frontend:assets/sales.svg')}}" alt="{{ $contents['sales']->translateOrFirst()->title }}">
                        </div>
                        @php
                            $sales = '';
                            if(isset($contents['sales']) && !is_null($contents['sales']))
                            {
                                if(!empty($contents['sales']->translateOrFirst()->description))
                                {
                                    $sales = $contents['sales']->translateOrFirst()->description;
                                }
                                else
                                {
                                    $sales = $contents['sales']->value;
                                }
                            }
                        @endphp
                        <div class="count-me">
                            <p class="counter">{{ $sales }}</p>
                            <h3>{{ $contents['sales']->translateOrFirst()->title }}</h3>
                        </div>
                    </div>
                </div>
            @endif --}}
        </div>
    </div>
</section>
<!-- END SECTION COUNTER UP -->
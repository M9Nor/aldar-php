<section class="recent">
    <div class="custom-container">
        <div class="row">
            <div class="col-12">
                <div class="title">
                    <h2>{{__("frontend::main.recent")}}</h2>
                </div>
                <div class="desc">
                    {{__("frontend::main.recent_desc")}}
                </div>
            </div>
            <div class="col-12">
                <div class="portfolio col-xl-12 p-0 mt-5">
                    <div class="slick-lancers slick-lancers-2">
                        @php
                            $defaultLangImage = route('image', ['size' => '1000x750', 'path' => 'defaults/base.png']);
                        @endphp
                        @foreach($recentProjects as $project)
                            @include('frontend::includes.project_single_opp', ['project' => $project,'defaultLangImage' => $defaultLangImage])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
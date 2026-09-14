<section id="about" class="about-us" style="background: {{$model->subject_text_background}};padding:25px 0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-sm-12">
                <h2 class="mt-4" style="text-align: center;color:{{$main_color}}">{{$model->subject_text_h2}}</h2>
            </div>
            <div class="col-lg-12">
                <div style="text-align: center">
                    {!!$model->subject_text_desc!!}
                </div>
            </div>
        </div>
    </div>
</section>
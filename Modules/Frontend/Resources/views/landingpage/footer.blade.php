<footer class="footer-section">
    <div class="footer-bottom gray-light-bg pt-2 pb-2" style="color: #fff;background: {{$second_color}};">
        <div class="container">
            <div class="row text-center justify-content-center">
                <div class="col-md-5 col-lg-4 col-sm-12">
                    <div class="social-list-wrap">
                        <ul class="social-list list-inline list-unstyled">
                            @if(!empty($model->footer_facebook))
                                <li class="list-inline-item">
                                    <a href="{{$model->footer_facebook}}" target="_blank" title="Facebook">
                                        <span style="color: #fff" class="ti-facebook"></span>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($model->footer_instagram))
                                <li class="list-inline-item">
                                    <a href="{{$model->footer_instagram}}" target="_blank" title="Instagram">
                                        <span style="color: #fff" class="ti-instagram"></span>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($model->footer_youtube))
                                <li class="list-inline-item">
                                    <a href="{{$model->footer_youtube}}" target="_blank" title="youtube">
                                        <span style="color: #fff" class="ti-youtube"></span>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($model->footer_whatsapp))
                                <li class="list-inline-item">
                                    <a href="https://api.whatsapp.com/send?phone={{$model->footer_whatsapp}}" target="_blank" title="whatsapp">
                                        <span style="color: #fff" class="fa fa-phone" style="font-size: 20px"></span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-md-7 col-lg-8 col-sm-12">
                    <p class="copyright-text pb-0 mb-0" style="padding: 10px 0;">
                        {{__('frontend::landing_page.bottom_footer_1')}} &copy; </span>{{ date('Y') }} - 
                        {!! __('frontend::main.powred_by') !!} <a style="color:#fff;font-size:14px" href="https://www.namaa-solutions.com/{{ in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'ar' }}" target="_blank" rel="noopener">{{__('frontend::main.developer_name')}}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
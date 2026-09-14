@extends('frontend::landingpage.layout')

@section('content')

<!--header section end-->

    <!--body content wrap start-->
    <div class="main">

        <!--hero section start-->
        <section class="hero-section background-img pt-100"
                style="background: url('img/hero-bg-4.jpg')no-repeat center center / cover">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-6 col-lg-6">
                        <div class="hero-content-left position-relative mt-lg-0 mt-md-0 mt-5 text-white">
                            <h1 class="text-white"><span>Build anything</span> That you want</h1>
                            <p class="lead">Progressively monetize cost effective innovation vis-a-vis cross-unit infrastructures. Uniquely e-enable front-end core competencies vis-a-vis premier.</p>

                            <div class="video-promo-content mt-4 d-flex align-items-center">
                                <a href="https://www.youtube.com/watch?v=9No-FiEInLA" class="popup-youtube video-play-icon"><span class="ti-control-play"></span> </a>
                                <span class="ml-4">Watch video overview</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-lg-5">
                        <div class="sign-up-form-wrap position-relative rounded p-5 gray-light-bg mt-5">
                            <div class="sign-up-form-header text-center mb-4">
                                <h4 class="mb-0">Get a quote today</h4>
                                <p>Get response within 24 hours</p>
                            </div>
                            <div class="message-box d-none">
                                <div class="alert alert-danger"></div>
                            </div>
                            <form action="#" method="post" id="getQuoteFrm" class="sign-up-form">
                                <div class="form-group input-group">
                                    <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                                </div>
                                <div class="form-group input-group">
                                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                                </div>
                                <div class="form-group input-group">
                                    <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
                                </div>
                                <div class="form-group input-group">
                                    <textarea name="message" id="msg" class="form-control" placeholder="Write your message" cols="30" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="submit" id="submit" class="btn solid-btn btn-block" value="Send">
                                </div>
                                <div class="form-check d-flex align-items-center text-center">
                                    <input type="checkbox" class="form-check-input mt-0 mr-3" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">I agree your <a
                                            href="#">terms & conditions</a></label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-shape position-absolute">
                <img src="img/wave-shap.svg" alt="shape">
            </div>
        </section>
        <!--hero section end-->

        <!--promo section start-->
        <section class="promo-section ptb-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-8">
                        <div class="section-heading text-center mb-5">
                            <h2>Why small business owners <br><span>love AppCo?</span></h2>
                            <p class="lead">
                                Following reasons show advantages of adding AppCo to your lead pages, demos and
                                checkouts
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row equal">
                    <div class="col-md-4 col-lg-4">
                        <div class="single-promo single-promo-hover single-promo-1 rounded text-center white-bg p-5 h-100">
                            <div class="circle-icon mb-5">
                                <span class="ti-vector text-white"></span>
                            </div>
                            <h5>Clean Design</h5>
                            <p>Increase sales by showing true dynamics of your website.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="single-promo single-promo-hover single-promo-1 rounded text-center white-bg p-5 h-100">
                            <div class="circle-icon mb-5">
                                <span class="ti-lock text-white"></span>
                            </div>
                            <h5>Secure Data</h5>
                            <p>Build your online store’s trust using Social Proof & Urgency.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="single-promo single-promo-hover single-promo-1 rounded text-center white-bg p-5 h-100">
                            <div class="circle-icon mb-5">
                                <span class="ti-eye text-white"></span>
                            </div>
                            <h5>Retina Ready</h5>
                            <p>Realize importance of social proof in customer’s purchase decision.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--promo section end-->

        <!--about us section start-->
        <section id="about" class="about-us ptb-100 gray-light-bg">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="about-content-right">
                            <img src="img/delivery-app.svg" width="500" alt="about us" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <span class="badge badge-primary badge-pill">About our app</span>
                        <h2 class="mt-4">Deliver your product using AppCo</h2>
                        <p class="mb-4 lh-190">Quick has all the right tools in order to make your website building process
                            a breeze and automatize your time-consuming tasks in your development workflow.</p>
                        <ul class="list-unstyled">
                            <li class="py-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="badge badge-circle badge-primary mr-3">
                                            <span class="ti-check"></span>
                                        </div>
                                    </div>
                                    <div><p class="mb-0">Tones of SASS variables</p></div>
                                </div>
                            </li>
                            <li class="py-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="badge badge-circle badge-primary mr-3">
                                            <span class="ti-check"></span>
                                        </div>
                                    </div>
                                    <div><p class="mb-0">Create your own skin to match your brand</p></div>
                                </div>
                            </li>
                            <li class="py-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="badge badge-circle badge-primary mr-3">
                                            <span class="ti-check"></span>
                                        </div>
                                    </div>
                                    <div><p class="mb-0">Globally orchestrate tactical channels whereas bricks</p></div>
                                </div>
                            </li>
                            <li class="py-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="badge badge-circle badge-primary mr-3">
                                            <span class="ti-check"></span>
                                        </div>
                                    </div>
                                    <div><p class="mb-0">Use Gulp to prepare all assets for production</p></div>
                                </div>
                            </li>
                            <li class="py-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="badge badge-circle badge-primary mr-3">
                                            <span class="ti-check"></span>
                                        </div>
                                    </div>
                                    <div><p class="mb-0">Collaboratively predominate vertical manufactured</p></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </section>
        <!--about us section end-->

        <!--counter section start -->
        <section id="features" class="imageblock-section switchable switchable-content">
            <div class="imageblock-section-img col-lg-5 col-md-4">
                <div class="background-image-holder" style="background: url('img/about-img.jpg')no-repeat center center / cover; opacity: 1;">  </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-7">
                        <!--feature new style start-->
                        <div class="row align-items-center ptb-100">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-start mb-5">
                                    <div class="pr-4">
                                        <div class="icon icon-shape icon-color-1 rounded-circle">
                                            <span class="ti-face-smile"></span>
                                        </div>
                                    </div>
                                    <div class="icon-text">
                                        <h5>Responsive web design</h5>
                                        <p class="mb-0">Modular and interchangable componente between layouts and even demos.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-5">
                                    <div class="pr-4">
                                        <div class="icon icon-shape icon-color-2 rounded-circle">
                                            <span class="ti-vector"></span>
                                        </div>
                                    </div>
                                    <div class="icon-text">
                                        <h5>Loaded with features</h5>
                                        <p class="mb-0">Modular and interchangable componente between layouts and even demos.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="pr-4">
                                        <div class="icon icon-shape icon-color-3 rounded-circle">
                                            <span class="ti-headphone-alt"></span>
                                        </div>
                                    </div>
                                    <div class="icon-text">
                                        <h5>Friendly online support</h5>
                                        <p class="mb-0">Modular and interchangable componente between layouts and even demos.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--feature new style end-->
                    </div>
                </div>
                <!--end of row-->
            </div>
        </section>
        <!--counter section end-->

        <!--our pricing packages section start-->
        <section id="pricing" class="package-section ptb-100 gray-light-bg">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="section-heading text-center mb-5">
                            <h2>Afforadble Pricing and Packages <br><span>choose your best one</span></h2>
                            <p class="lead">
                                Monotonectally grow strategic process improvements vis-a-vis integrated resources.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md">
                        <div class="card text-center single-pricing-pack">
                            <div class="pt-4"><h5>Basic</h5></div>
                            <div class="pricing-img mt-4">
                                <img src="img/basic.svg" alt="pricing img" class="img-fluid">
                            </div>
                            <div class="card-header py-4 border-0 pricing-header">
                                <div class="h1 text-center mb-0">$<span class="price font-weight-bolder">29</span></div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled text-sm mb-4 pricing-feature-list">
                                    <li>Push Notifications</li>
                                    <li>Data Transfer</li>
                                    <li>SQL Database</li>
                                    <li>Search & SEO Analytics</li>
                                    <li>24/7 Phone Support</li>
                                    <li>2 months technical support</li>
                                    <li>2+ profitable keyword</li>
                                </ul>
                                <a href="#" class="btn outline-btn mb-3" target="_blank">Purchase now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md">
                        <div class="card popular-price text-center single-pricing-pack">
                            <div class="pt-4"><h5>Standard</h5></div>
                            <div class="pricing-img mt-4">
                                <img src="img/standard.svg" alt="pricing img" class="img-fluid">
                            </div>
                            <div class="card-header py-4 border-0 pricing-header">
                                <div class="h1 text-center mb-0">$<span
                                        class="price font-weight-bolder">149</span></div>

                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled text-sm mb-4 pricing-feature-list">
                                    <li>Push Notifications</li>
                                    <li>Data Transfer</li>
                                    <li>SQL Database</li>
                                    <li>Search & SEO Analytics</li>
                                    <li>24/7 Phone Support</li>
                                    <li>1 Year technical support</li>
                                    <li>50+ profitable keyword</li>
                                </ul>
                                <a href="#" class="btn solid-btn mb-3" target="_blank">Purchase now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md">
                        <div class="card text-center single-pricing-pack">
                            <div class="pt-4"><h5>Unlimited</h5></div>
                            <div class="pricing-img mt-4">
                                <img src="img/unlimited.svg" alt="pricing img" class="img-fluid">
                            </div>
                            <div class="card-header py-4 border-0 pricing-header">
                                <div class="h1 text-center mb-0">$<span class="price font-weight-bolder">39</span></div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled text-sm mb-4 pricing-feature-list">
                                    <li>Push Notifications</li>
                                    <li>Data Transfer</li>
                                    <li>SQL Database</li>
                                    <li>Search & SEO Analytics</li>
                                    <li>24/7 Phone Support</li>
                                    <li>6 months technical support</li>
                                    <li>10+ profitable keyword</li>
                                </ul>
                                <a href="#" class="btn outline-btn mb-3" target="_blank">Purchase now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 text-center">
                    <p class="mb-2">If you need custom services or Need more? <a href="#" class="color-secondary">
                        Contact us
                    </a></p>
                </div>
            </div>
        </section>
        <!--our pricing packages section end-->

        <!--testimonial section start-->
        <section class="testimonial-section ptb-100">
            <div class="container">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-6">
                        <div class="section-heading mb-5">
                            <h2>Testimonials <br><span>what clients say</span></h2>
                            <p class="lead">
                                Rapidiously morph transparent internal or "organic" sources whereas resource sucking
                                e-business. Conveniently innovate compelling internal.
                            </p>

                            <div class="client-section-wrap d-flex flex-row align-items-center">
                                <ul class="list-inline">
                                    <li class="list-inline-item"><img src="img/client-1-color.png" width="85" alt="client"
                                                                    class="img-fluid"></li>
                                    <li class="list-inline-item"><img src="img/client-6-color.png" width="85" alt="client"
                                                                    class="img-fluid"></li>
                                    <li class="list-inline-item"><img src="img/client-3-color.png" width="85" alt="client"
                                                                    class="img-fluid"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="owl-carousel owl-theme client-testimonial arrow-indicator">
                            <div class="item">
                                <div class="testimonial-quote-wrap">
                                    <div class="media author-info mb-3">
                                        <div class="author-img mr-3">
                                            <img src="img/client-1.jpg" alt="client" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="mb-0">John Charles</h5>
                                            <span>Google</span>
                                        </div>
                                    </div>
                                    <div class="client-say">
                                        <p> <img src="img/quote.png" alt="quote" class="img-fluid"> Interactively optimize fully researched expertise vis-a-vis plug-and-play relationships. Intrinsicly develop viral core competencies for fully tested customer service. Enthusiastically create next-generation growth strategies and.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="testimonial-quote-wrap">
                                    <div class="media author-info mb-3">
                                        <div class="author-img mr-3">
                                            <img src="img/client-2.jpg" alt="client" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="mb-0">Arabella Ora</h5>
                                            <span>Amazon</span>
                                        </div>
                                    </div>
                                    <div class="client-say">
                                        <p><img src="img/quote.png" alt="quote" class="img-fluid">  Rapidiously develop user friendly growth strategies after extensive initiatives. Conveniently grow innovative benefits through fully tested deliverables. Rapidiously utilize focused platforms through end-to-end schemas.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="testimonial-quote-wrap">
                                    <div class="media author-info mb-3">
                                        <div class="author-img mr-3">
                                            <img src="img/client-1.jpg" alt="client" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="mb-0">Jeremy Jere</h5>
                                            <span>Themetags</span>
                                        </div>
                                    </div>
                                    <div class="client-say">
                                        <p><img src="img/quote.png" alt="quote" class="img-fluid"> Objectively synthesize client-centered e-tailers for maintainable channels. Holisticly administrate customer directed vortals whereas tactical functionalities. Energistically monetize reliable imperatives through client-centric best practices. Collaboratively.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="testimonial-quote-wrap">
                                    <div class="media author-info mb-3">
                                        <div class="author-img mr-3">
                                            <img src="img/client-1.jpg" alt="client" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="mb-0">John Charles</h5>
                                            <span>Google</span>
                                        </div>
                                    </div>
                                    <div class="client-say">
                                        <p><img src="img/quote.png" alt="quote" class="img-fluid"> Enthusiastically innovate B2C data without clicks-and-mortar convergence. Monotonectally deliver compelling testing procedures vis-a-vis B2B testing procedures. Competently evisculate integrated resources whereas global processes. Enthusiastically.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--testimonial section end-->

        <!--screenshots section start-->
        <section id="screenshots" class="screenshots-section ptb-100 gray-light-bg">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="section-heading text-center">
                            <h2>App screenshots <br> <span>Looks awesome</span></h2>
                            <p class="lead">Credibly synthesize multimedia based networks vis-a-vis top-line growth strategies. Continually leverage existing worldwide interfaces </p>
                        </div>
                    </div>
                </div>
                <!--start app screen carousel-->
                <div class="screen-slider-content mt-5">
                    <div class="screenshot-frame"></div>
                    <div class="screen-carousel owl-carousel owl-theme dot-indicator">
                        <img src="img/01.png" class="img-fluid" alt="screenshots">
                        <img src="img/02.png" class="img-fluid" alt="screenshots">
                        <img src="img/03.png" class="img-fluid" alt="screenshots">
                        <img src="img/04.png" class="img-fluid" alt="screenshots">
                        <img src="img/05.png" class="img-fluid" alt="screenshots">
                        <img src="img/06.png" class="img-fluid" alt="screenshots">
                    </div>
                </div>
                <!--end app screen carousel-->

            </div>
        </section>
        <!--screenshots section end-->

        <!--download section start-->
        <section class="download-section pt-100 background-img"
                style="background: url('img/app-hero-bg.jpg')no-repeat center center / cover">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-7">
                        <div class="download-content text-white pb-100">
                            <h2 class="text-white">Start Managing your apps
                                business, more faster</h2>
                            <p class="lead">Objectively deliver professional value with diverse web-readiness.
                                Collaboratively transition wireless customer service without goal-oriented catalysts for
                                change. Collaboratively.</p>

                            <div class="download-btn">
                                <a href="#" class="btn google-play-btn mr-3"><span class="ti-android"></span> Google
                                    Play</a>
                                <a href="#" class="btn app-store-btn"><span class="ti-apple"></span> App Store</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="download-img d-flex align-items-end">
                            <img src="img/app-hand-top.png" alt="download" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--download section end-->

        <!--our blog section start-->
        <section id="blog" class="our-blog-section ptb-100 gray-light-bg">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="section-heading mb-5">
                            <h2>Our Latest News</h2>
                            <p>
                                Enthusiastically drive revolutionary opportunities before emerging leadership. Distinctively
                                transform tactical methods of empowerment via resource sucking core.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="single-blog-card card border-0 shadow-sm">
                            <span class="category position-absolute badge badge-pill badge-primary">Lifestyle</span>
                            <img src="img/blog/1.jpg" class="card-img-top position-relative" alt="blog">
                            <div class="card-body">
                                <div class="post-meta mb-2">
                                    <ul class="list-inline meta-list">
                                        <li class="list-inline-item">Jan 21, 2019</li>
                                        <li class="list-inline-item"><span>45</span> Comments</li>
                                        <li class="list-inline-item"><span>10</span> Share</li>
                                    </ul>
                                </div>
                                <h3 class="h5 card-title"><a href="#">Appropriately productize fully</a></h3>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk.</p>
                                <a href="#" class="detail-link">Read more <span class="ti-arrow-right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="single-blog-card card border-0 shadow-sm">
                            <span class="category position-absolute badge badge-pill badge-danger">Technology</span>
                            <img src="img/blog/2.jpg" class="card-img-top position-relative" alt="blog">
                            <div class="card-body">
                                <div class="post-meta mb-2">
                                    <ul class="list-inline meta-list">
                                        <li class="list-inline-item">May 26, 2019</li>
                                        <li class="list-inline-item"><span>30</span> Comments</li>
                                        <li class="list-inline-item"><span>5</span> Share</li>
                                    </ul>
                                </div>
                                <h3 class="h5 card-title"><a href="#">Quickly formulate backend</a></h3>
                                <p class="card-text">Synergistically engage effective ROI after customer directed partnerships.</p>
                                <a href="#" class="detail-link">Read more <span class="ti-arrow-right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="single-blog-card card border-0 shadow-sm">
                            <span class="category position-absolute badge badge-pill badge-info">Science</span>
                            <img src="img/blog/3.jpg" class="card-img-top" alt="blog">
                            <div class="card-body">
                                <div class="post-meta mb-2">
                                    <ul class="list-inline meta-list">
                                        <li class="list-inline-item">Apr 25, 2019</li>
                                        <li class="list-inline-item"><span>41</span> Comments</li>
                                        <li class="list-inline-item"><span>30</span> Share</li>
                                    </ul>
                                </div>
                                <h3 class="h5 card-title"><a href="#">Objectively extend extensive</a></h3>
                                <p class="card-text">Holisticly mesh open-source leadership rather than proactive users. </p>
                                <a href="#" class="detail-link">Read more <span class="ti-arrow-right"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--our blog section end-->

        <!--our team section start-->
        <section id="team" class="team-member-section ptb-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-8">
                        <div class="section-heading text-center mb-5">
                            <h2>Our team members</h2>
                            <p class="lead">
                                Following reasons show advantages of adding AppCo to your lead pages, demos and checkouts
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="single-team-member position-relative">
                            <div class="team-image">
                                <img src="img/team-4.jpg" alt="Team Members" class="img-fluid rounded-circle">
                            </div>
                            <div class="team-info text-white rounded-circle d-flex flex-column align-items-center justify-content-center">
                                <h5 class="mb-0">Edna Mason</h5>
                                <h6>Senior Designer</h6>
                                <ul class="list-inline team-social social-icon mt-4 text-white">
                                    <li class="list-inline-item"><a href="#"><span class="ti-facebook"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-twitter"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-github"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-dribbble"></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="single-team-member position-relative">
                            <div class="team-image">
                                <img src="img/team-1.jpg" alt="Team Members" class="img-fluid rounded-circle">
                            </div>
                            <div class="team-info text-white rounded-circle d-flex flex-column align-items-center justify-content-center">
                                <h5 class="mb-0">Edna Mason</h5>
                                <h6>Senior Designer</h6>
                                <ul class="list-inline team-social social-icon mt-4 text-white">
                                    <li class="list-inline-item"><a href="#"><span class="ti-facebook"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-twitter"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-github"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-dribbble"></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="single-team-member position-relative">
                            <div class="team-image">
                                <img src="img/team-2.jpg" alt="Team Members" class="img-fluid rounded-circle">
                            </div>
                            <div class="team-info text-white rounded-circle d-flex flex-column align-items-center justify-content-center">
                                <h5 class="mb-0">Edna Mason</h5>
                                <h6>Senior Designer</h6>
                                <ul class="list-inline team-social social-icon mt-4 text-white">
                                    <li class="list-inline-item"><a href="#"><span class="ti-facebook"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-twitter"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-github"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-dribbble"></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="single-team-member position-relative">
                            <div class="team-image">
                                <img src="img/team-3.jpg" alt="Team Members" class="img-fluid rounded-circle">
                            </div>
                            <div class="team-info text-white rounded-circle d-flex flex-column align-items-center justify-content-center">
                                <h5 class="mb-0">Edna Mason</h5>
                                <h6>Senior Designer</h6>
                                <ul class="list-inline team-social social-icon mt-4 text-white">
                                    <li class="list-inline-item"><a href="#"><span class="ti-facebook"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-twitter"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-github"></span></a></li>
                                    <li class="list-inline-item"><a href="#"><span class="ti-dribbble"></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--our team section end-->

        <!--contact us section start-->
        <section id="contact" class="contact-us gray-light-bg ptb-100">
            <div class="container">
                <div class="row">
                    <div class="col-12 pb-3 message-box d-none">
                        <div class="alert alert-danger"></div>
                    </div>
                    <div class="col-md-5">
                        <div class="section-heading">
                            <h3>Contact with us</h3>
                            <p>It's very easy to get in touch with us. Just use the contact form or pay us a
                                visit for a coffee at the office. Dynamically innovate competitive technology after an
                                expanded array of leadership.</p>
                        </div>
                        <div class="footer-address">
                            <h6><strong>Head Office</strong></h6>
                            <p>121 King St, Melbourne VIC 3000, Australia</p>
                            <ul>
                                <li><span>Phone: +61 2 8376 6284</span></li>
                                <li><span>Email : <a href="mailto:hello@yourdomain.com">hello@yourdomain.com</a></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <form action="#" method="POST" id="contactForm" class="contact-us-form" novalidate="novalidate">
                            <h5>Reach us quickly</h5>
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter name"
                                                required="required">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter email"
                                                required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <input type="text" name="phone" value="" class="form-control" id="phone"
                                                placeholder="Your Phone">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <input type="text" name="company" value="" size="40" class="form-control"
                                                id="company" placeholder="Your Company">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea name="message" id="message" class="form-control" rows="7" cols="25"
                                                    placeholder="Message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 mt-3">
                                    <button type="submit" class="btn solid-btn" id="btnContactUs">
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                        <p class="form-message"></p>
                    </div>
                </div>
            </div>
        </section>
        <!--contact us section end-->

        <!--client section start-->
        <section class="client-section ptb-100">
            <div class="container">
                <!--clients logo start-->
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="section-heading text-center mb-5">
                            <h2>Trusted by companies</h2>
                            <p class="lead">
                                Rapidiously morph transparent internal or "organic" sources whereas resource sucking
                                e-business. Conveniently innovate compelling internal.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="owl-carousel owl-theme clients-carousel dot-indicator">
                            <div class="item single-client">
                                <img src="img/client-5-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-1-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-6-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-3-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-4-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-5-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-7-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-2-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-1-color.png" alt="client logo" class="client-img">
                            </div>
                            <div class="item single-client">
                                <img src="img/client-3-color.png" alt="client logo" class="client-img">
                            </div>
                        </div>
                    </div>
                </div>
                <!--clients logo end-->
            </div>
        </section>
        <!--client section start-->

    </div>
    <!--body content wrap end-->

    <!--shape image start-->
    <div class="shape-img subscribe-wrap">
        <img src="img/footer-top-shape.png" alt="footer shape" class="img-fluid">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form action="#" method="post" class="subscribe-form subscribe-form-footer d-none d-md-block d-lg-block">
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control input" id="email-footer" name="email"
                                placeholder="info@yourdomain.com">
                            <input type="submit" class="button btn solid-btn" id="submit-footer" value="Subscribe">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--shape image end-->

@endsection
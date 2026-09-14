@extends('frontend::layouts.master')

@section('content')

@include('frontend::includes.breadcrumb', [
    'title' => '',
    'items' => []
])

<section class="blog blog-section details">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12 blog-pots">
                {{-- <div class="row">
                    <section id="additional_details" class="headings-2 pt-0 pb-2 px-3 col-12">
                        <div class="pro-wrapper">
                            <div class="detail-wrapper-body">
                                <div class="listing-title-bar">
                                    <h3>{!! $model->translateOrFirst()->title !!}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="mt-0">
                            <p>{!! $model->translateOrFirst()->brief !!}</p>
                        </div>
                    </section>
                </div> --}}
                <div class="blog-info details mb-30 dynamic-description">
                    <h4 class="breadcrumb_title text-dark mb10">{{__('frontend::strings.this_page_is_not_available_desc')}}</h4>
                    @foreach ($trasnlatedContentLocales as $trasnlatedContentLocale)
                        <span>{{ $supportedLangs[$trasnlatedContentLocale]['native'] }} : </span>
                        <a hreflang="{{ $trasnlatedContentLocale }}" class="font-weight-bold" href="{{ LaravelLocalization::getLocalizedURL($trasnlatedContentLocale, request()->fullUrl(), [], true) }}"  class="dropdown-item">
                            {{ LaravelLocalization::getLocalizedURL($trasnlatedContentLocale, request()->fullUrl(), [], true) }}
                        </a>
                        <br>
                    @endforeach
                </div>
                <section class="single reviews leve-comments details">
                    <div id="add-review" class="add-review-box">
                        <h3 class="listing-desc-headline margin-bottom-20 mb-4">{{__('frontend::main.contact_form.title')}}</h3>
                        <span class="leave-rating-title">{{__('frontend::main.contact_form.sub_title')}}</span>
                        @include('frontend::includes.contact-form')
                    </div>
                </section>
            </div>
            <aside class="col-lg-4 col-md-12 car mt-lg-0 mt-md-4">
                @include('frontend::includes.sidebar')
            </aside>
        </div>


    </div>
</section>
@endsection

@push('scripts')
<script src="{{ mix('js/frontend.min.js') }}"></script>
@endpush

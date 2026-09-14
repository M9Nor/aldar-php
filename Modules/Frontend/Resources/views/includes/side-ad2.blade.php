<div class="widget-boxed-header mb-1">
    <h4>{{$secondBanners[app()->getLocale()]->translateOrFirst()->title}}</h4>
</div>
<div class="widget-boxed-body">
    <div class="banner">
        @php
            $default    = route('image', ['size' => '1000x1000', 'path' => 'defaults/attachments.png']);
        @endphp
        <a class="none-a" href="{{$secondBanners[app()->getLocale()]->link}}">
            <img src="{{$default}}" data-src="{{ $secondBanners[app()->getLocale()]->getTranslatedImage('1000x1000')}}" class="lazy w-100" alt="{{$secondBanners[app()->getLocale()]->translateOrFirst()->title}}">
        </a>
    </div>
</div>
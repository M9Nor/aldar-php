@if(isset($options))
    @php
        $opt = array_merge([
            'title'       => null,
            'sub_title'   => null,
            'description' => null,
            'keywords'    => null,
            'image'       => null,
            'type'        => 'website',
            'url'         => null,
        ], $options);
    @endphp
    @section('htmlTag') @parent @endsection
    @section('pageMeta') 
        @parent
        <meta property=og:title content="{{ (!empty($opt['title'])) ? $opt['title'] . (empty($opt['sub_title']) ? '' : ' | ') : '' }}{{ $opt['sub_title'] }}" />
        {{-- <meta property=og:title content="{!! $opt['sub_title'] !!}" /> --}}
        <meta property=og:type content="website" />
        <meta property=og:url content="{{URL::current()}}" />
        <meta property=og:site name content="{{__("frontend::app.site_name")}}" />

        @if(!is_null($opt['image']))
            <meta property=og:image content="{!! $opt['image'] !!}" />
        @endif
        @if(!is_null($opt['description']))
            <meta property=og:description content="{!! $opt['description'] !!}" />
        @endif
        @if(!is_null($opt['keywords']))
            <meta property=og:keywords content="{!! $opt['keywords'] !!}" />
        @endif
        <meta property=og:locale content="{{app()->getLocale()}}" />

        <meta property=og:image:width content="450"/>
        <meta property=og:image:height content="298"/>
    @endsection
@endif


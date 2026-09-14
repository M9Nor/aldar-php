@if(isset($options))
    @php
        $opt = array_merge([
            'title'         => null,
            'sub_title'     => null,
            'description'   => null,
            'image'         => null,
            'keywords'      => null,
            'prev'          => null,
            'next'          => null,
        ], $options);
    @endphp
    @section('htmlTag') @parent @endsection
    @section('pageMeta') 
        @if(!empty($opt['title']))
            <title>
                {{ (!empty($opt['title'])) ? $opt['title'] . (empty($opt['sub_title']) ? '' : ' | ') : '' }}{{ $opt['sub_title'] }} 
            </title>
        @else
            <title>
                {{ $opt['sub_title'] }} 
            </title>
        @endif
        {{-- <title>{!! $opt['sub_title'] !!} </title> --}}
        <meta name="description" content="{{ $opt['description'] }}">
        <meta name="og:image" content="{{ $opt['image'] }}"/>
        <meta name="keywords" content="{{ $opt['keywords'] }}">
        {{-- {{dd(URL::current())}} --}}
        @if(Route::currentRouteName() == 'FrontEndController@index')
            <link rel="canonical" href="{{Request::root()}}">
        @else
            <link rel="canonical" href="{{URL::current()}}">
        @endif
        
        <link rel="alternate" hreflang="{{app()->getLocale()}}"  href="{{URL::current()}}" />

        @if(!empty($opt['prev']))
            <link rel="prev" href="{{ $opt['prev'] }}" />
        @endif
        @if(!empty($opt['next']))
            <link rel="next" href="{{ $opt['next'] }}" />
        @endif
        

        @parent
    @endsection
@endif
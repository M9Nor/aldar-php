@if(isset($options))
    @php
        $opt = array_merge([
            'title'       => null,
            'sub_title'   => null,
            'description' => null,
            'image'       => null,
        ], $options);
    @endphp
    {{-- {{dd($opt['description'])}} --}}
    @section('htmlTag') @parent itemscope itemtype="http://schema.org/Article" @endsection
    @section('pageMeta') 
        @parent
        <meta itemprop="name" content="{{ (!empty($opt['title'])) ? $opt['title'] . (empty($opt['sub_title']) ? '' : ' | ') : '' }}{{ $opt['sub_title'] }}">
        {{-- <meta itemprop="name" content="{!! $opt['sub_title'] !!}"> --}}
        @if(!is_null($opt['description']))
            <meta itemprop="description" content="{{ $opt['description'] }}">
        @endif
        @if(!is_null($opt['image']))
            <meta itemprop="image" content="{!! $opt['image'] !!}">
        @endif
    @endsection
@endif


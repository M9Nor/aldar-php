@if(isset($options))
    @php
        $opt = array_merge([
            'title'       => null,
            'sub_title'   => null,
            'description' => null,
            'image'       => null,
            'type'        => 'summary_large_image',
            'url'         => null,
        ], $options);
    @endphp
    @section('htmlTag') @parent @endsection
    @section('pageMeta') 
        @parent
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ (!empty($opt['title'])) ? $opt['title'] . (empty($opt['sub_title']) ? '' : ' | ') : '' }}{{ $opt['sub_title'] }}">
        {{-- <meta name="twitter:title" content="{!! $opt['sub_title'] !!}"> --}}
        @if(!is_null($opt['description']))
            <meta name="twitter:description" content="{!! Str::limit($opt['description'], 500, '...') !!}">
        @endif
        {{-- <-- Twitter Summary card images must be at least 120x120px --> --}}
        @if(!is_null($opt['image']))
            <meta name="twitter:image" content="{!! $opt['image'] !!}">
        @endif
        <meta name="twitter:site" content="@namaa_solutions">
    @endsection
@endif
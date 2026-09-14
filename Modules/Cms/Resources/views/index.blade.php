@extends('cms::layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
            <h1>Hello World</h1>

            <p>
                This view is loaded from module: {!! config('cms.name') !!}
            </p>
        </div>
    </div>
@endsection

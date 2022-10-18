@extends('QiNiu::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from plugin: {!! config('qiniu.name') !!}
    </p>
@endsection

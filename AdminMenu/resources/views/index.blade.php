@extends('AdminMenu::layouts.master')

@section('content')
    @component("AdminMenu::components.{$type}", compact('detail', 'roles', 'fsLang', 'fsName'))@endcomponent
@endsection

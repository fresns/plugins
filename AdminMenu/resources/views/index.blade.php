@extends('AdminMenu::layouts.master')

@section('content')
    @switch($type)
        @case('user')
            @component('AdminMenu::components.user', compact('data', 'roles', 'authUuid', 'langTag', 'fsLang', 'fsName'))@endcomponent
        @break

        @case('post')
            @component('AdminMenu::components.post', compact('data', 'groupCategories', 'authUuid', 'langTag', 'fsLang', 'fsName'))@endcomponent
        @break

        @case('comment')
            @component('AdminMenu::components.comment', compact('data', 'authUuid', 'langTag', 'fsLang', 'fsName'))@endcomponent
        @break

        @default
            Default case...
    @endswitch
@endsection

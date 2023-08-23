@extends('EditorWorkspace::commons.fresns')

@section('content')
    <div class="ps-3 pb-3">
        <a class="btn btn-outline-secondary" href="{{ route('editor-workspace.admin.index') }}" role="button"><i class="bi bi-arrow-left"></i></a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">UID</th>
                    <th scope="col">Username</th>
                    <th scope="col">Nickname</th>
                    <th scope="col">Role</th>
                    <th scope="col">Posts</th>
                    <th scope="col">Comments</th>
                    <th scope="col">{{ __('FsLang::panel.table_status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <th scope="row">{{ $user->id }}</th>
                        <td>
                            @if ($identifier == 'uid')
                                <a href="{{ $url.$user->uid }}" target="_blank">{{ $user->uid }}</a>
                            @else
                                {{ $user->uid }}
                            @endif
                        </td>
                        <td>
                            @if ($identifier == 'username')
                                <a href="{{ $url.$user->username }}" target="_blank">{{ $user->username }}</a>
                            @else
                                {{ $user->username }}
                            @endif
                        </td>
                        <td>
                            {{ $user->nickname }}
                            {!! $user->verified_status ? '<i class="bi bi-patch-check-fill text-primary"></i>' : '' !!}
                        </td>
                        <td>
                            @if ($user?->main_role)
                                {{ $user?->main_role?->getLangName($defaultLanguage) }}
                            @else
                                Null
                            @endif
                        </td>
                        <td>{{ $user->stat->post_publish_count }}</td>
                        <td>{{ $user->stat->comment_publish_count }}</td>
                        <td>{!! $user->is_enabled ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

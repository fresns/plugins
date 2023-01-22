@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">PID</th>
                    <th scope="col">{{ __('EasyManager::fresns.group') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_summary') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.hashtag') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.file') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_is_allow') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_author') }}</th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_likes') }}
                        @if (request('orderBy') == 'like_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'like_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'like_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'like_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'like_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_dislikes') }}
                        @if (request('orderBy') == 'dislike_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'dislike_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'dislike_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'dislike_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'dislike_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_followers') }}
                        @if (request('orderBy') == 'follow_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'follow_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'follow_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'follow_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'follow_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_blockers') }}
                        @if (request('orderBy') == 'block_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'block_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'block_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'block_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'block_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_comments') }}
                        @if (request('orderBy') == 'comment_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'comment_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_publish_time') }}
                        @if (request('orderBy') == 'created_at' && request('orderDirection') == 'desc' || empty(request('orderBy')))
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'created_at', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'created_at' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'created_at', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'created_at', 'orderDirection' => 'asc']) }}" class="link-secondary"><i class="bi bi-caret-up"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_last_edit_time') }}
                        @if (request('orderBy') == 'latest_edit_at' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'latest_edit_at', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'latest_edit_at' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'latest_edit_at', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'latest_edit_at', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_latest_comment_time') }}
                        @if (request('orderBy') == 'latest_comment_at' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'latest_comment_at', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'latest_comment_at' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'latest_comment_at', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'latest_comment_at', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">{{ __('EasyManager::fresns.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <th scope="row">{{ $post->id }}</th>
                        <td><a href="{{ $url.$post->pid }}" target="_blank">{{ $post->pid }}</a></td>
                        <td><a href="{{ route('easy-manager.group.index', ['id' => $post?->group?->id]) }}">{{ $post?->group?->getLangName($defaultLanguage) }}</a></td>
                        <td>{{ $post->title ?? Str::limit(strip_tags($post->content), 30) }}</td>
                        <td>
                            @if (count($post->hashtags) > 0)
                                <a href="{{ route('easy-manager.hashtag.index', ['ids' => json_encode(collect($post->hashtags)->pluck('file_id'))]) }}">
                                    <span class="badge rounded-pill text-bg-light">{{ count($post->hashtags) }}</span>
                                </a>
                            @endif
                        </td>
                        <td>
                            @if (count($post->fileUsages) > 0)
                                <a href="{{ route('easy-manager.file.index', ['ids' => json_encode(collect($post->fileUsages)->pluck('file_id'))]) }}">
                                    <span class="badge rounded-pill text-bg-light">{{ count($post->fileUsages) }}</span>
                                </a>
                            @endif
                        </td>
                        <td>
                            @if ($post->postAppend->is_allow)
                                {{ __('EasyManager::fresns.option_no') }}
                            @else
                                {{ __('EasyManager::fresns.option_yes') }}
                            @endif
                        </td>
                        <td>
                            @if ($post->is_anonymous)
                                <a href="{{ route('easy-manager.user.index', ['uid' => $post->creator->uid]) }}" class="link-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('EasyManager::fresns.table_anonymous') }}">
                                    <i class="bi bi-person-lock"></i> {{ $post->creator->nickname }}
                                </a>
                            @else
                                <a href="{{ route('easy-manager.user.index', ['uid' => $post->creator->uid]) }}">{{ $post->creator->nickname }}</a>
                            @endif
                        </td>
                        <td>{{ $post->like_count }}</td>
                        <td>{{ $post->dislike_count }}</td>
                        <td>{{ $post->follow_count }}</td>
                        <td>{{ $post->block_count }}</td>
                        <td>
                            @if ($post->comment_count > 0)
                                <a href="{{ route('easy-manager.comment.index', ['postId' => $post->id]) }}">{{ $post->comment_count }}</a>
                            @else
                                {{ $post->comment_count }}
                            @endif
                        </td>
                        <td>{{ $post->created_at }}</td>
                        <td>{{ $post->latest_edit_at }}</td>
                        <td>{{ $post->latest_comment_at }}</td>
                        <td>
                            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="{{ __('EasyManager::fresns.under_development') }}">
                                <button class="btn btn-outline-primary btn-sm me-2" type="button" disabled>{{ __('EasyManager::fresns.button_edit') }}</button>
                                <button class="btn btn-outline-danger btn-sm" type="button" disabled>{{ __('EasyManager::fresns.button_delete') }}</button>
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="py-3 d-flex justify-content-center">
        {{ $posts->appends(request()->all())->links() }}
    </div>
@endsection

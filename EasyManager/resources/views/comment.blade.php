@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">CID</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_post_pid') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_parent_cid') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_summary') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.geotag') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.hashtag') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.file') }}</th>
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
                        {{ __('EasyManager::fresns.table_sub_comments') }}
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
                @foreach ($comments as $comment)
                    <tr>
                        <th scope="row">{{ $comment->id }}</th>
                        <td><a href="{{ $url.$comment->cid }}" target="_blank">{{ $comment->cid }}</a></td>
                        <td><a href="{{ route('easy-manager.post.index', ['pid' => $comment->post?->pid]) }}">{{ $comment->post?->pid }}</a></td>
                        <td><a href="{{ route('easy-manager.comment.index', ['cid' => $comment->parentComment?->cid]) }}">{{ $comment->parentComment?->cid }}</a></td>
                        <td>{{ Str::limit(strip_tags($comment->content), 30) }}</td>
                        <td><a href="{{ route('easy-manager.geotag.index', ['id' => $comment->geotag_id]) }}">{{ $comment->geotag?->getLangContent('name', $defaultLanguage) }}</a></td>
                        <td>
                            @if (count($comment->hashtags) > 0)
                                <a href="{{ route('easy-manager.hashtag.index', ['ids' => json_encode(collect($comment->hashtags)->pluck('file_id'))]) }}">
                                    <span class="badge rounded-pill text-bg-light">{{ count($comment->hashtags) }}</span>
                                </a>
                            @endif
                        </td>
                        <td>
                            @if (count($comment->fileUsages) > 0)
                                <a href="{{ route('easy-manager.file.index', ['ids' => json_encode(collect($comment->fileUsages)->pluck('file_id'))]) }}">
                                    <span class="badge rounded-pill text-bg-light">{{ count($comment->fileUsages) }}</span>
                                </a>
                            @endif
                        </td>
                        <td>
                            @if ($comment->is_anonymous)
                                <a href="{{ route('easy-manager.user.index', ['uid' => $comment->author->uid]) }}" class="link-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('EasyManager::fresns.table_anonymous') }}">
                                    <i class="bi bi-person-lock"></i> {{ $comment->author->nickname }}
                                </a>
                            @else
                                <a href="{{ route('easy-manager.user.index', ['uid' => $comment->author->uid]) }}">{{ $comment->author->nickname }}</a>
                            @endif
                        </td>
                        <td>{{ $comment->like_count }}</td>
                        <td>{{ $comment->dislike_count }}</td>
                        <td>{{ $comment->follow_count }}</td>
                        <td>{{ $comment->block_count }}</td>
                        <td>
                            @if ($comment->comment_count > 0)
                                <a href="{{ route('easy-manager.comment.index', ['parentId' => $comment->id]) }}">{{ $comment->comment_count }}</a>
                            @else
                                {{ $comment->comment_count }}
                            @endif
                        </td>
                        <td>{{ $comment->created_at }}</td>
                        <td>{{ $comment->latest_edit_at }}</td>
                        <td>{{ $comment->latest_comment_at }}</td>
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
        {{ $comments->appends(request()->all())->links() }}
    </div>
@endsection

@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">HID</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_name') }}</th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_posts') }}
                        @if (request('orderBy') == 'post_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'post_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_digest_posts') }}
                        @if (request('orderBy') == 'post_digest_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_digest_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'post_digest_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_digest_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_digest_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
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
                        {{ __('EasyManager::fresns.table_digest_comments') }}
                        @if (request('orderBy') == 'comment_digest_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_digest_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'comment_digest_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_digest_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_digest_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_create_time') }}
                        @if (request('orderBy') == 'id' && request('orderDirection') == 'desc' || empty(request('orderBy')))
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'id', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'id' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'id', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'id', 'orderDirection' => 'asc']) }}" class="link-secondary"><i class="bi bi-caret-up"></i></a>
                        @endif
                    </th>
                    <th scope="col">{{ __('EasyManager::fresns.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hashtags as $hashtag)
                    <tr>
                        <th scope="row">{{ $hashtag->id }}</th>
                        <td><a href="{{ $url.$hashtag->slug }}" target="_blank">{{ $hashtag->slug }}</a></td>
                        <td>{{ $hashtag->name }}</td>
                        <td><a href="{{ route('easy-manager.post.index', ['hashtagId' => $hashtag->id]) }}">{{ $hashtag->post_count }}</a></td>
                        <td>{{ $hashtag->post_digest_count }}</td>
                        <td><a href="{{ route('easy-manager.comment.index', ['hashtagId' => $hashtag->id]) }}">{{ $hashtag->comment_count }}</a></td>
                        <td>{{ $hashtag->comment_digest_count }}</td>
                        <td>{{ $hashtag->created_at }}</td>
                        <td>
                            <form action="{{ route('easy-manager.hashtag.update', $hashtag) }}" method="post">
                                @csrf
                                @method('put')
                                @if ($hashtag->is_enable)
                                    <input type="hidden" name="is_enable" value="0"/>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">{{ __('EasyManager::fresns.button_deactivate') }}</button>
                                @else
                                    <input type="hidden" name="is_enable" value="1"/>
                                    <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('EasyManager::fresns.button_activate') }}</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="py-3 d-flex justify-content-center">
        {{ $hashtags->appends(request()->all())->links() }}
    </div>
@endsection

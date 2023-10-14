@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">HID</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_name') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_type') }}</th>
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
                        <td><a href="{{ route('easy-manager.hashtag.index', ['type' => $hashtag->type]) }}">{{ $hashtag->type }}</a></td>
                        <td><a href="{{ route('easy-manager.post.index', ['hashtagId' => $hashtag->id]) }}">{{ $hashtag->post_count }}</a></td>
                        <td>{{ $hashtag->post_digest_count }}</td>
                        <td><a href="{{ route('easy-manager.comment.index', ['hashtagId' => $hashtag->id]) }}">{{ $hashtag->comment_count }}</a></td>
                        <td>{{ $hashtag->comment_digest_count }}</td>
                        <td>{{ $hashtag->created_at }}</td>
                        <td>
                            <form action="{{ route('easy-manager.hashtag.update', $hashtag) }}" method="post">
                                @csrf
                                @method('put')
                                <button type="button" class="btn btn-outline-primary btn-sm me-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editHashtag"
                                    data-name="{{ $hashtag->name }}"
                                    data-action="{{ route('easy-manager.hashtag.update', $hashtag) }}"
                                    data-params="{{ $hashtag->toJson() }}">
                                    {{ __('EasyManager::fresns.button_edit') }}
                                </button>

                                @if ($hashtag->is_enabled)
                                    <input type="hidden" name="is_enabled" value="0"/>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">{{ __('EasyManager::fresns.button_deactivate') }}</button>
                                @else
                                    <input type="hidden" name="is_enabled" value="1"/>
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

    <!-- Edit Modal -->
    <div class="modal fade name-lang-parent" id="editHashtag" tabindex="-1" aria-labelledby="editHashtag" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('EasyManager::fresns.button_edit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        @csrf
                        @method('put')
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">{{ __('EasyManager::fresns.table_type') }}</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="type">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9"><button type="submit" class="btn btn-primary">{{ __('EasyManager::fresns.button_save') }}</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('#editHashtag').on('show.bs.modal', function (e) {
            if ($(this).data('is_back')) {
                return;
            }

            let button = $(e.relatedTarget);
            let name = button.data('name');
            let action = button.data('action');
            let params = button.data('params');

            $(this).parent('form').trigger('reset');

            if (! params) {
                return;
            }

            $(this).find('.modal-title').text(name);
            $(this).find('form').attr('action', action);
            $(this).find('select[name=type]').val(params.type);
        });
    </script>
@endpush

@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">GID</th>
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
                    <th scope="col">{{ __('EasyManager::fresns.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groups as $group)
                    <tr>
                        <th scope="row">{{ $group->id }}</th>
                        <td><a href="{{ $url.$group->gid }}" target="_blank">{{ $group->gid }}</a></td>
                        <td>{{ $group->getLangName($defaultLanguage) }}</td>
                        <td><a href="{{ route('easy-manager.post.index', ['groupId' => $group->id]) }}">{{ $group->post_count }}</a></td>
                        <td>{{ $group->post_digest_count }}</td>
                        <td><a href="{{ route('easy-manager.comment.index', ['groupId' => $group->id]) }}">{{ $group->comment_count }}</a></td>
                        <td>{{ $group->comment_digest_count }}</td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-sm me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editGroup"
                                data-name="{{ $group->getLangName($defaultLanguage) }}"
                                data-action="{{ route('easy-manager.group.update', $group) }}"
                                data-params="{{ $group->toJson() }}">
                                {{ __('EasyManager::fresns.button_edit') }}
                            </button>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('easy-manager.group.edit.permissions', ['groupId' => $group->id]) }}" role="button">{{ __('FsLang::panel.button_config_permission') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="py-3 d-flex justify-content-center">
        {{ $groups->appends(request()->all())->links() }}
    </div>


    <!-- Modal -->
    <div class="modal fade name-lang-parent" id="editGroup" tabindex="-1" aria-labelledby="editGroup" aria-hidden="true">
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
                            <label class="col-sm-3 col-form-label">GID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="gid">
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
        $('#editGroup').on('show.bs.modal', function (e) {
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
            $(this).find('input[name=gid]').val(params.gid);
        });
    </script>
@endpush

@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">AID</th>
                    <th scope="col">UID</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_username') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_nickname') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_gender') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_main_role') }}</th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_posts') }}
                        @if (request('orderBy') == 'post_publish_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_publish_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'post_publish_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_publish_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'post_publish_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_comments') }}
                        @if (request('orderBy') == 'comment_publish_count' && request('orderDirection') == 'desc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_publish_count', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'comment_publish_count' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_publish_count', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'comment_publish_count', 'orderDirection' => 'desc']) }}" class="link-secondary"><i class="bi bi-caret-down"></i></a>
                        @endif
                    </th>
                    <th scope="col">{{ __('EasyManager::fresns.table_followers') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_expiry_date') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_wait_delete') }}</th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_register_time') }}
                        @if (request('orderBy') == 'id' && request('orderDirection') == 'desc' || empty(request('orderBy')))
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'id', 'orderDirection' => 'asc']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @elseif (request('orderBy') == 'id' && request('orderDirection') == 'asc')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'id', 'orderDirection' => 'desc']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'id', 'orderDirection' => 'asc']) }}" class="link-secondary"><i class="bi bi-caret-up"></i></a>
                        @endif
                    </th>
                    <th scope="col">{{ __('EasyManager::fresns.table_status') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @empty($user->profile)
                        @continue
                    @endempty

                    <tr>
                        <th scope="row">{{ $user->profile->id }}</th>
                        <td><a href="{{ route('easy-manager.account.index', ['aid' => $user->profile->account->aid]) }}">{{ $user->profile->account->aid }}</a></td>
                        @if ($identifier == 'uid')
                            <td>
                                <a href="{{ $url.$user->profile->uid }}" target="_blank">{{ $user->profile->uid }}</a>
                                {!! $user->profile->verified_status ? '<i class="bi bi-patch-check-fill text-primary"></i>' : '' !!}
                            </td>
                            <td>{{ $user->profile->username }}</td>
                        @else
                            <td>{{ $user->profile->uid }}</td>
                            <td>
                                <a href="{{ $url.$user->profile->username }}" target="_blank">{{ $user->profile->username }}</a>
                                {!! $user->profile->verified_status ? '<i class="bi bi-patch-check-fill text-primary"></i>' : '' !!}
                            </td>
                        @endif
                        <td>{{ $user->profile->nickname }}</td>
                        <td>
                            @if ($user->profile->gender == 2)
                                {{ __('EasyManager::fresns.option_male') }}
                            @elseif ($user->profile->gender == 3)
                                {{ __('EasyManager::fresns.option_female') }}
                            @endif
                        </td>
                        <td>
                            @if ($user->profile?->main_role)
                                <a href="#user{{ $user->profile->id }}RolesModal" data-bs-toggle="modal">{{ $user->profile?->main_role?->getLangContent('name', $defaultLanguage) }}</a>
                            @else
                                <a href="#user{{ $user->profile->id }}RolesModal" data-bs-toggle="modal" class="link-secondary">Null</a>
                            @endif

                            @if (count($user->profile->roles) > 1)
                                <span class="badge rounded-pill text-bg-light">{{ count($user->profile->roles) }}</span>
                            @endif

                            <!-- Modal -->
                            <div class="modal fade" id="user{{ $user->profile->id }}RolesModal" tabindex="-1" aria-labelledby="{{ $user->profile->id }}rolesModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="{{ $user->profile->id }}rolesModalLabel">{{ __('EasyManager::fresns.table_role_list') }}</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr class="table-secondary">
                                                        <th scope="col">RID</th>
                                                        <th scope="col">{{ __('EasyManager::fresns.table_role_name') }}</th>
                                                        <th scope="col">{{ __('EasyManager::fresns.table_role_is_main') }}</th>
                                                        <th scope="col">{{ __('EasyManager::fresns.table_expiry_date') }}</th>
                                                        <th scope="col">{{ __('EasyManager::fresns.table_restore_role') }}</th>
                                                        <th scope="col" class="text-center" style="width:30%">{{ __('EasyManager::fresns.table_options') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->profile->getUserRolesFullInfo($defaultLanguage) as $role)
                                                        <tr id="{{ $role['rid'] }}">
                                                            <th scope="row">{{ $role['rid'] }}</th>
                                                            <td>{{ $role['name'] }}</td>
                                                            <td>{!! $role['isMain'] ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                                                            <td>{{ $role['expiryDateTime'] }}</td>
                                                            <td>{{ $role['restoreRole'] ? $role['restoreRole']['name'] : '' }}</td>
                                                            <td class="text-center">
                                                                @if (count($user->profile->getUserRolesFullInfo($defaultLanguage)) > 1)
                                                                    <form action="{{ route('easy-manager.user.delete.role', ['uid' => $user->profile->uid, 'rid' => $role['rid']]) }}" method="post">
                                                                        @csrf
                                                                        @method('delete')
                                                                        <button type="submit" class="btn btn-link link-danger btn-sm">{{ __('EasyManager::fresns.button_delete') }}</button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary store-role"
                                                data-bs-target="#storeRoleModal"
                                                data-bs-toggle="modal"
                                                data-uid="{{ $user->profile->uid }}"
                                                data-nickname="{{ $user->profile->nickname }}"
                                                data-username="{{ $user->profile->username }}"
                                                data-action="{{ route('easy-manager.user.store.role', ['uid' => $user->profile->uid]) }}">{{ __('EasyManager::fresns.button_add_role') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><a href="{{ route('easy-manager.post.index', ['userId' => $user->profile->id]) }}">{{ $user->post_publish_count }}</a></td>
                        <td><a href="{{ route('easy-manager.comment.index', ['userId' => $user->profile->id]) }}">{{ $user->comment_publish_count }}</a></td>
                        <td>{{ $user->follow_me_count }}</td>
                        <td>{{ $user->profile->expired_at }}</td>
                        <td>
                            @if ($user->profile->wait_delete)
                                {{ __('EasyManager::fresns.option_yes') }} <span class="badge bg-secondary">{{ $account->wait_delete_at }}</span>
                            @else
                                {{ __('EasyManager::fresns.option_no') }}
                            @endif
                        </td>
                        <td>{{ $user->profile->created_at }}</td>
                        <td>{!! $user->profile->is_enabled ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                        <td>
                            <form action="{{ route('easy-manager.user.update', $user->profile) }}" method="post">
                                @csrf
                                @method('put')
                                @if ($user->profile->is_enabled)
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
        {{ $users->appends(request()->all())->links() }}
    </div>

    {{-- modal --}}
    <div class="modal fade" id="storeRoleModal" aria-hidden="true" aria-labelledby="storeRoleModalLabel" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="storeRoleModalLabel">{{ __('EasyManager::fresns.button_add_role') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        @csrf
                        @method('post')
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">{{ __('EasyManager::fresns.user') }}</label>
                            <div class="col-sm-9 pt-1">
                                <span id="nickname"></span>
                                <span id="username" class="ms-2 fs-8 text-secondary"></span>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">{{ __('EasyManager::fresns.table_role') }}</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="rid">
                                    <option selected>{{ __('EasyManager::fresns.select_box_tip_role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->rid }}">{{ $role->getLangContent('name', $defaultLanguage) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">{{ __('EasyManager::fresns.table_role_is_main') }}</label>
                            <div class="col-sm-9 pt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_main" id="status_true" value="1" checked>
                                    <label class="form-check-label" for="status_true">{{ __('FsLang::panel.option_yes') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_main" id="status_false" value="0">
                                    <label class="form-check-label" for="status_false">{{ __('FsLang::panel.option_no') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">{{ __('EasyManager::fresns.table_expiry_date') }}</label>
                            <div class="col-sm-9">
                                <input type="datetime-local" class="form-control" name="expired_at" value="">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">{{ __('EasyManager::fresns.table_restore_role_after') }}</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="restore_role_rid">
                                    <option selected value="0">-</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->rid }}">{{ $role->getLangContent('name', $defaultLanguage) }}</option>
                                    @endforeach
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
        $('#storeRoleModal').on('show.bs.modal', function (e) {
            if ($(this).data('is_back')) {
                return;
            }

            let button = $(e.relatedTarget);
            let nickname = button.data('nickname');
            let username = button.data('username');
            let action = button.data('action');

            $(this).parent('form').trigger('reset');

            $(this).find('#nickname').text(nickname);
            $(this).find('#username').text('@'+ username);
            $(this).find('form').attr('action', action);
        });
    </script>
@endpush

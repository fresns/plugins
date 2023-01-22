<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsLang['userAvatar'] }}</span>
    <div class="form-control">
        <img src="{{ $data['avatar'] }}" class="rounded-circle" height="60">
    </div>
    <a class="btn btn-outline-secondary pt-4" href="{{ route('admin-menu.edit.user', [
        'uid' => $data['uid'],
        'langTag' => $langTag,
        'authUuid' => $authUuid,
        'avatar' => 'true',
    ]) }}" role="button">{{ $fsLang['remove'] }}</a>
</div>

<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsName['user_nickname_name'] }}</span>
    <input type="text" class="form-control" value="{{ $data['nickname'] }}" disabled readonly>
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#nicknameModal">{{ $fsLang['modify'] }}</button>
</div>

<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsName['user_username_name'] }}</span>
    <input type="text" class="form-control" value="{{ $data['username'] }}" disabled readonly>
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#usernameModal">{{ $fsLang['modify'] }}</button>
</div>

<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsName['user_role_name'] }}</span>
    <input type="text" class="form-control" value="{{ $data['roleName'] }}" disabled readonly>
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#roleModal">{{ $fsLang['modify'] }}</button>
</div>

<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsLang['status'] }}</span>
    <div class="form-control">
        @if ($data['status'])
            <i class="bi bi-check-circle text-success"></i> {{ $fsLang['activate'] }}
        @else
            <i class="bi bi-slash-circle text-danger"></i> {{ $fsLang['deactivate'] }}
        @endif
    </div>
    @if ($data['status'])
        <a class="btn btn-outline-secondary" href="{{ route('admin-menu.edit.user', [
            'uid' => $data['uid'],
            'langTag' => $langTag,
            'authUuid' => $authUuid,
            'status' => 'false',
        ]) }}" role="button">{{ $fsLang['deactivate'] }}</a>
    @else
        <a class="btn btn-outline-secondary" href="{{ route('admin-menu.edit.user', [
            'uid' => $data['uid'],
            'langTag' => $langTag,
            'authUuid' => $authUuid,
            'status' => 'true',
        ]) }}" role="button">{{ $fsLang['activate'] }}</a>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="roleModalLabel">{{ $fsLang['choose'] }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach($roles as $role)
                        <a href="{{ route('admin-menu.edit.user', [
                            'uid' => $data['uid'],
                            'langTag' => $langTag,
                            'authUuid' => $authUuid,
                            'roleId' => $role['rid'],
                        ]) }}" class="list-group-item list-group-item-action">{{ $role['name'] }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- nickname Modal -->
<div class="modal fade" id="nicknameModal" tabindex="-1" aria-labelledby="nicknameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="nicknameModalLabel">{{ $fsName['user_nickname_name'] }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin-menu.edit.user') }}" method="GET">
                    <input type="hidden" name="uid" value="{{ $data['uid'] }}">
                    <input type="hidden" name="langTag" value="{{ $langTag }}">
                    <input type="hidden" name="authUuid" value="{{ $authUuid }}">

                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ $fsName['user_nickname_name'] }}</span>
                        <input type="text" class="form-control" name="nickname" value="{{ $data['nickname'] }}">
                        <button class="btn btn-outline-primary" type="submit">{{ $fsLang['saveChanges'] }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- username Modal -->
<div class="modal fade" id="usernameModal" tabindex="-1" aria-labelledby="usernameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="usernameModalLabel">{{ $fsName['user_username_name'] }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin-menu.edit.user') }}" method="GET">
                    <input type="hidden" name="uid" value="{{ $data['uid'] }}">
                    <input type="hidden" name="langTag" value="{{ $langTag }}">
                    <input type="hidden" name="authUuid" value="{{ $authUuid }}">

                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ $fsName['user_username_name'] }}</span>
                        <input type="text" class="form-control" name="username" value="{{ $data['username'] }}">
                        <button class="btn btn-outline-primary" type="submit">{{ $fsLang['saveChanges'] }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

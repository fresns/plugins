<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsLang['userAvatar'] }}</span>
    <div class="form-control">
        <img src="{{ $data['avatar'] }}" class="rounded-circle" width="60" height="60">
    </div>
    <a class="btn btn-outline-secondary pt-4" href="{{ route('admin-menu.edit.user', [
        'uid' => $data['uid'],
        'langTag' => $langTag,
        'authUlid' => $authUlid,
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
            <i class="bi bi-check-circle text-success"></i> <span class="text-success">{{ $fsLang['activate'] }}</span>
        @else
            <i class="bi bi-slash-circle text-danger"></i> <span class="text-danger">{{ $fsLang['deactivate'] }}</span>
        @endif
    </div>
    @if ($data['status'])
        <a class="btn btn-outline-secondary" href="{{ route('admin-menu.edit.user', [
            'uid' => $data['uid'],
            'langTag' => $langTag,
            'authUlid' => $authUlid,
            'status' => 'false',
        ]) }}" role="button">{{ $fsLang['deactivate'] }}</a>
    @else
        <a class="btn btn-outline-secondary" href="{{ route('admin-menu.edit.user', [
            'uid' => $data['uid'],
            'langTag' => $langTag,
            'authUlid' => $authUlid,
            'status' => 'true',
        ]) }}" role="button">{{ $fsLang['activate'] }}</a>
    @endif
</div>

<div class="d-grid gap-2">
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">{{ $fsLang['accountDelete'] }}</button>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">{{ $fsLang['accountDelete'] }}?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $fsLang['cancel'] }}</button>
                <a class="btn btn-danger" href="{{ route('admin-menu.delete.user', [
                    'uid' => $data['uid'],
                    'langTag' => $langTag,
                    'authUlid' => $authUlid,
                ]) }}" role="button">{{ $fsLang['confirm'] }}</a>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="roleModalLabel">{{ $fsLang['select'] }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach($roles as $role)
                        <a href="{{ route('admin-menu.edit.user', [
                            'uid' => $data['uid'],
                            'langTag' => $langTag,
                            'authUlid' => $authUlid,
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
                    <input type="hidden" name="authUlid" value="{{ $authUlid }}">

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
                    <input type="hidden" name="authUlid" value="{{ $authUlid }}">

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

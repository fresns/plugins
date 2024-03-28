{{-- avatar --}}
<form class="api-request-form" action="{{ route('admin-menu.api.edit.user') }}" method="patch">
    <input type="hidden" name="type" value="avatar">

    <div class="input-group mb-3">
        <span class="input-group-text">{{ $fsLang['userAvatar'] }}</span>
        <div class="form-control">
            <img src="{{ $detail['avatar'] }}" class="rounded-circle" width="60" height="60">
        </div>
        <button class="btn btn-outline-secondary" type="submit">{{ $fsLang['remove'] }}</button>
    </div>
</form>

{{-- nickname --}}
<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsName['user_nickname_name'] }}</span>
    <input type="text" class="form-control" value="{{ $detail['nickname'] }}" disabled readonly>
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#nicknameModal">{{ $fsLang['modify'] }}</button>
</div>

{{-- username --}}
<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsName['user_username_name'] }}</span>
    <input type="text" class="form-control" value="{{ $detail['username'] }}" disabled readonly>
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#usernameModal">{{ $fsLang['modify'] }}</button>
</div>

{{-- role --}}
<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsName['user_role_name'] }}</span>
    <input type="text" class="form-control" value="{{ $detail['roleName'] }}" disabled readonly>
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#roleModal">{{ $fsLang['modify'] }}</button>
</div>

{{-- status --}}
<div class="input-group mb-4">
    <span class="input-group-text">{{ $fsLang['status'] }}</span>
    <div class="form-control">
        @if ($detail['status'])
            <i class="bi bi-check-circle text-success"></i> <span class="text-success">{{ $fsLang['activate'] }}</span>
        @else
            <i class="bi bi-slash-circle text-danger"></i> <span class="text-danger">{{ $fsLang['deactivate'] }}</span>
        @endif
    </div>
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#statusConfirmModal">{{ $detail['status'] ? $fsLang['deactivate'] : $fsLang['activate']}}</button>
</div>

<div class="d-grid gap-2">
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">{{ $fsLang['accountDelete'] }}</button>
</div>

{{-- deleteConfirmModal --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p>{{ $fsLang['accountDelete'] }}?</p>
                <div id="deleteTip"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $fsLang['cancel'] }}</button>
                <form class="api-request-form" action="{{ route('admin-menu.api.delete.user') }}" method="delete">
                    <button class="btn btn-danger" type="submit">{{ $fsLang['confirm'] }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- roleModal -->
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
                        <a class="list-group-item list-group-item-action api-request-role @if ($detail['roleRid'] == $role['rid']) active @endif" href="#" data-rid="{{ $role['rid'] }}">{{ $role['name'] }}</a>
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
                <form class="api-request-form" action="{{ route('admin-menu.api.edit.user') }}" method="patch">
                    <input type="hidden" name="type" value="nickname">

                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ $fsName['user_nickname_name'] }}</span>
                        <input type="text" class="form-control" name="nickname" value="{{ $detail['nickname'] }}">
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
                <form class="api-request-form" action="{{ route('admin-menu.api.edit.user') }}" method="patch">
                    <input type="hidden" name="type" value="username">

                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ $fsName['user_username_name'] }}</span>
                        <input type="text" class="form-control" name="username" value="{{ $detail['username'] }}">
                        <button class="btn btn-outline-primary" type="submit">{{ $fsLang['saveChanges'] }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- statusConfirmModal --}}
<div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-labelledby="statusConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p>{{ $detail['status'] ? $fsLang['deactivate'] : $fsLang['activate']}}</p>
                @if (!$detail['status'])
                    <p>Deactivate status is only visible to the author</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $fsLang['cancel'] }}</button>

                <form class="api-request-form" action="{{ route('admin-menu.api.edit.user') }}" method="patch">
                    <input type="hidden" name="type" value="status">
                    <button class="btn btn-danger submit-btn" type="submit">{{ $fsLang['confirm'] }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        // api request role
        $('.api-request-role').click(function(e) {
            e.preventDefault();

            let btn = $(this);

            btn.prop('disabled', true);
            if (btn.children('.spinner-border').length == 0) {
                btn.prepend('<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> ');
            }
            btn.children('.spinner-border').removeClass('d-none');

            const rid = btn.data('rid');

            $.ajax({
                url: "{{ route('admin-menu.api.edit.user') }}",
                type: "patch",
                data: {
                    type: 'role',
                    rid: rid,
                },
                success: function (res) {
                    if (res.code != 0) {
                        tips(res.message, true);
                        return;
                    }

                    new bootstrap.Modal('#roleModal').hide();

                    tips(res.message, false);
                    $('#main').addClass('d-none');

                    fresnsCallbackSend('reload', res.data);
                },
                complete: function (e) {
                    btn.prop('disabled', false);
                    btn.find('.spinner-border').remove();
                },
            });
        });
    </script>
@endpush

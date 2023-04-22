<div class="alert alert-secondary" role="alert">
    {{ Str::limit(strip_tags($data['content']), 30) }}
    <hr>
    <p class="text-end mb-0">{{ $data['creator']['nickname'] }}</p>
</div>

<form action="{{ route('admin-menu.edit.comment') }}" method="GET">
    <input type="hidden" name="cid" value="{{ $data['cid'] }}">
    <input type="hidden" name="langTag" value="{{ $langTag }}">
    <input type="hidden" name="authUlid" value="{{ $authUlid }}">
    <div class="input-group mb-3">
        <span class="input-group-text">{{ $fsLang['contentDigest'] }}</span>
        <select class="form-select" name="digestState">
            <option value="1" {{ $data['digestState'] == 1 ? 'selected' : '' }}>No</option>
            <option value="2" {{ $data['digestState'] == 2 ? 'selected' : '' }}>General Digest</option>
            <option value="3" {{ $data['digestState'] == 3 ? 'selected' : '' }}>Advanced Digest</option>
        </select>
        <button class="btn btn-outline-secondary" type="submit">{{ $fsLang['setting'] }}</button>
    </div>
</form>

<form action="{{ route('admin-menu.edit.comment') }}" method="GET">
    <input type="hidden" name="cid" value="{{ $data['cid'] }}">
    <input type="hidden" name="langTag" value="{{ $langTag }}">
    <input type="hidden" name="authUlid" value="{{ $authUlid }}">
    <div class="input-group mb-3">
        <span class="input-group-text">{{ $fsLang['contentSticky'] }}</span>
        <select class="form-select" name="isSticky">
            <option value="false" {{ $data['isSticky'] ? '' : 'selected' }}>No</option>
            <option value="true" {{ $data['isSticky'] ? 'selected' : '' }}>Yes</option>
        </select>
        <button class="btn btn-outline-secondary" type="submit">{{ $fsLang['setting'] }}</button>
    </div>
</form>

<div class="input-group mb-4">
    <span class="input-group-text">{{ $fsLang['status'] }}</span>
    <div class="form-control">
        @if ($data['status'])
            <i class="bi bi-check-circle text-success"></i> <span class="text-success">{{ $fsLang['activate'] }}</span>
        @else
            <i class="bi bi-slash-circle text-danger"></i> <span class="text-danger">{{ $fsLang['deactivate'] }}</span>
        @endif
        <span class="ms-3 form-text">Deactivate status is only visible to the author</span>
    </div>
    @if ($data['status'])
        <a class="btn btn-outline-secondary" href="{{ route('admin-menu.edit.comment', [
            'cid' => $data['cid'],
            'langTag' => $langTag,
            'authUlid' => $authUlid,
            'status' => 'false',
        ]) }}" role="button">{{ $fsLang['deactivate'] }}</a>
    @else
        <a class="btn btn-outline-secondary" href="{{ route('admin-menu.edit.comment', [
            'cid' => $data['cid'],
            'langTag' => $langTag,
            'authUlid' => $authUlid,
            'status' => 'true',
        ]) }}" role="button">{{ $fsLang['activate'] }}</a>
    @endif
</div>

<div class="d-grid gap-2">
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">{{ $fsLang['delete'] }}</button>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">{{ $fsLang['delete'] }}?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $fsLang['cancel'] }}</button>
                <a class="btn btn-danger" href="{{ route('admin-menu.delete.comment', [
                    'cid' => $data['cid'],
                    'langTag' => $langTag,
                    'authUlid' => $authUlid,
                ]) }}" role="button">{{ $fsLang['confirm'] }}</a>
            </div>
        </div>
    </div>
</div>

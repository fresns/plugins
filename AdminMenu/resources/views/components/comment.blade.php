<div class="alert alert-secondary" role="alert">
    {{ Str::limit(strip_tags($data['content']), 30) }}
    <hr>
    <p class="text-end mb-0">{{ $data['creator']['nickname'] }}</p>
</div>

<div class="d-grid gap-2">
    <a class="btn btn-danger" href="{{ route('admin-menu.delete.comment', [
        'cid' => $data['cid'],
        'langTag' => $langTag,
        'authUuid' => $authUuid,
    ]) }}" role="button">{{ $fsLang['delete'] }}</a>
</div>

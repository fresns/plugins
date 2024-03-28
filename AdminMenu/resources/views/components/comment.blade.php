<div class="alert alert-secondary" role="alert">
    {{ Str::limit(strip_tags($detail['content']), 30) }}
    <hr>
    <p class="text-end mb-0">{{ $detail['author']['nickname'] }}</p>
</div>

<form class="api-request-comment-form" action="{{ route('admin-menu.api.edit.comment') }}" method="patch">
    {{-- digestState --}}
    <div class="input-group mb-3">
        <span class="input-group-text">{{ $fsLang['contentDigest'] }}</span>
        <select class="form-select" name="digestState">
            <option value="1" {{ $detail['digestState'] == 1 ? 'selected' : '' }}>{{ $fsLang['no'] }}</option>
            <option value="2" {{ $detail['digestState'] == 2 ? 'selected' : '' }}>{{ $fsLang['contentDigestGeneral'] }}</option>
            <option value="3" {{ $detail['digestState'] == 3 ? 'selected' : '' }}>{{ $fsLang['contentDigestPremium'] }}</option>
        </select>
        <button class="btn btn-outline-secondary submit-btn" type="submit" data-input-name="digestState">{{ $fsLang['setting'] }}</button>
    </div>

    {{-- isSticky --}}
    <div class="input-group mb-3">
        <span class="input-group-text">{{ $fsLang['contentSticky'] }}</span>
        <select class="form-select" name="isSticky">
            <option value="false" {{ $detail['isSticky'] ? '' : 'selected' }}>{{ $fsLang['no'] }}</option>
            <option value="true" {{ $detail['isSticky'] ? 'selected' : '' }}>{{ $fsLang['yes'] }}</option>
        </select>
        <button class="btn btn-outline-secondary submit-btn" type="submit" data-input-name="isSticky">{{ $fsLang['setting'] }}</button>
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
            <br>
            <span class="form-text">Deactivate status is only visible to the author</span>
        </div>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#statusConfirmModal">{{ $detail['status'] ? $fsLang['deactivate'] : $fsLang['activate']}}</button>
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
                    <button class="btn btn-danger submit-btn" type="submit" data-input-name="status">{{ $fsLang['confirm'] }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="d-grid gap-2">
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">{{ $fsLang['delete'] }}</button>
</div>

{{-- deleteConfirmModal --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p>{{ $fsLang['delete'] }}?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $fsLang['cancel'] }}</button>
                <form class="api-request-form" action="{{ route('admin-menu.api.delete.comment') }}" method="delete">
                    <button class="btn btn-danger" type="submit">{{ $fsLang['confirm'] }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        let clickedInputName = null;
        let clickedBtn = null;

        $('.submit-btn').click(function(e) {
            clickedInputName = $(this).data('input-name');

            clickedBtn = $(this);
        });

        $('.api-request-comment-form').submit(function (e) {
            e.preventDefault();

            clickedBtn.prop('disabled', true);
            if (clickedBtn.children('.spinner-border').length == 0) {
                clickedBtn.prepend('<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> ');
            }
            clickedBtn.children('.spinner-border').removeClass('d-none');

            let form = $(this);

            const actionUrl = form.attr('action'),
                methodType = form.attr('method') || 'POST';

            let newValue = form.find('input[name="' + clickedInputName + '"]').val();
            if (clickedInputName == 'digestState' || clickedInputName == 'isSticky') {
                newValue = form.find('select[name="' + clickedInputName + '"]').val();
            }

            let data = {
                inputName: clickedInputName,
                newValue: newValue,
            };

            $.ajax({
                url: actionUrl,
                type: methodType,
                data: data,
                success: function (res) {
                    if (res.code != 0) {
                        tips(res.message, true);
                        return;
                    }

                    new bootstrap.Modal('#statusConfirmModal').hide();

                    tips(res.message, false);
                    $('#main').addClass('d-none');

                    fresnsCallbackSend('reload', res.data);
                },
                complete: function (e) {
                    if (clickedBtn) {
                        clickedBtn.prop('disabled', false);
                        clickedBtn.find('.spinner-border').remove();

                        clickedBtn = null;
                    }
                },
            });

            clickedButtonType = null;
        });
    </script>
@endpush

<div class="alert alert-secondary" role="alert">
    {{ $detail['title'] ?? Str::limit(strip_tags($detail['content']), 30) }}
    <hr>
    <p class="text-end mb-0">{{ $detail['author']['nickname'] }}</p>
</div>

{{-- group --}}
<div class="input-group mb-3">
    <span class="input-group-text">{{ $fsName['group_name'] }}</span>
    <input type="text" class="form-control" value="{{ $detail['group']['name'] ?? null }}" disabled readonly>
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#groupSelectModal" data-initialized="0" id="editor-group">{{ $detail['group'] ? $fsLang['modify'] : $fsLang['setting'] }}</button>
</div>

<form class="api-request-post-form" action="{{ route('admin-menu.api.edit.post') }}" method="patch">
    {{-- title --}}
    <div class="input-group mb-3">
        <span class="input-group-text">{{ $fsLang['editorTitle'] }}</span>
        <input type="text" class="form-control" name="title" value="{{ $detail['title'] }}">
        <button class="btn btn-outline-secondary submit-btn" type="submit" data-input-name="title">{{ $fsLang['modify'] }}</button>
    </div>

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

    {{-- contentSticky --}}
    <div class="input-group mb-3">
        <span class="input-group-text">{{ $fsLang['contentSticky'] }}</span>
        <select class="form-select" name="stickyState">
            <option value="1" {{ $detail['stickyState'] == 1 ? 'selected' : '' }}>{{ $fsLang['no'] }}</option>
            <option value="2" {{ $detail['stickyState'] == 2 ? 'selected' : '' }}>{{ $fsLang['contentStickyGroup'] }}</option>
            <option value="3" {{ $detail['stickyState'] == 3 ? 'selected' : '' }}>{{ $fsLang['contentStickyGlobal'] }}</option>
        </select>
        <button class="btn btn-outline-secondary submit-btn" type="submit" data-input-name="stickyState">{{ $fsLang['setting'] }}</button>
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
                <form class="api-request-form" action="{{ route('admin-menu.api.delete.post') }}" method="delete">
                    <button class="btn btn-danger" type="submit">{{ $fsLang['confirm'] }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- groupSelectModal --}}
<div class="modal fade" id="groupSelectModal" tabindex="-1" aria-labelledby="groupSelectModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $fsLang['select'] }}</h5>
                <button type="button" class="btn-close" data-bs-target="#createModal" data-bs-toggle="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-start">
                {{-- Group List --}}
                <div id="editor-top-groups">
                    <form class="api-request-post-form" action="{{ route('admin-menu.api.edit.post') }}" method="patch">
                        <input type="hidden" name="group" value="">

                        <button type="submit" class="btn btn-outline-secondary btn-sm submit-btn mb-2 w-100" data-input-name="group">
                            {{ $fsLang['editorNoGroup'] }}
                        </button>
                    </form>
                    <div class="list-group"></div>
                    <div class="list-group-addmore text-center mb-2 fs-7 text-secondary"></div>
                </div>

                <div id="group-list-1" class="d-flex justify-content-start"></div>
                {{-- Group List --}}
            </div>
            <div class="modal-footer">
                <form class="api-request-post-form" action="{{ route('admin-menu.api.edit.post') }}" method="patch">
                    <input type="hidden" name="group" value="" id="group-select-input">
                    <button type="submit" class="btn btn-primary submit-btn" id="group-submit" data-input-name="group" disabled>{{ $fsLang['confirm'] }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        // top groups
        $('#editor-group').on('click', function (obj) {
            var initialized = $(this).attr('data-initialized');

            console.log('initialized', initialized);

            if (initialized == 1) {
                return;
            }

            editorGroup.editorAjaxGetTopGroups();
        });

        // Editor Groups
        var editorGroup = {
            // editorGroupSelect
            editorGroupSelect: function (obj) {
                var gid = $(obj).data('gid');
                var name = $(obj).text();
                var publish = $(obj).data('publish');
                var level = $(obj).data('level');
                var subgroupCount = $(obj).data('subgroup-count');

                console.log('editorGroupSelect', gid, name, publish, subgroupCount);

                var btnGid = $('#group-select-input').val();

                if (gid != btnGid) {
                    $('.group-list-' + gid).addClass('active');
                    $('.group-list-' + btnGid).removeClass('active');
                }

                $('#group-select-input').val(gid);

                if (publish == 1) {
                    $('#group-submit').prop('disabled', false);
                } else {
                    $('#group-submit').prop('disabled', true);
                }

                downLevel = level + 1;
                editorGroup.editorRemoveGroupBox(downLevel);

                if (subgroupCount) {
                    editorGroup.editorAjaxGetGroupList(level, gid, (page = 1));
                }
            },

            // editorAjaxGetTopGroups
            editorAjaxGetTopGroups: function (topGroupsPage = 1) {
                $('#editor-top-groups .list-group').append(
                    '<div class="text-center group-spinners mt-2"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                );
                $('#editor-top-groups .list-group-addmore').empty().append("{{ $fsLang['loading'] }}");

                let html = '';

                $.get("{{ route('admin-menu.api.groups') }}?topGroups=1&page=" + topGroupsPage, function (data) {
                        let apiData = data.data;

                        let groups = apiData.list;

                        topGroupsPage = topGroupsPage + 1;

                        if (groups.length > 0) {
                            $.each(groups, function (i, group) {
                                html +=
                                    '<a href="javascript:void(0)" data-gid="' +
                                    group.gid +
                                    '" data-level="1" data-subgroup-count="' +
                                    group.subgroupCount +
                                    '" onclick="editorGroup.editorGroupSelect(this)" class="list-group-item list-group-item-action group-list-' +
                                    group.gid +
                                    '"';

                                if (group.publishRule.canPublish && group.publishRule.allowPost) {
                                    html += ' data-publish="1">';
                                } else {
                                    html += ' data-publish="0">';
                                }

                                if (group.cover) {
                                    html += '<img src="' + group.cover + '" height="20" class="me-1">';
                                }

                                html += group.name + '</a>';
                            });
                        }

                        if (apiData.pagination.currentPage == 1) {
                            $('#editor-top-groups .list-group').each(function () {
                                $(this).empty();
                                $(this).next().empty();
                            });
                        }

                        $('#editor-top-groups .list-group .group-spinners').remove();
                        $('#editor-top-groups .list-group').append(html);

                        $('#editor-top-groups .list-group-addmore').empty();
                        if (apiData.pagination.currentPage < apiData.pagination.lastPage) {
                            let addMoreHtml = `<a href="javascript:void(0)"  class="add-more mt-3" onclick="editorGroup.editorAjaxGetTopGroups(${topGroupsPage})">{{ $fsLang['clickToLoadMore'] }}</a>`;
                            $('#editor-top-groups .list-group-addmore').append(addMoreHtml);
                        }

                        $('#editor-group').attr('data-initialized', 1);
                    }
                );
            },

            // editorAjaxGetGroupList
            editorAjaxGetGroupList: function (level, gid, page = 1) {
                var parentTargetId = 'group-list-' + level;
                level = level + 1;

                var targetId = 'group-list-' + level;
                var targetElement = $('#' + targetId);

                if (targetElement.length > 0) {
                    targetElement.empty().append('<div class="list-group"></div>');
                } else {
                    $('#' + parentTargetId).append(
                        '<div id="' +
                            targetId +
                            '" class="d-flex justify-content-start ms-4"><div class="list-group"></div></div>'
                    );
                }

                $('#' + targetId + ' .list-group').append(
                    '<div class="text-center group-spinners mt-2"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="list-group-addmore text-center mb-2 fs-7 text-secondary"></div></div>'
                );
                $('#' + targetId + ' .list-group-addmore').empty().append("{{ $fsLang['loading'] }}");

                let html = '';

                $.get("{{ route('admin-menu.api.groups') }}?pageSize=30&gid=" + gid + '&page=' + page, function (data) {
                    let apiData = data.data;

                    let groups = apiData.list;

                    page = page + 1;

                    if (groups.length > 0) {
                        $.each(groups, function (i, group) {
                            html +=
                                '<a href="javascript:void(0)" data-gid="' +
                                group.gid +
                                '" data-level="' +
                                level +
                                '" data-subgroup-count="' +
                                group.subgroupCount +
                                '" onclick="editorGroup.editorGroupSelect(this)" class="list-group-item list-group-item-action group-list-' +
                                group.gid +
                                '"';

                            if (group.publishRule.canPublish && group.publishRule.allowPost) {
                                html += ' data-publish="1">';
                            } else {
                                html += ' data-publish="0">';
                            }

                            if (group.cover) {
                                html += '<img src="' + group.cover + '" height="20" class="me-1">';
                            }

                            html += group.name + '</a>';
                        });
                    }

                    if (apiData.pagination.currentPage == 1) {
                        $('#' + targetId + ' .list-group').each(function () {
                            $(this).empty();
                            $(this).next().empty();
                        });
                    }

                    $('#' + targetId + ' .list-group .group-spinners').remove();
                    $('#' + targetId + ' .list-group').append(html);

                    $('#' + targetId + ' .list-group-addmore').empty();
                    if (apiData.pagination.currentPage < apiData.pagination.lastPage) {
                        let addMoreHtml = `<a href="javascript:void(0)"  class="add-more mt-3" onclick="editorGroup.editorAjaxGetTopGroups(${topGroupsPage})">{{ $fsLang['clickToLoadMore'] }}</a>`;
                        $('#' + targetId + ' .list-group-addmore').append(addMoreHtml);
                    }

                    $('#editor-group').attr('data-initialized', 1);
                });
            },

            // editorRemoveGroupBox
            editorRemoveGroupBox: function (level) {
                var targetId = 'group-list-' + level;
                var targetElement = $('#' + targetId);

                console.log('editorRemoveGroupBox', targetId);

                if (targetElement.length > 0) {
                    targetElement.remove();
                    editorGroup.editorRemoveGroupBox(level);
                }
            },
        };

        let clickedInputName = null;
        let clickedBtn = null;

        $('.submit-btn').click(function(e) {
            clickedInputName = $(this).data('input-name');

            clickedBtn = $(this);
        });

        $('.api-request-post-form').submit(function (e) {
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
            if (clickedInputName == 'digestState' || clickedInputName == 'stickyState') {
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

<div class="alert alert-secondary" role="alert">
    {{ $data['title'] ?? Str::limit(strip_tags($data['content']), 30) }}
    <hr>
    <p class="text-end mb-0">{{ $data['creator']['nickname'] }}</p>
</div>

<div class="input-group mb-4">
    <span class="input-group-text">{{ $fsName['group_name'] }}</span>
    <input type="text" class="form-control" value="{{ $data['group']['gname'] ?? null }}" disabled readonly>
    @if ($data['group'])
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#fresns-groups">{{ $fsLang['modify'] }}</button>
    @else
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#fresns-groups">{{ $fsLang['setting'] }}</button>
    @endif
</div>

<div class="d-grid gap-2">
    <a class="btn btn-danger" href="{{ route('admin-menu.delete.post', [
        'pid' => $data['pid'],
        'langTag' => $langTag,
        'authUuid' => $authUuid,
    ]) }}" role="button">{{ $fsLang['delete'] }}</a>
</div>


{{-- Group Modal --}}
<div class="modal fade" id="fresns-groups" tabindex="-1" aria-labelledby="fresns-groups" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $fsLang['choose'] }}</h5>
                <button type="button" class="btn-close" data-bs-target="#createModal" data-bs-toggle="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Group List --}}
                <div class="d-flex align-items-start">
                    <div class="nav flex-column nav-pills me-3" id="v-pills-post-box-tab" role="tablist" aria-orientation="vertical">
                        <a role="button" class="btn btn-outline-secondary btn-sm mb-2" href="{{ route('admin-menu.edit.post.group', [
                            'pid' => $data['pid'],
                            'langTag' => $langTag,
                            'authUuid' => $authUuid,
                        ]) }}">{{ $fsLang['editorNoGroup'] }} {{ $fsName['group_name'] }}</a>

                        {{-- Group Categories --}}
                        @foreach($groupCategories as $groupCategory)
                            <button class="nav-link group-categories" data-page-size="15" data-page="1" data-action="{{ route('admin-menu.group.list', ['gid' => $groupCategory['gid']]) }}" id="v-pills-{{ $groupCategory['gid'] }}-post-box-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{ $groupCategory['gid'] }}-post-box" type="button" role="tab" aria-controls="v-pills-{{ $groupCategory['gid'] }}-post-box" aria-selected="false">
                                @if ($groupCategory['cover'])
                                    <img src="{{ $groupCategory['cover'] }}" height="20">
                                @endif
                                {{ $groupCategory['gname'] }}
                            </button>
                        @endforeach
                    </div>

                    <div class="tab-content" id="v-pills-post-box-tabContent" style="width:60%;">
                        {{-- Group --}}
                        <div id="fresns-group-list">
                            <div class="list-group"></div>
                            <div class="list-group-addmore text-center my-3 fs-7"></div>
                        </div>
                    </div>
                </div>
                {{-- Group List End --}}
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        function boxAjaxGetGroupList(action, pageSize = 15, page = 1){
            let html = '';

            $('#fresns-group-list .list-group').append('<div class="text-center mt-4 group-spinners"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $('#fresns-group-list .list-group-addmore').empty().append("{{ $fsLang['loading'] }}");

            $.get(action + "&page=" + page + "&pageSize=" + pageSize, function (data){
                let lists = data.data.list
                page = page + 1
                if (lists.length > 0) {
                    $.each(lists, function (i, list){
                        html += '<a href="{{ route('admin-menu.edit.post.group', ['pid' => $data['pid'], 'langTag' => $langTag, 'authUuid' => $authUuid]) }}&gid=' + list.gid +'" class="list-group-item list-group-item-action">';
                        if (list.cover) {
                            html += '<img src="' + list.cover + '" height="20" class="me-1">';
                        }
                        html += list.gname + '</a>'
                    });
                }

                if (data.data.paginate.currentPage === 1){
                    $('#fresns-group-list .list-group').each(function (){
                        $(this).empty();
                        $(this).next().empty();
                    });
                }

                $('#fresns-group-list .list-group .group-spinners').remove();
                $('#fresns-group-list .list-group').append(html);

                $('#fresns-group-list .list-group-addmore').empty();
                if (data.data.paginate.currentPage < data.data.paginate.lastPage) {
                    let addMoreHtml = `<a href="javascript:void(0)"  class="add-more" onclick="boxAjaxGetGroupList('${action}', ${pageSize}, ${page})">{{ $fsLang['clickToLoadMore'] }}</a>`;
                    $('#fresns-group-list .list-group-addmore').append(addMoreHtml);
                }

                $("#fresns-groups .group-categories").each(function (){
                    $(this).attr('disabled', false)
                })
            })
        }

        $(function (){
            $("#fresns-groups .group-categories").on('click', function (){
                let obj = $(this),
                    pageSize = obj.data('page-size'),
                    page = obj.data('page'),
                    action = obj.data('action')

                $("#fresns-groups .group-categories").each(function (){
                    $(this).attr('disabled', true)
                })

                $('#fresns-groups .list-group').each(function (){
                    $(this).empty();
                    $(this).next().empty();
                });
                boxAjaxGetGroupList(action, pageSize, page)
            })
        })
    </script>
@endpush

@extends('EditorWorkspace::commons.master')

@section('content')
    <div class="container" id="createModal">
        <form class="form-post-box mt-2" action="{{ route('editor-workspace.work.quick.publish') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="authUlid" value="{{ $authUlid }}">
            <input type="hidden" name="postGid" value="">

            @if ($fsConfigs['post_editor_group'])
                <div class="shadow-sm">
                    <div class="d-grid">
                        <button class="rounded-0 border-0 list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2" style="background-color: aliceblue;" type="button" data-bs-toggle="modal" data-bs-target="#groupModal">
                            <span class="py-2 ms-1">
                                <i class="bi bi-archive-fill me-2"></i>
                                <span id="post-box-group">{{ $fsConfigs['group_name'] }}: {{ $fsLang['editorNoSelectGroup'] }}</span>
                            </span>
                            <span class="py-2"><i class="fa-solid fa-chevron-right"></i></span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Content Start --}}
            <div class="p-3">
                {{-- Title --}}
                @if ($fsConfigs['post_editor_title'])
                    <div class="collapse @if ($fsConfigs['post_editor_title_view'] == 1) show @endif" id="quickTitleCollapse">
                        <input type="text" class="form-control form-control-lg rounded-0 border-0 ps-2"
                            name="postTitle"
                            placeholder="{{ $fsLang['editorTitle'] }} (@if ($fsConfigs['post_editor_title_required']) {{ $fsLang['editorRequired'] }} @else {{ $fsLang['editorOptional'] }} @endif)"
                            maxlength="{{ $fsConfigs['post_editor_title_length'] }}"
                            @if ($fsConfigs['post_editor_title_required']) required @endif >
                        <hr>
                    </div>
                @endif

                {{-- Content --}}
                <textarea class="form-control rounded-0 border-0 fresns-content" name="content" id="quick-publish-post-content" rows="10" placeholder="{{ $fsLang['editorContent'] }}"></textarea>

                {{-- Content is Markdown --}}
                <div class="bd-highlight my-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isMarkdown" value="1" id="isMarkdown">
                        <label class="form-check-label" for="isMarkdown">{{ $fsLang['editorContentMarkdown'] }}</label>
                    </div>
                </div>

                {{-- Function Buttons --}}
                <div class="d-flex mt-2">
                    {{-- Title --}}
                    @if ($fsConfigs['post_editor_title'] && $fsConfigs['post_editor_title_view'] == 2)
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="collapse" href="#quickTitleCollapse" aria-expanded="false" aria-controls="quickTitleCollapse"><i class="bi bi-textarea-t"></i></button>
                    @endif

                    {{-- Upload Image --}}
                    @if ($fsConfigs['post_editor_image'])
                        <div class="input-group">
                            <label class="input-group-text" for="post-file">{{ $fsLang['editorImages'] }}</label>
                            <input type="file" class="form-control" accept="{{ $fileAccept['images'] ?? null }}" name="image" id="post-file">
                        </div>
                    @endif
                </div>
                <div class="d-flex mt-2">
                    <div class="input-group">
                        <span class="input-group-text">{{ $fsLang['contentAuthor'] }}</span>
                        <select class="form-select" required name="uid">
                            <option selected disabled>Select Author</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->uid }}">{{ $user->nickname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group ms-3">
                        <span class="input-group-text">{{ $fsLang['contentPublishTime'] }}</span>
                        <input type="datetime-local" name="datetime" class="form-control" id="datetimeInput" onchange="validateDateTime()">
                    </div>
                </div>

                <hr>

                <div class="d-flex bd-highlight align-items-center">
                    <div class="bd-highlight me-auto">
                        <button type="submit" class="btn btn-success btn-lg">{{ $fsConfigs['publish_post_name'] }}</button>
                    </div>

                    <div class="bd-highlight d-flex flex-row">
                    </div>
                </div>
            </div>
            {{-- Form End --}}
        </form>
    </div>

    {{-- Group Modal --}}
    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $fsConfigs['group_name'] }}</h5>
                    <button type="button" class="btn-close" data-bs-target="#groupModal" data-bs-toggle="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Group List --}}
                    <div class="d-flex align-items-start">
                        <div class="nav flex-column nav-pills me-3" id="v-pills-post-box-tab" role="tablist" aria-orientation="vertical">
                            @if (! $fsConfigs['post_editor_group_required'])
                                <button type="button" id="post-box-not-select-group" class="btn btn-outline-secondary btn-sm mb-2 modal-close" data-bs-target="#groupModal" data-bs-toggle="modal" aria-label="Close">{{ $fsLang['editorNoGroup'] }}</button>
                            @endif

                            {{-- Group Categories --}}
                            @foreach($groupCategories as $groupCategory)
                                <button class="nav-link group-categories" data-page-size=15 data-page=1 data-action="{{ route('editor-workspace.work.groups', ['gid' => $groupCategory['gid']]) }}" id="v-pills-{{ $groupCategory['gid'] }}-post-box-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{ $groupCategory['gid'] }}-post-box" type="button" role="tab" aria-controls="v-pills-{{ $groupCategory['gid'] }}-post-box" aria-selected="false">
                                    @if ($groupCategory['cover'])
                                        <img src="{{ $groupCategory['cover'] }}" loading="lazy" height="20">
                                    @endif
                                    {{ $groupCategory['gname'] }}
                                </button>
                            @endforeach
                        </div>

                        <div class="tab-content" id="v-pills-post-box-tabContent" style="width:70%;">
                            {{-- Group --}}
                            @foreach($groupCategories as $groupCategory)
                                <div class="tab-pane fade" id="v-pills-{{ $groupCategory['gid'] }}-post-box" role="tabpanel" aria-labelledby="v-pills-{{ $groupCategory['gid'] }}-post-box-tab" tabindex="0">
                                    <div class="list-group"></div>
                                    <div class="list-group-addmore text-center my-3"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Group List --}}
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            document.getElementById('datetimeInput').max = (new Date()).toISOString().slice(0, 16);

            function validateDateTime() {
                const input = document.getElementById('datetimeInput');
                const selectedDateTime = new Date(input.value);
                const now = new Date();

                if (selectedDateTime > now) {
                    alert('Please select a time prior to the current time');
                    input.value = now.toISOString().slice(0, 16);
                }
            }

            function postBoxSelectGroup(obj) {
                var gid = $(obj).data('gid');
                var gname = $(obj).text();
                $('#createModal #post-box-group').text(gname);
                $("#createModal input[name='postGid']").val(gid);
            }

            function boxAjaxGetGroupList(action, pageSize = 15, page = 1){
                let html = '';

                $('#v-pills-post-box-tabContent .tab-pane .list-group').append('<div class="text-center mt-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

                $('#groupModal .tab-pane.fade.active.show .list-group-addmore').empty().append("{{ $fsLang['loading'] }}");

                $.get(action + "?page=" + page + "&pageSize=" + pageSize + "&uid={{ $headers['uid'] }}", function (data){
                    let lists = data.list
                    page = page + 1
                    if (lists.length > 0) {
                        $.each(lists, function (i, list){
                            html += '<a href="javascript:void(0)" data-gid="'+ list.gid +'" data-bs-target="#groupModal" data-bs-toggle="modal" onclick="postBoxSelectGroup(this)" class="list-group-item list-group-item-action';
                            if (list.publishRule.allowPost) {
                                html += '">';
                            } else {
                                html += ' disabled opacity-75">';
                            }
                            if (list.cover) {
                                html += '<img src="' + list.cover + '" height="20" class="me-1">';
                            }
                            html += list.gname + '</a>'
                        });
                    }

                    if (data.paginate.currentPage === 1){
                        $('#groupModal .list-group').each(function (){
                            $(this).empty();
                            $(this).next().empty();
                        });
                    }

                    $('#groupModal .tab-pane.fade.active.show .list-group').append(html);

                    $('#groupModal .tab-pane.fade.active.show .list-group-addmore').empty();
                    if (data.paginate.currentPage < data.paginate.lastPage) {
                        let addMoreHtml = `<a href="javascript:void(0)"  class="add-more" onclick="boxAjaxGetGroupList('${action}', ${pageSize}, ${page})">{{ $fsLang['clickToLoadMore'] }}</a>`;
                        $('#groupModal .tab-pane.fade.active.show .list-group-addmore').append(addMoreHtml);
                    }

                    $("#groupModal .group-categories").each(function (){
                        $(this).attr('disabled', false)
                    })
                })
            }

            $(function (){
                $("#groupModal .group-categories").on('click', function (){
                    let obj = $(this),
                        pageSize = obj.data('page-size'),
                        page = obj.data('page'),
                        action = obj.data('action')

                    $("#groupModal .group-categories").each(function (){
                        $(this).attr('disabled', true)
                    })

                    $('#groupModal .list-group').each(function (){
                        $(this).empty();
                        $(this).next().empty();
                    });
                    boxAjaxGetGroupList(action, pageSize, page)
                })

                $("#post-box-not-select-group").on('click', function (){
                    $('#createModal #post-box-group').text("{{ $fsLang['editorNoSelectGroup'] }}");
                    $("#createModal input[name='postGid']").val("");
                })
            });

            // show loading spinner while processing a form
            // https://getbootstrap.com/docs/5.1/components/spinners/
            $(document).on('submit', 'form', function () {
                var btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true);
                if (0 === btn.children('.spinner-border').length) {
                    btn.prepend(
                        '<span class="spinner-border spinner-border-sm mg-r-5 d-none" role="status" aria-hidden="true"></span> '
                    );
                }
                btn.children('.spinner-border').removeClass('d-none');
            });

            // submit
            $('.form-post-box').submit(function (e) {
                e.preventDefault();
                let form = $(this),
                    data = new FormData($(this)[0]),
                    btn = $(this).find('button[type="submit"]'),
                    actionUrl = $(this).attr('action');

                $.ajax({
                    type: 'POST',
                    url: actionUrl,
                    data: data, // serializes the form's elements.
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function (res) {
                        window.tips(res.message, res.code);
                        if (res.code != 0) {
                            return;
                        }

                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    complete: function (e) {
                        btn.prop('disabled', false);
                        btn.find('.spinner-border').remove();
                    },
                });
            });
        </script>
    @endpush
@endsection

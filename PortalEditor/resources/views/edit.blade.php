@extends('PortalEditor::fresns')

@section('content')
    <div class="mx-5 mt-3 mb-5">
        <div class="d-flex flex-row">
            <a class="btn btn-outline-secondary" href="{{ route('portal-editor.index') }}" role="button"><i class="bi bi-arrow-left"></i></a>
            <div class="ms-3 mt-1">
                <span class="badge text-bg-primary">{{ $name }}</span>
                <span class="badge rounded-pill text-bg-secondary ms-2">
                    {{ $lang['langName'] }}
                    @if ($lang['areaStatus'])
                        ({{ $lang['areaName'] }})
                    @endif
                </span>
            </div>
        </div>

        <div class="mt-4">
            <form action="{{ route('portal-editor.update', ['id' => $id, 'langTag' => $langTag]) }}" method="post">
                @csrf
                @method('put')
                <textarea name="content" id="setCodeContent" style="display: none;"></textarea>

                <div id="editor"></div>

                <!--button_save-->
                <div class="text-center pt-3 pb-5">
                    <button type="submit" id="save" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <textarea id="codeContent" style="display: none;">{{ $portal }}</textarea>
@endsection

@push('style')
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ace-builds@1.22.0/css/ace.min.css"> --}}
    <link href="/assets/plugins/PortalEditor/css/ace.min.css" rel="stylesheet">
    <style type="text/css" media="screen">
        #editor {
            width: 100%;
            height: 600px;
        }
    </style>
@endpush

@push('script')
    {{-- <script src="https://cdn.jsdelivr.net/npm/ace-builds@1.22.0/src-min-noconflict/ace.min.js" type="text/javascript" charset="utf-8"></script> --}}
    <script src="/assets/plugins/PortalEditor/js/ace.min.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/javascript");

        function decodeHtmlEntities(text) {
            var textarea = document.createElement('textarea');
            textarea.innerHTML = text;
            return textarea.value;
        }

        var codeContent = $('#codeContent').html();
        var decodedContent = decodeHtmlEntities(codeContent);

        editor.setValue(decodedContent);

        var form = document.querySelector("form");
        form.addEventListener("submit", function () {
            var setCodeContent = editor.getValue();
            document.getElementById("setCodeContent").value = setCodeContent;
        });
    </script>
@endpush

@extends('Cloudinary::layouts.master')

@section('content')
    @if ($fileCount >= $uploadConfig['uploadNumber'])
        <div class="alert alert-danger" role="alert">
            {{ $fileCountTip }}
        </div>
    @else
        <form class="mt-4 mb-2 mx-2" method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="type" value="{{ $fileType }}">
            <input type="hidden" name="platformId" value="{{ $headers['platformId'] }}">
            <input type="hidden" name="aid" value="{{ $headers['aid'] }}">
            <input type="hidden" name="uid" value="{{ $headers['uid'] }}">
            <input type="hidden" name="usageType" value="{{ $uploadInfo['usageType'] }}">
            <input type="hidden" name="tableName" value="{{ $uploadInfo['tableName'] }}">
            <input type="hidden" name="tableColumn" value="{{ $uploadInfo['tableColumn'] ?? 'id' }}">
            <input type="hidden" name="tableId" value="{{ $uploadInfo['tableId'] ?? null }}">
            <input type="hidden" name="tableKey" value="{{ $uploadInfo['tableKey'] ?? null }}">

            <div class="input-group">
                <input class="form-control" type="file" id="formFile" @if($uploadConfig['uploadNumber'] > 1) multiple="multiple" max="{{ $fileMax }}" @endif accept="{{ $uploadConfig['inputAccept'] }}" class="cloudinary-fileupload" data-cloudinary-field="image_id" data-form-data="[upload-preset-and-other-upload-options-as-html-escaped-JSON-data]">
                <button class="btn btn-outline-secondary ajax-progress-submit" type="button">{{ $fsLang['editorUploadButton'] }}</button>
            </div>
            <div class="ajax-progress progress mt-2"></div>
        </form>
    @endif

    <div class="mx-2 mt-3 text-secondary fs-7" id="extensions" data-value="{{ $uploadConfig['extensions'] }}">{{ $fsLang['editorUploadExtensions'] }}: {{ $uploadConfig['extensions'] }}</div>
    <div class="mx-2 mt-2 text-secondary fs-7" id="uploadMaxSize" data-value="{{ $uploadConfig['maxSize'] }}">{{ $fsLang['editorUploadMaxSize'] }}: {{ $uploadConfig['maxSize'] }} MB</div>
    @if ($uploadConfig['maxTime'] > 0)
        <div class="mx-2 mt-2 text-secondary fs-7" id="uploadMaxTime" data-value="{{ $uploadConfig['maxTime'] }}">{{ $fsLang['editorUploadMaxTime'] }}: {{ $uploadConfig['maxTime'] }} {{ $fsLang['unitSecond'] }}</div>
    @endif
    <div class="mx-2 my-2 text-secondary fs-7" id="uploadFileMax" data-value="{{ $fileMax }}">{{ $fsLang['editorUploadNumber'] }}: {{ $fileMax }}</div>
@endsection

@push('style')
    <style>
        .fs-7 {
            font-size: 0.9rem;
        }
    </style>
@endpush

@push('script')
    <script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>

    <script type="text/javascript">
        function getFileExtension(filename) {
            return filename.slice(((filename.lastIndexOf('.') - 1) >>> 0) + 2);
        }

        function getSignData() {
            var fileType = $('[name="type"]').val();
            var usageType = $('[name="usageType"]').val();

            return new Promise((resolve, reject) => {
                $.ajax({
                    method: 'get',
                    url: `{{ route('cloudinary.signdata') }}?type=${fileType}&usageType=${usageType}`,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        if (response.code != 0) {
                            window.tips(response.err_msg)
                        } else {
                            resolve(response.data)
                        }
                    },
                    error: function(error) {
                        console.error(error);
                        window.tips(error.responseJSON.message || error.responseJSON.err_msg || '{{ $fsLang['errorUnknown'] }}')
                        reject(error)
                    },
                });
            });
        }

        async function uploadFiles(event) {
            event.preventDefault();
            const signData = await getSignData();

            var files = $('#formFile')[0].files;
            var file;

            if (files.length) {
                $('.ajax-progress-submit').prop('disabled', true);
                $('.ajax-progress-submit').text('{{ $fsLang['editorUpload'] }}...');
                $('.ajax-progress-submit').prepend('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ');
            }

            for (let index = 0; index < files.length; index++) {
                file = files[index];

                uploadFile(file, signData);
            }
        }

        const fileInput = document.querySelector("input[type=file]");
        fileInput.addEventListener("change", function() {
            if (this.files.length > "{{ $fileMax }}") {
                alert("{{ $fsLang['editorUploadNumber'] }}: {{ $fileMax }}");
                this.value = "";
            }
        });

        // upload file to cloudinary
        function uploadFile(file, signData) {
            const url = "https://api.cloudinary.com/v1_1/" + signData.cloudname + "/auto/upload";

            const formData = new FormData();
            formData.append("file", file);
            formData.append("api_key", signData.apikey);
            formData.append("timestamp", signData.timestamp);
            formData.append("signature", signData.signature);
            formData.append("eager", signData.eager);
            formData.append("folder", signData.folder);

            $.ajax({
                method: 'post',
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);

                    if (response.error) {
                        alert(response.error.message)
                    }

                    saveFileinfo(response, file)
                },
                error: function(error) {
                    console.error(error);

                    if (error.responseJSON.error) {
                        alert(error.responseJSON.error.message)
                    }
                },
            });

        }

        // save file info to fresns
        function saveFileinfo(response, file) {
            var fileType = $('[name="type"]').val();
            var platformId = $('[name="platformId"]').val();
            var aid = $('[name="aid"]').val();
            var uid = $('[name="uid"]').val();
            var usageType = $('[name="usageType"]').val();
            var tableName = $('[name="tableName"]').val();
            var tableColumn = $('[name="tableColumn"]').val();
            var tableId = $('[name="tableId"]').val();
            var tableKey = $('[name="tableKey"]').val();
            var dir = response.folder;
            var uploadType = '';
            var extension = getFileExtension(file.name);
            var videoDuration = response.duration;
            var audioDuration = response.duration;
            var imageWidth = null;
            var imageHeight = null;
            var key = response.public_id + '.' + extension;
            switch (Number(usageType)) {
                case 1:
                    uploadType = 'image';
                    imageWidth = response.width;
                    imageHeight = response.height;
                    break
                case 2:
                    uploadType = 'video';
                case 3:
                    uploadType = 'audio';
                    break
                case 4:
                    uploadType = 'document';
                    break
            }

            var setting_extensions = $('#extensions').data('value');
            var setting_uploadMaxSize = $('#uploadMaxSize').data('value');
            var setting_uploadMaxTime = $('#uploadMaxTime').data('value');
            var setting_uploadFileMax = $('#uploadFileMax').data('value');
            if (setting_uploadMaxSize) {
                setting_uploadMaxSize = setting_uploadMaxSize + 1
            }
            if (setting_uploadMaxTime) {
                setting_uploadMaxTime = setting_uploadMaxTime + 1
            }

            // error file extension
            if (setting_extensions && setting_extensions.includes(extension) === false) {
                alert('{{ $fsError['fileType'] }}');
                return;
            }

            // error file size
            if (setting_uploadMaxSize && file.size > setting_uploadMaxSize * 1024 * 1024) {
                alert('{{ $fsError['fileSize'] }}');
                return;
            }

            var fileInfoItem = {
                name: file.name,
                mime: file.type,
                extension: extension,
                size: file.size, // Byte
                md5: null,
                sha: null,
                shaType: null,
                path: key,
                imageWidth: imageWidth,
                imageHeight: imageHeight,
                videoTime: videoDuration,
                videoCoverPath: null,
                videoGifPath: null,
                audioTime: audioDuration,
                transcodingState: 1,
                moreJson: null,
                originalPath: null,
                rating: null,
                remark: null,
            };

            console.log('fileInfoItem', fileInfoItem);

            // save file info
            $.ajax({
                url: '/api/cloudinary/upload-fileinfo',
                method: 'post',
                data: {
                    aid: aid,
                    uid: uid,
                    platformId: platformId,
                    usageType: usageType,
                    tableName: tableName,
                    tableColumn: tableColumn,
                    tableId: tableId,
                    tableKey: tableKey,
                    type: fileType,
                    fileInfo: [fileInfoItem],
                },
                success(res) {
                    if (res.code != 0) {
                        alert(res.message);
                        return;
                    }

                    const fresnsCallbackMessage = {
                        code: 0,
                        message: 'ok',
                        action: {
                            postMessageKey: '{{ $postMessageKey }}',
                            windowClose: true,
                            redirectUrl: '',
                            dataHandler: 'add'
                        },
                        data: res.data,
                    }

                    const messageString = JSON.stringify(fresnsCallbackMessage);
                    const userAgent = navigator.userAgent.toLowerCase();

                    switch (true) {
                        case (window.Android !== undefined):
                            // Android (addJavascriptInterface)
                            window.Android.receiveMessage(messageString);
                            break;

                        case (window.webkit && window.webkit.messageHandlers.iOSHandler !== undefined):
                            // iOS (WKScriptMessageHandler)
                            window.webkit.messageHandlers.iOSHandler.postMessage(messageString);
                            break;

                        case (window.FresnsJavascriptChannel !== undefined):
                            // Flutter
                            window.FresnsJavascriptChannel.postMessage(messageString);
                            break;

                        case (window.ReactNativeWebView !== undefined):
                            // React Native WebView
                            window.ReactNativeWebView.postMessage(messageString);
                            break;

                        case (userAgent.indexOf('miniprogram') > -1):
                            // WeChat Mini Program
                            wx.miniProgram.postMessage({ data: messageString });
                            wx.miniProgram.navigateBack();
                            break;

                        // Web
                        default:
                            parent.postMessage(messageString, '*');
                    }
                },
            });
        }

        $(document).ready(function () {
            $('.ajax-progress-submit').click(_.debounce(uploadFiles, 1500));
        });
    </script>
@endpush

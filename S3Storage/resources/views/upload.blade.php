@extends('S3Storage::layouts.master')

@section('content')
    <div>
        <form class="api-request-form" action="#" method="post" enctype="multipart/form-data">
            <input type="hidden" name="extensions" value="{{ $extensionNames }}">
            <input type="hidden" name="maxSize" value="{{ $maxSize }}">
            <input type="hidden" name="maxDuration" value="{{ $maxDuration }}">

            <div class="input-group mb-3">
                <input type="file" class="form-control" name="files" accept="{{ $inputAccept }}" @if($maxUploadNumber > 1) multiple="multiple" max="{{ $maxUploadNumber }}" @endif>
                <button class="btn btn-outline-secondary" type="submit" id="uploadSubmit">{{ $fsLang['uploadButton'] }}</button>
            </div>

            {{-- progress bar --}}
            <div class="progress d-none" id="progressbar" role="progressbar" aria-label="Animated striped example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
            </div>
        </form>

        <div class="form-text mt-3 text-break">{{ $fsLang['uploadTipExtensions'] }}: {{ $extensionNames }}</div>

        <div class="form-text mt-2">{{ $fsLang['uploadTipMaxSize'] }}: {{ $maxSize }} MB</div>

        @if ($fileType == 'video' || $fileType == 'audio')
            <div class="form-text mt-2">{{ $fsLang['uploadTipMaxDuration'] }}: {{ $maxDuration }} {{ $fsLang['unitSecond'] }}</div>
        @endif

        <div class="form-text mt-2">{{ $fsLang['uploadTipMaxNumber'] }}: {{ $maxUploadNumber }}</div>
    </div>
@endsection

@push('script')
    <script>
        var numberFiles = 0;
        var numberUploaded = 0;

        var progressInterval;
        var maxProgress = 50;

        // request
        $('.api-request-form').submit(function (e) {
            e.preventDefault();

            let form = $(this),
                files = form.find('input[name=files]')[0].files;

            if (!files.length) {
                alert("{{ $fsLang['uploadTip'] }}");
                return;
            }

            numberFiles = files.length;
            numberUploaded = 0;

            // progress bar initialization
            $('#progressbar').removeClass('d-none').attr('aria-valuenow', 0);
            $('#progressbar').find('.progress-bar').css('width', '0%').text('0%');
            clearInterval(progressInterval);

            // Adjusting progress bar increment dynamically
            progressInterval = setInterval(function () {
                var currentProgress = parseInt($('#progressbar').attr('aria-valuenow'));
                var increment = 0;

                if (currentProgress < maxProgress) {
                    // Faster increment rate before reaching maxProgress
                    increment = maxProgress - currentProgress > 10 ? 10 : maxProgress - currentProgress;
                } else if (currentProgress >= maxProgress && currentProgress < 100) {
                    // Slower increment rate after reaching maxProgress
                    increment = currentProgress < 98 ? 1 : 0.5; // even slower when approaching 100%
                }

                var newProgress = currentProgress + increment;
                newProgress = newProgress > 100 ? 100 : newProgress;

                $('#progressbar').attr('aria-valuenow', newProgress);
                $('#progressbar').find('.progress-bar').css('width', newProgress + '%').text(newProgress + '%');

                if (newProgress >= 100 || numberUploaded === numberFiles) {
                    clearInterval(progressInterval);
                }
            }, 500);

            // upload
            Array.from(files).forEach(file => {
                getFileData(file);
            });
        });

        // get file data
        function getFileData(file) {
            let fileType = "{{ $fileType }}";

            function proceedWithUpload(fileType, fileData) {
                console.log('fileData', fileType, fileData);

                if (!validateFile(fileData)) {
                    $('#uploadSubmit').prop('disabled', false);
                    $('#uploadSubmit').find('.spinner-border').remove();

                    $('#progressbar').addClass('d-none');
                    return;
                }

                makeUploadToken(fileData, file);
            }

            let fileData = {
                name: file.name,
                mime: file.type,
                extension: file.name.split('.').pop().toLowerCase(),
                size: file.size,
                width: null,
                height: null,
                duration: null,
                warning: 'none',
                sortOrder: null,
                moreInfo: null,
            };

            if (fileType == 'image') {
                const image = new Image();
                image.onload = function () {
                    fileData.width = this.naturalWidth;
                    fileData.height = this.naturalHeight;

                    // clean up memory
                    URL.revokeObjectURL(this.src);

                    // upload file
                    proceedWithUpload(fileType, fileData);
                };
                image.onerror = function () {
                    console.error('Error loading image');
                };
                image.src = URL.createObjectURL(file);

                return;
            }

            if (fileType == 'video' || fileType == 'audio') {
                const media = document.createElement(fileType);
                media.preload = 'metadata';
                media.onloadedmetadata = function () {
                    fileData.width = fileType == 'video' ? media.videoWidth : null;
                    fileData.height = fileType == 'video' ? media.videoHeight : null;
                    fileData.duration = Math.round(media.duration);

                    // clean up memory
                    URL.revokeObjectURL(this.src);

                    // upload file
                    proceedWithUpload(fileType, fileData);
                };
                media.onerror = function () {
                    console.error(`Error loading ${fileType}`);
                };
                media.src = URL.createObjectURL(file);

                return;
            }

            // upload file
            proceedWithUpload(fileType, fileData);
        };

        // validate file data
        function validateFile(fileData) {
            let extensions = $('input[name="extensions"]').val().split(','),
                maxSize = parseInt($('input[name="maxSize"]').val()),
                maxDuration = parseInt($('input[name="maxDuration"]').val());

            let fileName = fileData.name;
            let fileExtension = fileData.extension;
            let fileSize = fileData.size;
            let fileDuration = fileData.duration;
            let tipMessage;

            if (!extensions.includes(fileExtension)) {
                tipMessage = '[' + fileName + "] {{ $fsLang['uploadWarningExtension'] }}";
                tips(tipMessage, true);

                return false;
            }

            if (fileSize > maxSize * 1024 * 1024) {
                tipMessage = '[' + fileName + "] {{ $fsLang['uploadWarningMaxSize'] }}";
                tips(tipMessage, true);

                return false;
            }

            if (maxDuration && fileDuration > maxDuration) {
                tipMessage = '[' + fileName + "] {{ $fsLang['uploadWarningMaxDuration'] }}";
                tips(tipMessage, true);

                return false;
            }

            return true;
        };

        // make upload token
        function makeUploadToken(fileData, file) {
            $.ajax({
                url: "{{ route('s3-storage.api.upload-token') }}",
                type: 'POST',
                data: fileData,
                success: function (res) {
                    if (res.code != 0) {
                        tips(res.message, true);

                        $('#uploadSubmit').prop('disabled', false);
                        $('#uploadSubmit').find('.spinner-border').remove();

                        $('#progressbar').addClass('d-none');
                        return;
                    }

                    uploadFile(res.data, file);
                },
                error: function (e) {
                    tips(e.responseJSON.message, true);

                    $('#uploadSubmit').prop('disabled', false);
                    $('#uploadSubmit').find('.spinner-border').remove();

                    $('#progressbar').addClass('d-none');
                },
            });
        };

        // upload file
        function uploadFile(uploadToken, file) {
            let formData = new FormData();

            formData.append('file', file);

            $.ajax({
                url: uploadToken.url,
                type: uploadToken.method,
                headers: uploadToken.headers,
                data: formData,
                processData: false,
                contentType: false,
                enctype: 'multipart/form-data',
                success: function (res) {
                    numberUploaded++;

                    console.log('updateFileUploaded', uploadToken.fid, numberFiles, numberUploaded);

                    updateFileUploaded(uploadToken.fid);
                },
                error: function (e) {
                    tips(e.responseJSON.message, true);

                    $('#uploadSubmit').prop('disabled', false);
                    $('#uploadSubmit').find('.spinner-border').remove();

                    $('#progressbar').addClass('d-none');
                },
            });
        };

        // update file uploaded
        function updateFileUploaded(fid) {
            let lastUploaded = numberFiles == numberUploaded;

            $.ajax({
                url: "{{ route('s3-storage.api.uploaded') }}",
                type: 'patch',
                data: {
                    fid: fid,
                },
                success: function (res) {
                    if (res.code != 0) {
                        tips(res.message, true);
                        return;
                    }

                    // postMessage
                    let callbackAction = {
                        postMessageKey: '{{ $postMessageKey }}',
                        windowClose: lastUploaded,
                        redirectUrl: '',
                        dataHandler: 'add',
                    };

                    // /static/js/fresns-callback.js
                    FresnsCallback.send(callbackAction, res.data);
                },
                error: function (e) {
                    tips(e.responseJSON.message, true);
                },
                complete: function (e) {
                    if (lastUploaded) {
                        $('#uploadSubmit').prop('disabled', false);
                        $('#uploadSubmit').find('.spinner-border').remove();

                        $('#progressbar').attr('aria-valuenow', 100);
                        $('#progressbar').find('.progress-bar').css('width', '100%').text('100%');
                    }
                },
            });
        };
    </script>
@endpush

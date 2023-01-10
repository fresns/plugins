$.ajaxSetup({
    headers: {
        Accept: 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
});

// set timeout toast hide
const setTimeoutToastHide = () => {
    $('.toast.show').each((k, v) => {
        setTimeout(function () {
            $(v).hide();
        }, 1500);
    });
};

// tips
window.tips = function (message, code = 200) {
    let html = `<div aria-live="polite" aria-atomic="true" class="position-fixed top-50 start-50 translate-middle" style="z-index:9999">
          <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="toast-header">
                  <img src="/static/images/icon.png" width="20px" height="20px" class="rounded me-2" alt="Fresns">
                  <strong class="me-auto">Fresns</strong>
                  <small>${code}</small>
                  <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
              <div class="toast-body">${message}</div>
          </div>
      </div>`;
    $('div.fresns-tips').prepend(html);
    setTimeoutToastHide();
};

function getUploadToken() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '',
            method: 'get',
            success(res) {
                if (res.code != 0) {
                    reject('get upload token error ' + res.message);
                    return;
                }

                resolve(res.data.token);
            },
            error(err) {
                window.tips(error.responseJSON.message);
            },
        });
    });
}

// progress
// progress-bar 将会作为 "<div class="ajax-progress progress mt-2"></div>" 的子元素添加到尾部
// 使用时，需要在页面中增加代码 <div class="ajax-progress progress mt-2"></div>
// 并在页面的相关按钮增加 class 样式 ajax-progress-submit
// 在相关代码中调用:
// progressInit && progressInit();
// progressReset() && progressReset();
// progressDown() && progressDown();
// progressExit() && progressExit();
window.progress = {
    total: 100,
    valuenow: 0,
    speed: 1000,
    parentElement: null,
    stop: false,
    html: function () {
        return `<div class="progress-bar" role="progressbar" style="width: ${progress.valuenow}%" aria-valuenow="${progress.valuenow}" aria-valuemin="0" aria-valuemax="100">${progress.valuenow}</div>`;
    },
    setProgressElement: function (pe) {
        this.parentElement = pe;
        return this;
    },
    init: function () {
        this.total = 100;
        this.valuenow = 0;
        this.parentElement = null;
        this.stop = false;
        return this;
    },
    work: function () {
        this.add(progress);
    },
    add: function (obj) {
        var html = obj.html();

        if (obj.stop !== true && obj.valuenow < obj.total) {
            let num = parseFloat(obj.total) - parseFloat(obj.valuenow);
            obj.valuenow = (parseFloat(obj.valuenow) + parseFloat(num / 100)).toFixed(2);
            $(obj.parentElement).empty().append(html);
        } else {
            $(obj.parentElement).empty().append(html);
            return;
        }
        setTimeout(function () {
            obj.add(obj);
        }, obj.speed);
    },
    exit: function () {
        this.valuenow = 0;
        this.stop = true;
        return this;
    },
    done: function () {
        this.valuenow = this.total;
        return this;
    },
    clearHtml: function () {
        this.parentElement?.empty();
    },
};

function progressInit() {
    var progressObj = progress.init();
    var ele = $('.ajax-progress').removeClass('d-none');
    if (ele.length > 0) {
        progressObj.setProgressElement(ele[0]);
        progressObj.work();
    }
}

function progressReset() {
    $('.ajax-progress').empty();
    $('.ajax-progress-submit').show().removeAttr('disabled');
}

function progressDown() {
    progress.done();
}

function progressExit() {
    progress.exit();
}

$(document).ready(function () {
    $('.ajax-progress-submit').click(_.debounce(uploadFiles, 1500));
});

function uploadFiles(event) {
    event.preventDefault();

    var files = $('#formFile')[0].files;
    var file;

    for (let index = 0; index < files.length; index++) {
        file = files[index];

        uploadFile(file);
    }
}

function getFileExtension(filename) {
    return filename.slice(((filename.lastIndexOf('.') - 1) >>> 0) + 2);
}

/**
 * @see https://developer.qiniu.com/kodo/sdk/javascript
 */
function uploadFile(file) {
    // 清空 progress 进度
    progressReset();
    // 触发 progress 进度显示
    progressInit();

    var key, token, putExtra, config, observable, subscription;

    var form = $('#QiNiuForm');
    var fileType = $(form).find('input[name="type"]').val();
    var platformId = $(form).find('input[name="platformId"]').val();
    var aid = $(form).find('input[name="aid"]').val();
    var uid = $(form).find('input[name="uid"]').val();
    var usageType = $(form).find('input[name="usageType"]').val();
    var tableName = $(form).find('input[name="tableName"]').val();
    var tableColumn = $(form).find('input[name="tableColumn"]').val();
    var tableId = $(form).find('input[name="tableId"]').val();
    var tableKey = $(form).find('input[name="tableKey"]').val();
    var dir = $(form).find('input[name="dir"]').val();
    var uploadType = $(form).find('input[name="uploadType"]').val();
    var uploadToken = $(form).find('input[name="uploadToken"]').val();

    var uploadTypeStr = (uploadType == 'video' && '视频') || '音频';

    var setting_extensions = $('#extensions').data('value');
    var setting_uploadMaxSize = $('#uploadMaxSize').data('value');
    var setting_uploadMaxTime = $('#uploadMaxTime').data('value');
    var setting_uploadFileMax = $('#uploadFileMax').data('value');

    var extension = getFileExtension(file.name);

    // 不支持当前文件的后缀
    if (setting_extensions && setting_extensions.includes(extension) === false) {
        window.tips('文件类型不正确，上传失败');
        progressExit && progressExit();
        return;
    }

    // 文件过大，单位 MB
    if (setting_uploadMaxSize && file.size > setting_uploadMaxSize * 1024 * 1024) {
        window.tips('文件过大，上传失败');
        progressExit && progressExit();
        return;
    }

    var ele;
    var duration = null;
    var videoDuration = null;
    var audioDuration = null;
    var imageWidth = null;
    var imageHeight = null;
    if (uploadType == 'image') {
        ele = document.createElement('img');
        ele.src = URL.createObjectURL(file);
        ele.onloadedmetadata = function () {
            imageWidth = $(ele).width();
            imageHeight = $(ele).height();
            URL.revokeObjectURL(ele.src);
        };
    } else if (uploadType == 'video') {
        ele = document.createElement('video');
        ele.src = URL.createObjectURL(file);
        ele.onloadedmetadata = function () {
            videoDuration = ele.duration;
            duration = videoDuration;
            URL.revokeObjectURL(ele.src);
        };
    } else if (uploadType == 'audio') {
        ele = document.createElement('audio');
        ele.src = URL.createObjectURL(file);
        ele.onloadedmetadata = function () {
            audioDuration = ele.duration;
            duration = audioDuration;
            URL.revokeObjectURL(ele.src);
        };
    } else {
        //
    }

    var randomStr = Math.round(Math.random() * 100000000000);
    var currentTimestampMs = +new Date();
    key = dir + '/' + `${uploadType}-${randomStr}-${currentTimestampMs}` + '.' + extension;
    token = uploadToken;

    putExtra = {
        fname: file.name,
        mimeType: file.type,
        customVars: {
            'x:usageType': usageType,
            'x:tableName': tableName,
            'x:tableColumn': tableColumn,
            'x:tableId': tableId,
            'x:tableKey': tableKey,
            'x:dir': dir,
            'x:uploadToken': uploadToken,
        },
        metadata: {
            // 'x-qn-meta-xx': '',
        },
    };
    config = {
        useCdnDomain: false, // 是否使用 cdn 加速域名
        disableStatisticsReport: false, // 是否禁用日志报告
        upprotocol: (window.location.href.indexOf('https') == 0 && 'https') || 'http',
        retryCount: 3, // 上传自动重试次数（整体重试次数，而不是某个分片的重试次数）
        checkByMD5: true, // 是否开启 MD5 校验，为布尔值；在断点续传时，开启 MD5 校验会将已上传的分片与当前分片进行 MD5 值比对，若不一致，则重传该分片，避免使用错误的分片
        chunkSize: 4, // 分片上传时每片的大小，必须为正整数，单位为 MB，且最大不能超过 1024
    };

    var intervalId = setInterval(() => {
        // 获取音视频元数据需要等待，时长不确定，故使用定时器进行检测，每 500ms 判断一次是否加载完成

        // 不是音视频的时候，直接可以上传
        // 音视频的时长获取到之后，也可以进行上传，此时需要清理定时器
        if ((uploadType == 'video' || uploadType == 'audio') && setting_uploadMaxTime) {
            console.log(`${uploadTypeStr} duration: ${duration}s, uploadMaxTime: ${setting_uploadMaxTime}`);

            if (!duration) {
                console.log(`获取${uploadTypeStr}时长失败`);
                return;
            }

            if (duration > setting_uploadMaxTime) {
                clearInterval(intervalId);
                window.tips(`${uploadTypeStr}时长过长，上传失败`);
                progressExit && progressExit();
                return;
            }
        }

        clearInterval(intervalId);

        observable = qiniu.upload(file, key, token, putExtra, config);

        subscription = observable.subscribe({
            next(res) {
                console.log(res, 'next');
            },
            error(err) {
                console.error(err, 'error');
                progressExit && progressExit();
                window.tips(`${err.name}: ${err.message}`);
            },
            complete(res) {
                console.log(res, 'complete');
                progressDown && progressDown();

                var searchParams = new URLSearchParams(window.location.href);
                var urlConfig = {};

                try {
                    urlConfig = JSON.parse(window.atob(searchParams.get('config')));
                } catch (e) {}

                var fileInfoItem = {
                    name: file.name,
                    mime: file.type,
                    extension: extension,
                    size: file.size, // 单位 Byte
                    md5: null,
                    sha: res.hash,
                    shaType: 'hash',
                    path: res.key,
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

                // 上传到七牛
                $.ajax({
                    url: '/api/qiniu/upload-fileinfo',
                    method: 'post',
                    data: {
                        aid: searchParams.get('aid') || aid,
                        uid: searchParams.get('uid') || uid,
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
                            window.tips(res.message);
                            return;
                        }

                        var message;
                        parent.postMessage(
                            (message = {
                                postMessageKey: searchParams.get('postMessageKey'), // 路径中 postMessageKey 变量值
                                windowClose: true, // 是否关闭窗口或弹出层(modal)
                                variables: {
                                    // 路径中变量值原样返回
                                    type: searchParams.get('type'),
                                    scene: searchParams.get('scene'),
                                    aid: searchParams.get('aid') || aid,
                                    uid: searchParams.get('uid') || uid,
                                    rid: searchParams.get('rid'),
                                    gid: searchParams.get('gid'),
                                    pid: searchParams.get('pid'),
                                    cid: searchParams.get('cid'),
                                    eid: searchParams.get('eid'),
                                    fid: searchParams.get('fid'),
                                    plid: searchParams.get('plid'),
                                    clid: searchParams.get('clid'),
                                    uploadInfo: searchParams.get('uploadInfo'),
                                },
                                // 以下逻辑同 API 一致
                                code: 0, // 处理状态，0 表示，其余为失败状态码
                                message: 'ok', // 失败时的提示信息
                                data: res.data,
                            })
                        );

                        console.log('发送给父级的信息', message);
                    },
                });
            },
        });
    }, 500);
}

$(document).ready(function () {});

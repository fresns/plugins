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

    var ele;
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
            URL.revokeObjectURL(ele.src);
        };
    } else if (uploadType == 'audio') {
        ele = document.createElement('audio');
        ele.src = URL.createObjectURL(file);
        ele.onloadedmetadata = function () {
            audioDuration = ele.duration;
            URL.revokeObjectURL(ele.src);
        };
    }

    var randomStr = Math.round(Math.random() * 100000000000)
    var currentTimestampMs = +new Date()
    key = dir + '/' + `${uploadType}-${randomStr}-${currentTimestampMs}` + '.' + getFileExtension(file.name);
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

    observable = qiniu.upload(file, key, token, putExtra, config);

    subscription = observable.subscribe({
        next(res) {
            console.log(res, 'next');
        },
        error(err) {
            console.error(err, 'error');
            window.tips(`${err.name}: ${err.message}`);
        },
        complete(res) {
            console.log(res, 'complete');

            var searchParams = new URLSearchParams(window.location.href);
            var urlConfig = {};

            try {
                urlConfig = JSON.parse(window.atob(searchParams.get('config')));
            } catch (e) {}

            var fileInfoItem = {
                name: file.name,
                mime: file.type,
                extension: getFileExtension(file.name),
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
}

$(document).ready(function () {});

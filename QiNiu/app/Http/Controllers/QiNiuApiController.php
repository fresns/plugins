<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Http\Controllers;

use App\Fresns\Api\Traits\ApiResponseTrait;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\File;
use App\Models\PluginCallback;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Plugins\QiNiu\Http\Requests\UploadFileInfoDTO;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class QiNiuApiController extends Controller
{
    use ApiResponseTrait;
    use QiNiuStorageTrait;

    public function callback(string $ulid)
    {
        // 接收到七牛请求, 进行请求记录
        \info('接收到七牛请求 '.$ulid);
        \info(var_export(\request()->all(), true));

        $data = \request()->all();

        $pluginCallback = PluginCallback::query()->where('ulid', $ulid)->first();
        \info('plugin_callback', [
            $pluginCallback?->toArray(),
        ]);
        if (! $pluginCallback) {
            return $this->failure(3e4, '未找到 callback 信息 '.$ulid);
        }

        $fileInfo = $pluginCallback->content['file'] ?? [];

        switch ($pluginCallback->content['sence']) {
            case 'transcoding':
                $file = File::where('fid', $fileInfo['fid'])->first();

                // 失败
                if ($data['code'] == 3) {
                    $file->update([
                        'transcoding_state' => File::TRANSCODING_STATE_FAILURE,
                        'transcoding_reason' => $data['items'][0]['error'] ?? null,
                    ]);
                    break;
                }

                // 成功
                if ($data['code'] == 0) {
                    $file->update([
                        'original_path' => $file->path,
                    ]);

                    $diskPath = $pluginCallback->content['save_path'];

                    /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter $adapter */
                    $adapter = $this->setType($file->type)->getAdapter();
                    [$stat, $error] = $adapter->getBucketManager()->stat($this->getBucketName(), $diskPath);

                    $meta = [];
                    if (! $error) {
                        $meta = array_merge([
                            'mime' => $stat['mimeType'],
                            'extension' => pathinfo($diskPath, PATHINFO_EXTENSION),
                            'size' => $stat['fsize'],
                            'md5' => $stat['md5'],
                            'sha' => $stat['hash'],
                            'sha_type' => 'hash',
                        ]);
                    }

                    $videoScreenshot = ConfigHelper::fresnsConfigByItemKey('video_screenshot');

                    // unit: seconds @see https://developer.qiniu.com/dora/1313/video-frame-thumbnails-vframe
                    $videoCoverPath = $diskPath.'?'.$videoScreenshot;
                    if (empty($videoScreenshot)) {
                        info('视频封面图生成失败，未配置 video_screenshot 转码设置');

                        // 保留原来的
                        $videoCoverPath = $file->video_cover_path;
                    }

                    $file->update(array_merge([
                        'path' => $diskPath,
                        'video_cover_path' => $videoCoverPath,
                        'transcoding_state' => File::TRANSCODING_STATE_DONE,
                    ], $meta));

                    CacheHelper::forgetFresnsFileUsage($file->id);
                    break;
                }
            break;
        }

        $pluginCallback->update([
            'is_use' => PluginCallback::IS_USE_TRUE,
        ]);

        return $this->success(null, '操作 '.$ulid);
    }

    public function uploadFileInfo(Request $request)
    {
        $dtoRequest = new UploadFileInfoDTO($request->all());

        $bodyInfo = [
            'platformId' => $dtoRequest->platformId,
            'usageType' => $dtoRequest->usageType,
            'tableName' => $dtoRequest->tableName,
            'tableColumn' => $dtoRequest->tableColumn,
            'tableId' => $dtoRequest->tableId ?? null,
            'tableKey' => $dtoRequest->tableKey ?? null,
            'aid' => $dtoRequest->aid,
            'uid' => $dtoRequest->uid,
            'type' => (int) $dtoRequest->type,
            'fileInfo' => $dtoRequest->fileInfo,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->uploadFileInfo($bodyInfo);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        return $this->success($fresnsResp->getData());
    }
}

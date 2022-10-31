<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Http\Controllers;

use Plugins\QiNiu\Http\Requests\UploadFileInfoDTO;
use App\Fresns\Api\Traits\ApiResponseTrait;
use App\Utilities\FileUtility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class QiNiuApiController extends Controller
{
    use ApiResponseTrait;
    use QiNiuStorageTrait;

    public function callback(string $uuid)
    {
        // 接收到七牛请求, 进行请求记录
        \info('接收到七牛请求 '.$uuid);
        \info(var_export(\request()->all(), true));

        $data = \request()->all();

        $pluginCallback = \App\Models\PluginCallback::query()->where('uuid', $uuid)->first();
        \info('plugin_callback', [
            $pluginCallback?->toArray(),
        ]);
        if (! $pluginCallback) {
            return $this->failure(3e4, '未找到 callback 信息 '.$uuid);
        }

        $fileInfo = $pluginCallback->content['file'] ?? [];

        switch ($pluginCallback->content['sence']) {
            case 'upload_file':
                if ($data['code'] == 3) {
                    return $this->failure(3e4, '转码失败 '.$uuid);
                }

                if ($data['code'] == 0 && $fileInfo['type'] == \App\Models\File::TYPE_VIDEO) {
                    // 保存视频截图
                    \App\Models\File::where('fid', $fileInfo['fid'])->update([
                        // 'video_cover' => $pluginCallback->content['save_path'],
                        'video_cover_path' => $pluginCallback->content['save_path'],
                    ]);
                }
            break;
            case 'transcoding':
                $file = \App\Models\File::where('fid', $fileInfo['fid'])->first();

                // 失败
                if ($data['code'] == 3) {
                    $file->update([
                        'transcoding_state' => \App\Models\File::TRANSCODING_STATE_FAILURE,
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

                    $file->update(array_merge([
                        'path' => $diskPath,
                        'transcoding_state' => \App\Models\File::TRANSCODING_STATE_DONE,
                    ], $meta));
                    break;
                }
            break;
        }

        $pluginCallback->update([
            'is_use' => \App\Models\PluginCallback::IS_USE_TRUE,
        ]);

        return $this->success(null, '操作 '.$uuid);
    }

    public function uploadFileInfo(Request $request)
    {
        $dtoRequest = new UploadFileInfoDTO($request->all());

        $bodyInfo = [
            'aid' => $dtoRequest->aid,
            'uid' => $dtoRequest->uid,
            'platformId' => $dtoRequest->platformId,
            'usageType' => $dtoRequest->usageType,
            'tableName' => $dtoRequest->tableName,
            'tableColumn' => $dtoRequest->tableColumn,
            'tableId' => $dtoRequest->tableId ?? null,
            'tableKey' => $dtoRequest->tableKey ?? null,
            'type' => (int) $dtoRequest->type,
            'fileInfo' => $dtoRequest->fileInfo,
        ];

        $data = FileUtility::uploadFileInfo($bodyInfo);

        if (!$data) {
            return $this->failure();
        }

        return $this->success($data);
    }
}
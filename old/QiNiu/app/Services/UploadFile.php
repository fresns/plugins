<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Models\File;
use App\Models\PluginCallback;
use App\Utilities\FileUtility;
use Fresns\DTO\DTO;
use Illuminate\Support\Str;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class UploadFile extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'platformId' => ['integer', 'required'],
            'useType' => ['integer', 'required'],
            'tableName' => ['string', 'required'],
            'tableColumn' => ['string', 'required'],
            'tableId' => ['integer', 'nullable'],
            'tableKey' => ['string', 'nullable'],
            'aid' => ['string', 'nullable'],
            'uid' => ['integer', 'nullable'],
            'type' => ['integer', 'required'],
            'file' => ['file', 'required'],
            'moreJson' => ['json', 'nullable'],
        ];
    }

    public function process()
    {
        $storage = $this->getStorage();

        if (is_null($storage)) {
            return null;
        }

        // 获取要保存的目录
        $dir = FileHelper::fresnsFileStoragePath($this->getType(), $this->useType);

        // 将上传的文件保存到指定的目录下
        $diskPath = $storage->putFile($dir, $this->file);

        /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter $adapter */
        $adapter = $storage->getAdapter();
        // dd($adapter->getBucketManager()->stat($this->getBucketName(), 'videos/systems/202206/0oMj2HQyTHDJ8jUQXX8a9wIR3EBbJnVldPEmqLYh.jpg'));
        [$stat, $error] = $adapter->getBucketManager()->stat($this->getBucketName(), $diskPath);

        $bodyInfo = [
            'platformId' => $this->platformId,
            'useType' => $this->useType,
            'tableName' => $this->tableName,
            'tableColumn' => $this->tableColumn,
            'tableId' => $this->tableId,
            'tableKey' => $this->tableKey,
            'aid' => $this->aid ?: null,
            'uid' => $this->uid ?: null,
            'type' => $this->type,
            'moreJson' => $this->moreJson ?: null,
            'md5' => $stat['md5'] ?? null,
        ];

        $uploadFileInfo = FileUtility::saveFileInfoToDatabase($bodyInfo, $diskPath, $this->file);

        if (is_null($error) && $this->getType() === File::TYPE_VIDEO) {
            $uuid = Str::uuid()->toString();

            $result = $this->generateVideoCover($storage->getAdapter(), $dir, $diskPath, $uuid);

            $pluginCallback = $this->savePluginCallback($result, $uploadFileInfo, $uuid);
            \info(var_export($pluginCallback->toArray(), 1));
        }

        @unlink($this->file->path());

        return $uploadFileInfo;
    }

    public function generateVideoCover($storage, $dir, $diskPath, $uuid)
    {
        $result = $this->executeTranscoding(
            auth: $storage->getAuthManager(),
            transParams: $this->getVideoScreenshot(),
            bucket: $this->getBucketName(),
            dir: $dir,
            key: $diskPath,
            filename: pathinfo($diskPath, PATHINFO_FILENAME).'.jpg',
            notifyUrl: route('qiniu.transcoding.callback', ['uuid' => $uuid]),
        );

        return $result;
    }

    public function savePluginCallback(array $result, array $uploadFileInfo, $uuid)
    {
        return PluginCallback::create([
            'plugin_unikey' => 'QiNiu',
            'uuid' => $uuid,
            'types' => PluginCallback::TYPE_CUSTOMIZE,
            'content' => [
                'sence' => 'upload_file',
                'pipline_id' => $result['id'],
                'save_path' => $result['path'],
                'file' => $uploadFileInfo,
            ],
            'is_use' => PluginCallback::IS_USE_FALSE,
            'use_plugin_unikey' => 'QiNiu',
        ]);
    }

    public function getVideoScreenshot()
    {
        return ConfigHelper::fresnsConfigByItemKey('video_screenshot');
    }
}

<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use App\Utilities\FileUtility;
use Fresns\DTO\DTO;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class UploadFile extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'platformId' => ['integer', 'required'],
            'usageType' => ['integer', 'required'],
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
        $dir = FileHelper::fresnsFileStoragePath($this->getType(), $this->usageType);

        // 将上传的文件保存到指定的目录下
        $diskPath = $storage->putFile($dir, $this->file);

        /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter $adapter */
        $adapter = $storage->getAdapter();
        // dd($adapter->getBucketManager()->stat($this->getBucketName(), 'videos/systems/202206/0oMj2HQyTHDJ8jUQXX8a9wIR3EBbJnVldPEmqLYh.jpg'));
        [$stat, $error] = $adapter->getBucketManager()->stat($this->getBucketName(), $diskPath);

        $bodyInfo = [
            'platformId' => $this->platformId,
            'usageType' => $this->usageType,
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
            $file = File::whereFid($uploadFileInfo['fid'])->first();

            $uploadFileInfo['videoCoverUrl'] = $this->generateVideoCover($file);
        }

        @unlink($this->file->path());

        return $uploadFileInfo;
    }

    public function generateVideoCover(File $file)
    {
        $videoScreenshot = ConfigHelper::fresnsConfigByItemKey('video_screenshot');
        if (empty($videoScreenshot)) {
            info('视频封面图生成失败，未配置 video_screenshot 转码设置');

            return;
        }

        // unit: seconds @see https://developer.qiniu.com/dora/1313/video-frame-thumbnails-vframe
        $videoCoverPath = $file->path.'?'.$videoScreenshot;

        $file->update([
            'video_cover_path' => $videoCoverPath,
        ]);

        $videoConfig = ConfigHelper::fresnsConfigByItemKey('video_bucket_domain');

        $videoCoverUrl = StrHelper::qualifyUrl($videoCoverPath, $videoConfig);

        return $videoCoverUrl;
    }
}

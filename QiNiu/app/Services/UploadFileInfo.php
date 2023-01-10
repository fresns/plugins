<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Helpers\ConfigHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use App\Utilities\FileUtility;
use Fresns\DTO\DTO;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class UploadFileInfo extends DTO
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
            'fileInfo' => ['array', 'required'],
        ];
    }

    public function process()
    {
        $this->resetQiNiuConfig();

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
            'fileInfo' => $this->fileInfo,
        ];

        $uploadFileInfo = FileUtility::uploadFileInfo($bodyInfo);

        $newFileInfo = [];
        foreach ($uploadFileInfo as $fileInfo) {
            if ($fileInfo['type'] == File::TYPE_VIDEO) {
                $fileModel = File::where('fid', $fileInfo['fid'])->first();

                $fileInfo['videoCoverUrl'] = $this->generateVideoCover($fileModel);
            }

            $newFileInfo[] = $fileInfo;
        }

        return $newFileInfo;
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

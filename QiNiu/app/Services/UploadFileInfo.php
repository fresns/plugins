<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

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

        $uploadFileInfos = FileUtility::uploadFileInfo($bodyInfo);

        $data = [];
        foreach ($uploadFileInfos as $uploadFileInfo) {
            if ($uploadFileInfo->type == File::TYPE_VIDEO) {
                $this->generateVideoCover($uploadFileInfo);
            }

            $data[] = $uploadFileInfo->getFileInfo();
        }

        return $data;
    }

    public function generateVideoCover(File $file)
    {
        $videoCover = $file->path.'?vframe/jpg/offset/1'; // unit: seconds @see https://developer.qiniu.com/dora/1313/video-frame-thumbnails-vframe

        $file->update([
            'video_cover_path' => $videoCover,
        ]);
        // dd($videoCover);
    }
}

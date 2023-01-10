<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Helpers\CacheHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use Fresns\DTO\DTO;
use Illuminate\Validation\Rule;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class AntiLinkFileInfo extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', Rule::in(array_keys(File::TYPE_MAP))],
            'fileIdOrFid' => ['string', 'required'],
        ];
    }

    public function getAntiLinkFileInfo()
    {
        /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter $storage */
        $storage = $this->getAdapter();

        if (is_null($storage)) {
            return null;
        }

        if (! $this->isEnableAntiLink()) {
            return null;
        }

        $cacheKey = 'qiniu_file_antilink_'.$this->fileIdOrFid;
        $cacheTags = ['fresnsPlugins', 'pluginQiNiu'];

        $fileInfo = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($fileInfo)) {
            $file = $this->getFile();
            if (is_null($file)) {
                return null;
            }

            $fileInfo = $file->getFileInfo();

            $deadline = $this->getDeadline();

            $keys = [
                'imageDefaultUrl', 'imageConfigUrl', 'imageAvatarUrl', 'imageRatioUrl', 'imageSquareUrl', 'imageBigUrl',
                'videoCoverUrl', 'videoGifUrl', 'videoUrl',
                'audioUrl',
                'documentUrl', 'documentPreviewUrl',
            ];

            foreach ($keys as $key) {
                if (! empty($fileInfo[$key])) {
                    $fileInfo[$key] = $this->getAntiLinkUrl($fileInfo[$key], $deadline);
                }
            }

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType($this->getType());

            CacheHelper::put($fileInfo, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $fileInfo;
    }

    public function getFile()
    {
        if (StrHelper::isPureInt($this->fileIdOrFid)) {
            $file = File::where('id', $this->fileIdOrFid)->first();
        } else {
            $file = File::where('fid', $this->fileIdOrFid)->first();
        }

        return $file;
    }
}

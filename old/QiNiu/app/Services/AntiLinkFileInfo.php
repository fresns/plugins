<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

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
        $cacheExpireAt = now()->addSeconds($this->getExpireSeconds() - 60);

        // 缓存
        $data = cache()->remember($cacheKey, $cacheExpireAt, function () {
            $file = $this->getFile();
            if (is_null($file)) {
                return null;
            }

            $fileInfo = $file->getFileInfo();

            $antiLinkKey = $this->getAntiLinkKey();
            $deadline = $this->getDeadline();

            $keys = [
                'imageDefaultUrl', 'imageConfigUrl', 'imageAvatarUrl', 'imageRatioUrl', 'imageSquareUrl', 'imageBigUrl',
                'videoCoverUrl', 'videoGifUrl', 'videoUrl',
                'audioUrl',
                'documentUrl', 'documentPreviewUrl',
            ];

            foreach ($keys as $key) {
                if (! empty($fileInfo[$key])) {
                    $fileInfo[$key] = $this->getAntiLinkUrl($fileInfo[$key], $antiLinkKey, $deadline);
                }
            }

            return $fileInfo;
        });

        if (is_null($data)) {
            cache()->forget($cacheKey);
        }

        return $data;
    }

    public function getFile()
    {
        return File::where('id', $this->fileIdOrFid)->orWhere('fid', $this->fileIdOrFid)->first();
    }
}

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

class AntiLinkFileOriginalUrl extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', Rule::in(array_keys(File::TYPE_MAP))],
            'fileIdOrFid' => ['string', 'required'],
        ];
    }

    public function getAntiLinkFileOriginalUrl()
    {
        /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter $storage */
        $storage = $this->getAdapter();

        if (is_null($storage)) {
            return null;
        }

        if (! $this->isEnableAntiLink()) {
            return null;
        }

        $antiLinkFileInfo = new AntiLinkFileInfo([
            'type' => $this->type,
            'fileIdOrFid' => $this->fileIdOrFid,
        ]);

        /** @var File $file */
        $file = $antiLinkFileInfo->getFile();

        $originalPath = $file->original_path;

        if (! $originalPath) {
            $originalPath = $file->path;
        }

        $url = sprintf('%s/%s', rtrim($this->getBucketDomain(), '/'), ltrim($originalPath));

        return [
            'originalUrl' => $this->getAntiLinkUrl($url, $this->getAntiLinkKey(), $this->getDeadline()),
        ];
    }
}

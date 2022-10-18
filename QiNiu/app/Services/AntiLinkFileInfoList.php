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

class AntiLinkFileInfoList extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', Rule::in(array_keys(File::TYPE_MAP))],
            'fileIdsOrFids' => ['array', 'required'],
        ];
    }

    public function getAntiLinkFileInfoList()
    {
        $data = [];
        foreach ($this->fileIdsOrFids as $fileIdOrFid) {
            $antiLinkFileInfo = new AntiLinkFileInfo([
                'type' => $this->type,
                'fileIdOrFid' => $fileIdOrFid,
            ]);

            $data[] = $antiLinkFileInfo->getAntiLinkFileInfo();
        }

        return $data;
    }
}

<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Utilities\FileUtility;
use Fresns\DTO\DTO;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class LogicalDeletionFiles extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'fileIdsOrFids' => ['array', 'required'],
        ];
    }

    public function delete()
    {
        FileUtility::logicalDeletionFiles($this->fileIdsOrFids);

        return true;
    }
}

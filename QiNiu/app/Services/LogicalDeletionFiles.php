<?php

namespace Plugins\QiNiu\Services;

use Fresns\DTO\DTO;
use App\Utilities\FileUtility;
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
<?php

namespace Plugins\QiNiu\Services;

use Fresns\DTO\DTO;
use App\Models\File;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;
use Illuminate\Validation\Rule;

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
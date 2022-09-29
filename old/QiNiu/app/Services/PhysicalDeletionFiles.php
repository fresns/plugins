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

class PhysicalDeletionFiles extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', Rule::in(array_keys(File::TYPE_MAP))],
            'fileIdsOrFids' => ['array', 'required'],
        ];
    }

    public function delete()
    {
        /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter */
        $storage = $this->getAdapter();
        if (is_null($storage)) {
            return null;
        }

        $files = File::whereIn('id', $this->fileIdsOrFids)->orWhereIn('fid', $this->fileIdsOrFids)->get();

        foreach ($files as $file) {
            $storage->delete($file->path);
            $file->delete();

            // 删除 防盗链 缓存
            cache()->forget('qiniu_file_antilink_'.$file->id);
            cache()->forget('qiniu_file_antilink_'.$file->fid);
        }

        return true;
    }
}

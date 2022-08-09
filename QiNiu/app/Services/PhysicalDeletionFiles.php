<?php

namespace Plugins\QiNiu\Services;

use Fresns\DTO\DTO;
use App\Models\File;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;
use Illuminate\Validation\Rule;

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
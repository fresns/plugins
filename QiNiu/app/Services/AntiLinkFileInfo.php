<?php

namespace Plugins\QiNiu\Services;

use Fresns\DTO\DTO;
use App\Models\File;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;
use Illuminate\Validation\Rule;

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

        if (!$this->isEnableAntiLink()) {
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
                if (!empty($fileInfo[$key])) {
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
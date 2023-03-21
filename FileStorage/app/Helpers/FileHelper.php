<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Helpers;

use App\Helpers\CacheHelper as FresnsCacheHelper;
use App\Helpers\FileHelper as FresnsFileHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\StrHelper;
use App\Models\File;

class FileHelper
{
    // get anti link token
    public static function token(string $fid, ?int $time = null, ?string $type = null): string
    {
        $time = $time ?: time();

        $key = config('app.key').$fid.$time.$type;

        return md5($key);
    }

    // get anti link url
    public static function url(string $fileIdOrFid, ?string $type = null): string
    {
        if (StrHelper::isPureInt($fileIdOrFid)) {
            $file = PrimaryHelper::fresnsModelById('file', $fileIdOrFid);
            $fid = $file?->fid;
        } else {
            $fid = $fileIdOrFid;
        }

        if (empty($fid)) {
            return null;
        }

        $urlType = match ($type) {
            'imageConfigUrl' => 'config',
            'imageRatioUrl' => 'ratio',
            'imageSquareUrl' => 'square',
            'imageBigUrl' => 'big',
            'videoPosterUrl' => 'poster',
            'documentPreviewUrl' => 'preview',
            'originalUrl' => 'original',
            default => null,
        };

        $fileType = match ($type) {
            'videoPosterUrl' => File::TYPE_VIDEO,
            'videoUrl' => File::TYPE_VIDEO,
            'audioUrl' => File::TYPE_AUDIO,
            'documentPreviewUrl' => File::TYPE_DOCUMENT,
            default => File::TYPE_IMAGE,
        };

        $config = FresnsFileHelper::fresnsFileStorageConfigByType($fileType);

        $time = now()->addMinutes($config['antiLinkExpire'])->timestamp;
        $token = FileHelper::token($fid, $time, $urlType);

        $path = "/api/file-storage/file?fid={$fid}&token={$token}&time={$time}&type={$urlType}";

        return StrHelper::qualifyUrl($path, $config['bucketDomain']);
    }

    // get file info
    public static function info(string $fileIdOrFid): int|array
    {
        $cacheKey = 'fresns_file_storage_antilink_'.$fileIdOrFid;
        $cacheTags = ['fresnsPlugins', 'pluginFileStorage'];

        $fileInfo = FresnsCacheHelper::get($cacheKey, $cacheTags);
        if (empty($fileInfo)) {
            if (StrHelper::isPureInt($fileIdOrFid)) {
                $file = File::where('id', $fileIdOrFid)->first();
            } else {
                $file = File::where('fid', $fileIdOrFid)->first();
            }

            if (empty($file) || ! $file?->is_enable) {
                return null;
            }

            $fileInfo = $file->getFileInfo();

            $keys = [
                'imageConfigUrl', 'imageRatioUrl', 'imageSquareUrl', 'imageBigUrl',
                'videoPosterUrl', 'videoUrl',
                'audioUrl',
                'documentPreviewUrl',
            ];

            foreach ($keys as $key) {
                if ($key == 'documentPreviewUrl') {
                    $documentUrl = $file->getFileUrl();

                    $antiLinkUrl = FileHelper::url($fileInfo['fid'], $key);

                    $fileInfo[$key] = FresnsFileHelper::fresnsFileDocumentPreviewUrl($antiLinkUrl, $file->fid, $file->extension);

                    continue;
                }

                if (empty($fileInfo[$key])) {
                    continue;
                }

                $fileInfo[$key] = FileHelper::url($fileInfo['fid'], $key);
            }

            $cacheTime = FresnsCacheHelper::fresnsCacheTimeByFileType($file->type);
            FresnsCacheHelper::put($fileInfo, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $fileInfo;
    }
}

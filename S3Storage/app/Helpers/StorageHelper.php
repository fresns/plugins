<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\S3Storage\Helpers;

use App\Helpers\CacheHelper;
use App\Helpers\FileHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    // get disk config
    public static function disk(int $fileType): array
    {
        $configs = FileHelper::fresnsFileStorageConfigByType($fileType);

        return [
            'driver' => 's3',
            'key' => $configs['secretId'],
            'secret' => $configs['secretKey'],
            'region' => $configs['bucketRegion'],
            'bucket' => $configs['bucketName'],
            'url' => $configs['bucketDomain'],
            'endpoint' => $configs['antiLinkKey'] ?? $configs['bucketDomain'],
            'use_path_style_endpoint' => false,
            'throw' => false,
        ];
    }

    // get anti link url
    public static function url(?File $file, ?string $type = null): ?string
    {
        if (empty($file)) {
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

        $config = FileHelper::fresnsFileStorageConfigByType($file->type);
        $diskConfig = StorageHelper::disk($file->type);

        $url = Storage::build($diskConfig)->temporaryUrl($file->path, now()->addMinutes($config['antiLinkExpire'] ?? 10));

        return $url;
    }

    // get file info
    public static function info(string $fileIdOrFid): int|array
    {
        $cacheKey = 'fresns_s3_storage_antilink_'.$fileIdOrFid;
        $cacheTags = ['fresnsPlugins', 'pluginS3Storage'];

        $fileInfo = CacheHelper::get($cacheKey, $cacheTags);
        if (empty($fileInfo)) {
            if (StrHelper::isPureInt($fileIdOrFid)) {
                $file = File::where('id', $fileIdOrFid)->first();
            } else {
                $file = File::where('fid', $fileIdOrFid)->first();
            }

            if (empty($file) || ! $file?->is_enabled) {
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

                    $antiLinkUrl = StorageHelper::url($file, $key);

                    $fileInfo[$key] = FileHelper::fresnsFileDocumentPreviewUrl($antiLinkUrl, $file->fid, $file->extension);

                    continue;
                }

                if (empty($fileInfo[$key])) {
                    continue;
                }

                $fileInfo[$key] = StorageHelper::url($file, $key);
            }

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType($file->type);
            CacheHelper::put($fileInfo, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $fileInfo;
    }
}

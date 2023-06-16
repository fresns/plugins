<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\S3Storage\Helpers;

use App\Helpers\CacheHelper;
use App\Helpers\FileHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class ConfigHelper
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
            'endpoint' => $configs['antiLinkKey'],
            'use_path_style_endpoint' => false,
            'throw' => false,
        ];
    }

    // get anti link url
    public static function url(string $fileIdOrFid, ?string $type = null): string
    {
        if (StrHelper::isPureInt($fileIdOrFid)) {
            $file = PrimaryHelper::fresnsModelById('file', $fileIdOrFid);
        } else {
            $file = PrimaryHelper::fresnsModelByFsid('file', $fileIdOrFid);
        }

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

        $fileType = match ($type) {
            'videoPosterUrl' => File::TYPE_VIDEO,
            'videoUrl' => File::TYPE_VIDEO,
            'audioUrl' => File::TYPE_AUDIO,
            'documentPreviewUrl' => File::TYPE_DOCUMENT,
            default => File::TYPE_IMAGE,
        };

        $config = FileHelper::fresnsFileStorageConfigByType($fileType);
        $diskConfig = ConfigHelper::disk($fileType);

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

                    $antiLinkUrl = ConfigHelper::url($fileInfo['fid'], $key);

                    $fileInfo[$key] = FileHelper::fresnsFileDocumentPreviewUrl($antiLinkUrl, $file->fid, $file->extension);

                    continue;
                }

                if (empty($fileInfo[$key])) {
                    continue;
                }

                $fileInfo[$key] = ConfigHelper::url($fileInfo['fid'], $key);
            }

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType($file->type);
            CacheHelper::put($fileInfo, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $fileInfo;
    }
}

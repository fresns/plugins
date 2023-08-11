<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\Cloudinary\Helpers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;

class StorageHelper
{
    // config
    // https://github.com/cloudinary-devs/cloudinary-laravel/
    public static function config(int $type): void
    {
        $config = FileHelper::fresnsFileStorageConfigByType($type);

        $cloudUrl = "cloudinary://{$config['secretId']}:{$config['secretKey']}@{$config['bucketName']}";

        config([
            'cloudinary.notification_url' => null,
            'cloudinary.cloud_url' => $cloudUrl,
            'cloudinary.upload_preset' => null,
        ]);
    }

    // get public id
    public static function getPublicId(string $path, string $extension): string
    {
        $string = Str::of($path)->rtrim('.'.$extension);

        return $string;
    }

    // get anti link url
    public static function url(File $file, ?string $urlType = null): ?string
    {
        StorageHelper::config($file->type);

        $publicId = StorageHelper::getPublicId($file->path, $file->extension);

        $expireMinutes = match ($file->type) {
            File::TYPE_IMAGE => ConfigHelper::fresnsConfigByItemKey('image_url_expire'),
            File::TYPE_VIDEO => ConfigHelper::fresnsConfigByItemKey('video_url_expire'),
            File::TYPE_AUDIO => ConfigHelper::fresnsConfigByItemKey('audio_url_expire'),
            File::TYPE_DOCUMENT => ConfigHelper::fresnsConfigByItemKey('document_url_expire'),
            default => ConfigHelper::fresnsConfigByItemKey('image_url_expire'),
        };
        $tenMinutesLater = time() + $expireMinutes * 60;

        $resourceType = match ($file->type) {
            File::TYPE_IMAGE => 'image',
            File::TYPE_VIDEO => 'video',
            File::TYPE_AUDIO => 'video',
            default => 'raw',
        };

        $url = Cloudinary::uploadApi()->privateDownloadUrl($publicId, $file->extension, [
            'resource_type' => $resourceType,
            'expires_at' => $tenMinutesLater,
        ]);

        return $url;
    }

    // get file info
    public static function info(string $fileIdOrFid): int|array
    {
        $cacheKey = 'fresns_cloudinary_antilink_'.$fileIdOrFid;
        $cacheTags = ['fresnsPlugins', 'pluginCloudinary'];

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

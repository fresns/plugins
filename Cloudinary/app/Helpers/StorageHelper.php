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
    // build disk
    public static function buildDisk(int $fileType): void
    {
        $config = FileHelper::fresnsFileStorageConfigByType($fileType);

        $cloudUrl = "cloudinary://{$config['secretId']}:{$config['secretKey']}@{$config['bucketName']}";

        config([
            'cloudinary.notification_url' => null,
            'cloudinary.cloud_url' => $cloudUrl,
            'cloudinary.upload_preset' => null,
        ]);
    }

    // get cloudinary public id
    public static function getPublicId(string $path, string $extension): string
    {
        $string = Str::of($path)->rtrim('.'.$extension);

        return $string;
    }

    // get temporary url
    public static function fileUrl(?File $file, ?string $type = null): ?string
    {
        if (empty($file)) {
            return null;
        }

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($file->type);
        $temporaryUrlKey = $storageConfig['temporaryUrlKey'];
        $temporaryUrlExpiration = $storageConfig['temporaryUrlExpiration'] ?? 30;

        StorageHelper::buildDisk($file->type);

        $publicId = StorageHelper::getPublicId($file->path, $file->extension);

        $resourceType = match ($file->type) {
            File::TYPE_IMAGE => 'image',
            File::TYPE_VIDEO => 'video',
            File::TYPE_AUDIO => 'video',
            default => 'raw',
        };

        $tenMinutesLater = time() + $temporaryUrlExpiration * 60;

        // https://cloudinary.com/documentation/image_upload_api_reference#generate_archive_optional_parameters
        $fileUrl = null;
        switch ($type) {
            case 'imageConfigUrl':
                $configs = ConfigHelper::fresnsConfigByItemKeys([
                    'image_handle_position',
                    'image_thumb_config',
                ]);

                $fileUrl = Cloudinary::uploadApi()->privateDownloadUrl($publicId, $file->extension, [
                    'resource_type' => $resourceType,
                    'transformations' => $configs['image_thumb_config'],
                    'expires_at' => $tenMinutesLater,
                ]);
                break;

            case 'imageRatioUrl':
                $configs = ConfigHelper::fresnsConfigByItemKeys([
                    'image_handle_position',
                    'image_thumb_ratio',
                ]);

                $fileUrl = Cloudinary::uploadApi()->privateDownloadUrl($publicId, $file->extension, [
                    'resource_type' => $resourceType,
                    'transformations' => $configs['image_thumb_ratio'],
                    'expires_at' => $tenMinutesLater,
                ]);
                break;

            case 'imageSquareUrl':
                $configs = ConfigHelper::fresnsConfigByItemKeys([
                    'image_handle_position',
                    'image_thumb_square',
                ]);

                $fileUrl = Cloudinary::uploadApi()->privateDownloadUrl($publicId, $file->extension, [
                    'resource_type' => $resourceType,
                    'transformations' => $configs['image_thumb_square'],
                    'expires_at' => $tenMinutesLater,
                ]);
                break;

            case 'imageBigUrl':
                $configs = ConfigHelper::fresnsConfigByItemKeys([
                    'image_handle_position',
                    'image_thumb_big',
                ]);

                $fileUrl = Cloudinary::uploadApi()->privateDownloadUrl($publicId, $file->extension, [
                    'resource_type' => $resourceType,
                    'transformations' => $configs['image_thumb_big'],
                    'expires_at' => $tenMinutesLater,
                ]);
                break;

            case 'videoUrl':
                $configs = ConfigHelper::fresnsConfigByItemKeys([
                    'video_transcode_handle_position',
                    'video_transcode_parameter',
                ]);

                $fileUrl = Cloudinary::uploadApi()->privateDownloadUrl($publicId, $file->extension, [
                    'resource_type' => $resourceType,
                    'transformations' => $configs['video_transcode_parameter'],
                    'expires_at' => $tenMinutesLater,
                ]);
                break;

            case 'videoPosterUrl':
                $configs = ConfigHelper::fresnsConfigByItemKeys([
                    'video_poster_handle_position',
                    'video_poster_parameter',
                ]);
                // $videoPosterPath = $file->video_poster_path ?: $publicId.'.jpg';
                // $fileUrl = StrHelper::qualifyUrl($videoPosterPath, $storageConfig['accessDomain']);

                $fileUrl = Cloudinary::uploadApi()->privateDownloadUrl($publicId, 'jpg', [
                    'resource_type' => 'image',
                    'transformations' => $configs['video_poster_parameter'],
                    'expires_at' => $tenMinutesLater,
                ]);
                break;

            case 'audioUrl':
                $configs = ConfigHelper::fresnsConfigByItemKeys([
                    'audio_transcode_handle_position',
                    'audio_transcode_parameter',
                ]);

                $fileUrl = Cloudinary::uploadApi()->privateDownloadUrl($publicId, $file->extension, [
                    'resource_type' => $resourceType,
                    'transformations' => $configs['audio_transcode_parameter'],
                    'expires_at' => $tenMinutesLater,
                ]);
                break;

            case 'documentPreviewUrl':
                $fileUrl = FileHelper::fresnsFileDocumentPreviewUrl($file->extension);
                break;

            case 'originalUrl':
                $fileUrl = Cloudinary::uploadApi()->privateDownloadUrl($publicId, $file->extension, [
                    'resource_type' => $resourceType,
                    'expires_at' => $tenMinutesLater,
                ]);
                break;
        }

        return $fileUrl;
    }

    // get file info
    public static function fileInfo(string $fileIdOrFid): int|array
    {
        $cacheKey = 'fresns_file_info_'.$fileIdOrFid;
        $cacheTag = 'fresnsFiles';

        $fileInfo = CacheHelper::get($cacheKey, $cacheTag);
        if (empty($fileInfo)) {
            if (StrHelper::isPureInt($fileIdOrFid)) {
                $fileModel = File::where('id', $fileIdOrFid)->first();
            } else {
                $fileModel = File::where('fid', $fileIdOrFid)->first();
            }

            if (! $fileModel || ! $fileModel?->is_enabled) {
                return null;
            }

            $fileInfo = $fileModel->getFileInfo();

            // temporary url
            $configs = FileHelper::fresnsFileStorageConfigByType($fileModel->type);
            if ($configs['temporaryUrlStatus']) {
                $urlKeys = [
                    'imageConfigUrl', 'imageRatioUrl', 'imageSquareUrl', 'imageBigUrl',
                    'videoUrl', 'videoPosterUrl',
                    'audioUrl',
                    'documentPreviewUrl',
                ];

                foreach ($urlKeys as $key) {
                    if (! array_key_exists($key, $fileInfo)) {
                        continue;
                    }

                    $fileInfo[$key] = StorageHelper::fileUrl($fileModel, $key);
                }
            }

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType($fileModel->type, null, 2);
            CacheHelper::put($fileInfo, $cacheKey, $cacheTag, $cacheTime);
        }

        return $fileInfo;
    }
}

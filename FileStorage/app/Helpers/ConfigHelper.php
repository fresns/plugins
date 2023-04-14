<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Helpers;

use App\Helpers\CacheHelper as FresnsCacheHelper;
use App\Helpers\ConfigHelper as FresnsConfigHelper;
use App\Models\File;

class ConfigHelper
{
    // file config keys
    public static function configKeys(int $type): array
    {
        $fileTypeName = match ($type) {
            File::TYPE_IMAGE => 'image',
            File::TYPE_VIDEO => 'video',
            File::TYPE_AUDIO => 'audio',
            File::TYPE_DOCUMENT => 'document',
        };

        $configKeys = [
            "filestorage_{$fileTypeName}_driver",
            "filestorage_{$fileTypeName}_private_key",
            "filestorage_{$fileTypeName}_passphrase",
            "filestorage_{$fileTypeName}_host_fingerprint",
            "{$fileTypeName}_secret_id",
            "{$fileTypeName}_secret_key",
            "{$fileTypeName}_bucket_name",
            "{$fileTypeName}_bucket_area",
        ];

        return $configKeys;
    }

    // image file config keys
    public static function imageConfigKeys(): array
    {
        return [
            'filestorage_image_driver',
            'filestorage_image_processing_status',
            'filestorage_image_processing_library',
            'filestorage_image_processing_params',
            'filestorage_image_watermark_file',
            'filestorage_image_watermark_config',
        ];
    }

    // get cache key
    public static function cacheKey(array $itemKeys): string
    {
        $key = reset($itemKeys).'_'.end($itemKeys).'_'.count($itemKeys);

        $cacheKey = "fresns_config_keys_{$key}";

        return $cacheKey;
    }

    // forget cache
    public static function forgetCache(int $fileType): bool
    {
        $cacheTags = ['fresnsPlugins', 'pluginFileStorage'];

        // config
        $configKeys = ConfigHelper::configKeys($fileType);
        FresnsCacheHelper::forgetFresnsMultilingual(ConfigHelper::cacheKey($configKeys), $cacheTags);
        foreach ($configKeys as $key) {
            FresnsCacheHelper::forgetFresnsConfigs($key);
        }

        // image processing config
        if ($fileType == File::TYPE_IMAGE) {
            $imageConfigKeys = ConfigHelper::imageConfigKeys();
            FresnsCacheHelper::forgetFresnsMultilingual(ConfigHelper::cacheKey($imageConfigKeys), $cacheTags);
            foreach ($imageConfigKeys as $key) {
                FresnsCacheHelper::forgetFresnsConfigs($key);
            }
        }

        return true;
    }

    // get disk config
    public static function disk(int $fileType): array
    {
        $configKeys = ConfigHelper::configKeys($fileType);

        $configs = FresnsConfigHelper::fresnsConfigByItemKeys($configKeys);

        $fileTypeName = match ($fileType) {
            File::TYPE_IMAGE => 'image',
            File::TYPE_VIDEO => 'video',
            File::TYPE_AUDIO => 'audio',
            File::TYPE_DOCUMENT => 'document',
        };

        $configArr = [
            'driver' => $configs["filestorage_{$fileTypeName}_driver"],
            'privateKey' => $configs["filestorage_{$fileTypeName}_private_key"],
            'passphrase' => $configs["filestorage_{$fileTypeName}_passphrase"],
            'hostFingerprint' => $configs["filestorage_{$fileTypeName}_host_fingerprint"],
            'username' => $configs["{$fileTypeName}_secret_id"],
            'password' => $configs["{$fileTypeName}_secret_key"],
            'host' => $configs["{$fileTypeName}_bucket_name"],
            'port' => $configs["{$fileTypeName}_bucket_area"],
        ];

        $diskConfig = match ($configArr['driver']) {
            default =>  config('filesystems.disks.public'),
            'local' => config('filesystems.disks.public'),
            'ftp' => [
                'driver' => 'ftp',
                'host' => $configArr['host'],
                'port' => (int) ($configArr['port'] ?? 21),
                'username' => $configArr['username'],
                'password' => $configArr['password'],

                // Optional FTP Settings...
                // 'root' => env('FTP_ROOT'),
                // 'passive' => true,
                // 'ssl' => true,
                // 'timeout' => 30,
            ],
            'sftp' => [
                'driver' => 'sftp',
                'host' => $configArr['host'],
                'port' => (int) ($configArr['port'] ?? 22),

                // Settings for basic authentication...
                'username' => $configArr['username'],
                'password' => $configArr['password'],

                // Settings for SSH key based authentication with encryption password...
                'privateKey' => $configArr['privateKey'],
                'passphrase' => $configArr['passphrase'],

                'hostFingerprint' => $configArr['hostFingerprint'],

                // Optional SFTP Settings...
                // 'maxTries' => 4,
                // 'root' => env('SFTP_ROOT', ''),
                // 'timeout' => 30,
                // 'useAgent' => true,
            ],
        };

        return $diskConfig;
    }
}

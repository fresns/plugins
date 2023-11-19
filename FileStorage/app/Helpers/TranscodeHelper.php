<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Helpers;

use App\Helpers\ConfigHelper as FresnsConfigHelper;
use App\Helpers\FileHelper as FresnsFileHelper;
use App\Models\File as FileModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class TranscodeHelper
{
    // image processing
    public static function imageProcessing(UploadedFile $file, array $fileInfo)
    {
        if (! $file) {
            return;
        }

        $configs = FresnsConfigHelper::fresnsConfigByItemKeys(ConfigHelper::imageConfigKeys());

        if ($configs['filestorage_image_processing_status'] == 'close') {
            return;
        }

        $params = [
            'config' => $configs['filestorage_image_processing_params']['config'] ?? 400,
            'ratio' => $configs['filestorage_image_processing_params']['ratio'] ?? 400,
            'square' => $configs['filestorage_image_processing_params']['square'] ?? 200,
            'big' => $configs['filestorage_image_processing_params']['big'] ?? 1500,
            'watermarkStatus' => $configs['filestorage_image_watermark_config']['status'] ?? 'close',
            'watermarkPosition' => $configs['filestorage_image_watermark_config']['position'] ?? 'center',
        ];

        $localStorage = Storage::build(config('filesystems.disks.local'));
        $publicStorage = Storage::build(config('filesystems.disks.public'));
        $fileModel = FileModel::where('fid', $fileInfo['fid'])->first();

        // file path
        $filePath = $publicStorage->path($fileModel->path);
        $tempFilePath = null;
        if ($configs['filestorage_image_driver'] != 'local') {
            $tempFilePath = $localStorage->putFile('temporary/images/original', $file);

            $filePath = $localStorage->path($tempFilePath);
        }

        // image manager
        $imageDriver = $configs['filestorage_image_processing_library'] ?? 'gd';
        $manager = new ImageManager($imageDriver);

        // image read
        try {
            $config = $manager->read($filePath)->scaleDown(width: $params['config'])->sharpen(8);
            $ratio = $manager->read($filePath)->scaleDown(width: $params['ratio'])->sharpen(8);
            $square = $manager->read($filePath)->resizeDown($params['square'], $params['square'])->sharpen(8);
            $big = $manager->read($filePath)->scaleDown(width: $params['big'])->sharpen(8);
        } catch (\Exception $e) {
            return;
        }

        // image watermark
        if ($params['watermarkStatus'] == 'open' && $configs['filestorage_image_watermark_file']) {
            $watermarkFile = FileModel::where('id', $configs['filestorage_image_watermark_file'])->first();

            if ($watermarkFile?->path) {
                $watermarkFilePath = $publicStorage->path($watermarkFile->path);

                if (file_exists($watermarkFilePath)) {
                    $imageWidth = $fileModel->image_width / 2;
                    $imageHeight = $fileModel->image_height / 2;

                    if ($watermarkFile->image_width < $imageWidth || $watermarkFile->image_height < $imageHeight) {
                        $big->place($watermarkFilePath, $params['watermarkPosition']);
                    }
                }
            }
        }

        // file path
        $newFilePath = FresnsFileHelper::fresnsFilePathForImage('name-end', $fileModel->path);
        $storagePath = [
            'configPath' => storage_path('app/public/'.$newFilePath['configPath']),
            'ratioPath' => storage_path('app/public/'.$newFilePath['ratioPath']),
            'squarePath' => storage_path('app/public/'.$newFilePath['squarePath']),
            'bigPath' => storage_path('app/public/'.$newFilePath['bigPath']),
        ];

        // save format
        $format = match ($fileModel->extension) {
            'png' => 'toPng',
            'webp' => 'toWebp',
            'gif' => 'toGif',
            default => 'toJpeg',
        };

        // save local
        if ($configs['filestorage_image_driver'] == 'local') {
            $config->$format()->save($storagePath['configPath']);
            $ratio->$format()->save($storagePath['ratioPath']);
            $square->$format()->save($storagePath['squarePath']);
            $big->$format()->save($storagePath['bigPath']);

            return true;
        }

        // save remote temporary file
        $temporaryPath = [
            'configPath' => storage_path('app/temporary/'.$newFilePath['configPath']),
            'ratioPath' => storage_path('app/temporary/'.$newFilePath['ratioPath']),
            'squarePath' => storage_path('app/temporary/'.$newFilePath['squarePath']),
            'bigPath' => storage_path('app/temporary/'.$newFilePath['bigPath']),
        ];
        $dir = dirname($temporaryPath['configPath']);
        if (! is_dir($dir)) {
            File::makeDirectory($dir, 0775, true);
        }
        $config->$format()->save($temporaryPath['configPath']);
        $ratio->$format()->save($temporaryPath['ratioPath']);
        $square->$format()->save($temporaryPath['squarePath']);
        $big->$format()->save($temporaryPath['bigPath']);

        // save to remote
        $diskConfig = ConfigHelper::disk(FileModel::TYPE_IMAGE);
        $fileDirectory = dirname($fileModel->path);

        $storage = Storage::build($diskConfig);

        $storage->putFileAs($fileDirectory, $temporaryPath['configPath'], basename($temporaryPath['configPath']));
        $storage->putFileAs($fileDirectory, $temporaryPath['ratioPath'], basename($temporaryPath['ratioPath']));
        $storage->putFileAs($fileDirectory, $temporaryPath['squarePath'], basename($temporaryPath['squarePath']));
        $storage->putFileAs($fileDirectory, $temporaryPath['bigPath'], basename($temporaryPath['bigPath']));

        // delete temporary file
        $imagePathArr = array_filter([
            $tempFilePath,
            'temporary/'.$newFilePath['configPath'],
            'temporary/'.$newFilePath['ratioPath'],
            'temporary/'.$newFilePath['squarePath'],
            'temporary/'.$newFilePath['bigPath'],
        ]);
        $localStorage->delete($imagePathArr);
    }
}

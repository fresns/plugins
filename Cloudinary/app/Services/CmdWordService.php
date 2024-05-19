<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\Cloudinary\Services;

use App\Fresns\Words\File\DTO\GetTemporaryUrlFileInfoDTO;
use App\Fresns\Words\File\DTO\GetTemporaryUrlFileInfoListDTO;
use App\Fresns\Words\File\DTO\GetTemporaryUrlOfOriginalFileDTO;
use App\Fresns\Words\File\DTO\LogicalDeletionFilesDTO;
use App\Fresns\Words\File\DTO\PhysicalDeletionFilesDTO;
use App\Fresns\Words\File\DTO\UploadFileDTO;
use App\Helpers\CacheHelper;
use App\Helpers\FileHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use App\Models\FileUsage;
use App\Utilities\ConfigUtility;
use App\Utilities\FileUtility;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Plugins\Cloudinary\Helpers\StorageHelper;

class CmdWordService
{
    use CmdWordResponseTrait;

    // getUploadToken
    public function getUploadToken($wordBody)
    {
        /**
         * S3 upload are not supported by the storage provider.
         */
        return $this->failure(32104, ConfigUtility::getCodeMessage(32104));
    }

    // uploadFile
    public function uploadFile($wordBody)
    {
        $dtoWordBody = new UploadFileDTO($wordBody);

        StorageHelper::buildDisk($dtoWordBody->type);
        $storePath = FileHelper::fresnsFileStoragePath($dtoWordBody->type, $dtoWordBody->usageType);

        $cloudinaryTag = match ($dtoWordBody->usageType) {
            FileUsage::TYPE_OTHER => 'others',
            FileUsage::TYPE_SYSTEM => 'systems',
            FileUsage::TYPE_STICKER => 'stickers',
            FileUsage::TYPE_USER => 'users',
            FileUsage::TYPE_CONVERSATION => 'conversations',
            FileUsage::TYPE_POST => 'posts',
            FileUsage::TYPE_COMMENT => 'comments',
            FileUsage::TYPE_EXTEND => 'extends',
            FileUsage::TYPE_App => 'apps',
            default => 'others',
        };

        $config = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        // https://cloudinary.com/documentation/image_upload_api_reference#upload_optional_parameters
        $options = [
            'type' => $config['bucketRegion'] ?? 'upload', // upload, private and authenticated
            'folder' => $storePath,
            'tags' => ['fresns', $cloudinaryTag],
        ];

        switch ($dtoWordBody->type) {
            case File::TYPE_IMAGE:
                $result = Cloudinary::upload($dtoWordBody->file->getRealPath(), $options);
                break;

            case File::TYPE_VIDEO:
                $result = Cloudinary::uploadVideo($dtoWordBody->file->getRealPath(), $options);
                break;

            case File::TYPE_AUDIO:
                $result = Cloudinary::uploadVideo($dtoWordBody->file->getRealPath(), $options);
                break;

            default:
                $result = Cloudinary::uploadFile($dtoWordBody->file->getRealPath(), $options);
                break;
        }

        $publicId = $result->getPublicId();

        $extension = $dtoWordBody->file->extension();
        $path = $publicId.'.'.$extension;

        $width = null;
        $height = null;
        $duration = null;
        $videoPosterPath = null;
        $transcodingState = File::TRANSCODING_STATE_WAIT;

        if ($dtoWordBody->type == File::TYPE_IMAGE || $dtoWordBody->type == File::TYPE_VIDEO) {
            $width = $result->getResponse()['width'];
            $height = $result->getResponse()['height'];
        }

        if ($dtoWordBody->type == File::TYPE_VIDEO) {
            $duration = $result->getResponse()['duration'];
            $videoPosterPath = $publicId.'.jpg';
            $transcodingState = File::TRANSCODING_STATE_DONE;
        }

        if ($dtoWordBody->type == File::TYPE_AUDIO) {
            $duration = $result->getResponse()['duration'];
            $transcodingState = File::TRANSCODING_STATE_DONE;
        }

        $sha256Hash = hash_file('sha256', $dtoWordBody->file->path());

        $fileInfo = [
            'type' => $dtoWordBody->type,
            'width' => $width,
            'height' => $height,
            'duration' => $duration,
            'sha' => $sha256Hash,
            'shaType' => 'sha256',
            'warningType' => $dtoWordBody->warningType,
            'path' => $path,
            'transcodingState' => $transcodingState,
            'videoPosterPath' => $videoPosterPath,
            'originalPath' => null,
            'uploaded' => true,
        ];

        $usageInfo = [
            'usageType' => $dtoWordBody->usageType,
            'platformId' => $dtoWordBody->platformId,
            'tableName' => $dtoWordBody->tableName,
            'tableColumn' => $dtoWordBody->tableColumn,
            'tableId' => $dtoWordBody->tableId,
            'tableKey' => $dtoWordBody->tableKey,
            'moreInfo' => $dtoWordBody->moreInfo,
            'aid' => $dtoWordBody->aid,
            'uid' => $dtoWordBody->uid,
            'remark' => null,
        ];

        $fileModel = FileUtility::uploadFileInfo($dtoWordBody->file, $fileInfo, $usageInfo);

        $apiFileInfo = FileHelper::fresnsFileInfoById($fileModel->fid, $usageInfo);

        if (empty($apiFileInfo)) {
            return $this->failure(32104, ConfigUtility::getCodeMessage(32104));
        }

        return $this->success($apiFileInfo);
    }

    // getTemporaryUrlFileInfo
    public function getTemporaryUrlFileInfo($wordBody)
    {
        $dtoWordBody = new GetTemporaryUrlFileInfoDTO($wordBody);

        $fileInfo = StorageHelper::fileInfo($dtoWordBody->fileIdOrFid);

        return $this->success($fileInfo);
    }

    // getTemporaryUrlFileInfoList
    public function getTemporaryUrlFileInfoList($wordBody)
    {
        $dtoWordBody = new GetTemporaryUrlFileInfoListDTO($wordBody);

        $data = [];
        foreach ($dtoWordBody->fileIdsOrFids as $id) {
            $data[] = StorageHelper::fileInfo($id);
        }

        return $this->success($data);
    }

    // getTemporaryUrlOfOriginalFile
    public function getTemporaryUrlOfOriginalFile($wordBody)
    {
        $dtoWordBody = new GetTemporaryUrlOfOriginalFileDTO($wordBody);

        if (StrHelper::isPureInt($dtoWordBody->fileIdOrFid)) {
            $file = PrimaryHelper::fresnsModelById('file', $dtoWordBody->fileIdOrFid);
        } else {
            $file = PrimaryHelper::fresnsModelByFsid('file', $dtoWordBody->fileIdOrFid);
        }

        $originalUrl = StorageHelper::fileUrl($file, 'originalUrl');

        return $this->success([
            'originalUrl' => $originalUrl,
        ]);
    }

    // logicalDeletionFiles
    public function logicalDeletionFiles($wordBody)
    {
        $dtoWordBody = new LogicalDeletionFilesDTO($wordBody);

        FileUtility::logicalDeletionFiles($dtoWordBody->fileIdsOrFids);

        return $this->success();
    }

    // physicalDeletionFiles
    public function physicalDeletionFiles($wordBody)
    {
        $dtoWordBody = new PhysicalDeletionFilesDTO($wordBody);

        // Storage disk
        StorageHelper::buildDisk($dtoWordBody->type);

        foreach ($dtoWordBody->fileIdsOrFids as $id) {
            if (StrHelper::isPureInt($id)) {
                $file = File::where('id', $id)->first();
            } else {
                $file = File::where('fid', $id)->first();
            }

            if (empty($file)) {
                continue;
            }

            FileUsage::where('file_id', $file->id)->delete();

            $publicId = StorageHelper::getPublicId($file->path, $file->extension);
            $resourceType = match ($file->type) {
                File::TYPE_IMAGE => 'image',
                File::TYPE_VIDEO => 'video',
                File::TYPE_AUDIO => 'video',
                default => 'raw',
            };

            $deleteStatus = Cloudinary::destroy($publicId, [
                'resource_type' => $resourceType,
            ]);

            if (! $deleteStatus) {
                return $this->failure(21006);
            }

            if ($file->type == File::TYPE_VIDEO && $file->video_poster_path) {
                $posterPublicId = StorageHelper::getPublicId($file->video_poster_path, 'jpg');

                Cloudinary::destroy($posterPublicId, [
                    'resource_type' => $resourceType,
                ]);
            }

            if ($file->original_path) {
                $originalPublicId = StorageHelper::getPublicId($file->original_path, $file->extension);

                Cloudinary::destroy($originalPublicId, [
                    'resource_type' => $resourceType,
                ]);
            }

            $file->update([
                'physical_deletion' => true,
            ]);

            $file->delete();

            // forget cache
            CacheHelper::clearDataCache('file', $file->fid);
        }

        return $this->success();
    }
}

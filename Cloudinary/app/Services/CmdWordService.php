<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\Cloudinary\Services;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
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
use Plugins\Cloudinary\Services\DTO\GetAntiLinkFileInfoDTO;
use Plugins\Cloudinary\Services\DTO\GetAntiLinkFileInfoListDTO;
use Plugins\Cloudinary\Services\DTO\GetAntiLinkFileOriginalUrlDTO;
use Plugins\Cloudinary\Services\DTO\GetUploadTokenDTO;
use Plugins\Cloudinary\Services\DTO\LogicalDeletionFilesDTO;
use Plugins\Cloudinary\Services\DTO\PhysicalDeletionFilesDTO;
use Plugins\Cloudinary\Services\DTO\UploadFileDTO;
use Plugins\Cloudinary\Services\DTO\UploadFileInfoDTO;

class CmdWordService
{
    use CmdWordResponseTrait;

    // getUploadToken
    public function getUploadToken($wordBody)
    {
        $dtoWordBody = new GetUploadTokenDTO($wordBody);

        $data = [
            'storageId' => File::STORAGE_CLOUDINARY,
            'token' => null,
            'expireTime' => null,
        ];

        return $this->success($data);
    }

    // uploadFile
    public function uploadFile($wordBody)
    {
        $dtoWordBody = new UploadFileDTO($wordBody);

        // config
        StorageHelper::config($dtoWordBody->type);

        // upload
        $folder = FileHelper::fresnsFileStoragePath($dtoWordBody->type, $dtoWordBody->usageType);
        $usageTag = match ($dtoWordBody->usageType) {
            FileUsage::TYPE_OTHER => 'others',
            FileUsage::TYPE_SYSTEM => 'systems',
            FileUsage::TYPE_OPERATION => 'operations',
            FileUsage::TYPE_STICKER => 'stickers',
            FileUsage::TYPE_USER => 'users',
            FileUsage::TYPE_CONVERSATION => 'conversations',
            FileUsage::TYPE_POST => 'posts',
            FileUsage::TYPE_COMMENT => 'comments',
            FileUsage::TYPE_EXTEND => 'extends',
            FileUsage::TYPE_PLUGIN => 'plugins',
            default => 'others',
        };

        // https://cloudinary.com/documentation/image_upload_api_reference#upload_optional_parameters
        $config = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);
        $options = [
            'type' => $config['bucketRegion'] ?? 'upload', // upload, private and authenticated
            'folder' => $folder,
            'tags' => ['fresns', $usageTag],
        ];

        switch ($dtoWordBody->type) {
            case File::TYPE_IMAGE:
                // image
                $result = Cloudinary::upload($dtoWordBody->file->getRealPath(), $options);
                break;

            case File::TYPE_VIDEO:
                // video
                $result = Cloudinary::uploadVideo($dtoWordBody->file->getRealPath(), $options);
                break;

            case File::TYPE_AUDIO:
                // audio
                $result = Cloudinary::uploadVideo($dtoWordBody->file->getRealPath(), $options);
                break;

            default:
                // file
                $result = Cloudinary::uploadFile($dtoWordBody->file->getRealPath(), $options);
                break;
        }

        $publicId = $result->getPublicId();

        // save
        $bodyInfo = [
            'platformId' => $dtoWordBody->platformId,
            'usageType' => $dtoWordBody->usageType,
            'tableName' => $dtoWordBody->tableName,
            'tableColumn' => $dtoWordBody->tableColumn,
            'tableId' => $dtoWordBody->tableId,
            'tableKey' => $dtoWordBody->tableKey,
            'aid' => $dtoWordBody->aid,
            'uid' => $dtoWordBody->uid,
            'type' => $dtoWordBody->type,
            'disk' => 'remote',
            'imageHandlePosition' => null,
            'videoTime' => null,
            'videoPosterPath' => null,
            'audioTime' => null,
            'transcodingState' => 1,
            'moreJson' => $dtoWordBody->moreJson,
        ];

        $langTag = \request()->header('X-Fresns-Client-Lang-Tag', ConfigHelper::fresnsConfigDefaultLangTag());

        if ($dtoWordBody->type == File::TYPE_VIDEO) {
            $videoTime = $result->getResponse()['duration'];
            $videoMaxTime = ConfigHelper::fresnsConfigByItemKey('video_max_time');

            // check file time
            if ($videoTime > $videoMaxTime) {
                // delete file
                Cloudinary::destroy($publicId, [
                    'resource_type' => 'video',
                ]);

                return $this->failure(
                    36114,
                    ConfigUtility::getCodeMessage(36114, 'Fresns', $langTag),
                );
            }

            $bodyInfo['videoTime'] = $videoTime;
            $bodyInfo['videoPosterPath'] = $publicId.'.jpg';
            $bodyInfo['transcodingState'] = File::TRANSCODING_STATE_DONE;
        }

        if ($dtoWordBody->type == File::TYPE_AUDIO) {
            $audioTime = $result->getResponse()['duration'];
            $audioMaxTime = ConfigHelper::fresnsConfigByItemKey('audio_max_time');

            // check file time
            if ($audioTime > $audioMaxTime) {
                // delete file
                Cloudinary::destroy($publicId, [
                    'resource_type' => 'video',
                ]);

                return $this->failure(
                    36114,
                    ConfigUtility::getCodeMessage(36114, 'Fresns', $langTag),
                );
            }

            $bodyInfo['audioTime'] = $audioTime;
            $bodyInfo['transcodingState'] = File::TRANSCODING_STATE_DONE;
        }

        $extension = $dtoWordBody->file->extension();
        $path = $publicId.'.'.$extension;

        $fileInfo = FileUtility::saveFileInfoToDatabase($bodyInfo, $path, $dtoWordBody->file);

        return $this->success($fileInfo);
    }

    // uploadFileInfo
    public function uploadFileInfo($wordBody)
    {
        $dtoWordBody = new UploadFileInfoDTO($wordBody);

        $bodyInfo = [
            'platformId' => $dtoWordBody->platformId,
            'usageType' => $dtoWordBody->usageType,
            'tableName' => $dtoWordBody->tableName,
            'tableColumn' => $dtoWordBody->tableColumn,
            'tableId' => $dtoWordBody->tableId,
            'tableKey' => $dtoWordBody->tableKey,
            'aid' => $dtoWordBody->aid,
            'uid' => $dtoWordBody->uid,
            'type' => $dtoWordBody->type,
            'fileInfo' => $dtoWordBody->fileInfo,
        ];

        $fileInfo = FileUtility::uploadFileInfo($bodyInfo);

        return $this->success($fileInfo);
    }

    // getAntiLinkFileInfo
    public function getAntiLinkFileInfo($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoDTO($wordBody);

        $fileInfo = StorageHelper::info($dtoWordBody->fileIdOrFid);

        return $this->success($fileInfo);
    }

    // getAntiLinkFileInfoList
    public function getAntiLinkFileInfoList($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoListDTO($wordBody);

        $data = [];
        foreach ($dtoWordBody->fileIdsOrFids as $id) {
            $data[] = StorageHelper::info($id);
        }

        return $this->success($data);
    }

    // getAntiLinkFileOriginalUrl
    public function getAntiLinkFileOriginalUrl($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileOriginalUrlDTO($wordBody);

        if (StrHelper::isPureInt($dtoWordBody->fileIdOrFid)) {
            $file = PrimaryHelper::fresnsModelById('file', $dtoWordBody->fileIdOrFid);
        } else {
            $file = PrimaryHelper::fresnsModelByFsid('file', $dtoWordBody->fileIdOrFid);
        }

        $originalUrl = StorageHelper::url($file, 'originalUrl');

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

        StorageHelper::config($dtoWordBody->type);

        // delete
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

            // if ($file->type == File::TYPE_VIDEO && $file->video_poster_path) {
            //     // code
            // }

            // if ($file->original_path) {
            //     // code
            // }

            // delete file
            $fileDelete = Cloudinary::destroy($publicId, [
                'resource_type' => $resourceType,
            ]);

            if (! $fileDelete) {
                return $this->failure(21006);
            }

            $file->update([
                'physical_deletion' => true,
            ]);

            $file->delete();

            // forget cache
            CacheHelper::forgetFresnsFileUsage($file->id);
            CacheHelper::forgetFresnsKeys([
                "fresns_cloudinary_antilink_{$file->id}",
                "fresns_cloudinary_antilink_{$file->fid}",
            ], [
                'fresnsPlugins',
                'pluginCloudinary',
            ]);
        }

        return $this->success();
    }
}

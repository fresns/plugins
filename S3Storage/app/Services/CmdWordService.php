<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\S3Storage\Services;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use App\Models\FileUsage;
use App\Utilities\ConfigUtility;
use App\Utilities\FileUtility;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Illuminate\Support\Facades\Storage;
use Plugins\S3Storage\Helpers\StorageHelper;
use Plugins\S3Storage\Services\DTO\GetAntiLinkFileInfoDTO;
use Plugins\S3Storage\Services\DTO\GetAntiLinkFileInfoListDTO;
use Plugins\S3Storage\Services\DTO\GetAntiLinkFileOriginalUrlDTO;
use Plugins\S3Storage\Services\DTO\GetUploadTokenDTO;
use Plugins\S3Storage\Services\DTO\LogicalDeletionFilesDTO;
use Plugins\S3Storage\Services\DTO\PhysicalDeletionFilesDTO;
use Plugins\S3Storage\Services\DTO\UploadFileDTO;
use Plugins\S3Storage\Services\DTO\UploadFileInfoDTO;

class CmdWordService
{
    use CmdWordResponseTrait;

    // getUploadToken
    public function getUploadToken($wordBody)
    {
        $dtoWordBody = new GetUploadTokenDTO($wordBody);

        $data = [
            'storageId' => File::STORAGE_UNKNOWN,
            'token' => null,
            'expireTime' => null,
        ];

        return $this->success($data);
    }

    // uploadFile
    public function uploadFile($wordBody)
    {
        $dtoWordBody = new UploadFileDTO($wordBody);

        $diskConfig = StorageHelper::disk($dtoWordBody->type);

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
            'moreJson' => $dtoWordBody->moreJson,
        ];

        $fileInfo = FileUtility::uploadFile($bodyInfo, $diskConfig, $dtoWordBody->file);

        if (empty($fileInfo)) {
            $langTag = \request()->header('X-Fresns-Client-Lang-Tag', ConfigHelper::fresnsConfigDefaultLangTag());

            return $this->failure(
                32104,
                ConfigUtility::getCodeMessage(32104, 'Fresns', $langTag)
            );
        }

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

        // Storage disk
        $diskConfig = StorageHelper::disk($dtoWordBody->type);
        $fresnsStorage = Storage::build($diskConfig);

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

            $fresnsStorage->delete($file->path);

            $file->update([
                'physical_deletion' => 1,
            ]);

            $file->delete();

            // forget cache
            CacheHelper::forgetFresnsFileUsage($file->id);
            CacheHelper::forgetFresnsKeys([
                "fresns_s3_storage_antilink_{$file->id}",
                "fresns_s3_storage_antilink_{$file->fid}",
            ], [
                'fresnsPlugins',
                'pluginS3Storage',
            ]);
        }

        return $this->success();
    }
}

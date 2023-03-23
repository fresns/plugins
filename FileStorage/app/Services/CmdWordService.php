<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Services;

use App\Helpers\CacheHelper;
use App\Helpers\FileHelper as FresnsFileHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use App\Models\FileUsage;
use App\Utilities\FileUtility;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Illuminate\Support\Facades\Storage;
use Plugins\FileStorage\Helpers\ConfigHelper;
use Plugins\FileStorage\Helpers\FileHelper;
use Plugins\FileStorage\Helpers\TranscodeHelper;
use Plugins\FileStorage\Services\DTO\AudioAndVideoTranscodeDTO;
use Plugins\FileStorage\Services\DTO\GetAntiLinkFileInfoDTO;
use Plugins\FileStorage\Services\DTO\GetAntiLinkFileInfoListDTO;
use Plugins\FileStorage\Services\DTO\GetAntiLinkFileOriginalUrlDTO;
use Plugins\FileStorage\Services\DTO\GetUploadTokenDTO;
use Plugins\FileStorage\Services\DTO\LogicalDeletionFilesDTO;
use Plugins\FileStorage\Services\DTO\PhysicalDeletionFilesDTO;
use Plugins\FileStorage\Services\DTO\UploadFileDTO;
use Plugins\FileStorage\Services\DTO\UploadFileInfoDTO;

class CmdWordService
{
    use CmdWordResponseTrait;

    // getUploadToken
    public function getUploadToken($wordBody)
    {
        $dtoWordBody = new GetUploadTokenDTO($wordBody);

        $data = [
            'storageId' => 2,
            'token' => null,
            'expireTime' => null,
        ];

        return $this->success($data);
    }

    // uploadFile
    public function uploadFile($wordBody)
    {
        $dtoWordBody = new UploadFileDTO($wordBody);

        $diskConfig = ConfigHelper::disk($dtoWordBody->type);

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
            'disk' => ($diskConfig['driver'] == 'local') ? 'local' : 'remote',
            'imageHandlePosition' => 'name-end',
            'moreJson' => $dtoWordBody->moreJson,
        ];

        $fileInfo = FileUtility::uploadFile($bodyInfo, $diskConfig, $dtoWordBody->file);

        if ($fileInfo['type'] == File::TYPE_IMAGE) {
            TranscodeHelper::imageProcessing($dtoWordBody->file, $fileInfo);
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

        $fileInfo = FileHelper::info($dtoWordBody->fileIdOrFid);

        return $this->success($fileInfo);
    }

    // getAntiLinkFileInfoList
    public function getAntiLinkFileInfoList($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoListDTO($wordBody);

        $data = [];
        foreach ($dtoWordBody->fileIdsOrFids as $id) {
            $data[] = FileHelper::info($id);
        }

        return $this->success($data);
    }

    // getAntiLinkFileOriginalUrl
    public function getAntiLinkFileOriginalUrl($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileOriginalUrlDTO($wordBody);

        return $this->success([
            'originalUrl' => FileHelper::url($dtoWordBody->fileIdOrFid, 'originalUrl'),
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
        $diskConfig = ConfigHelper::disk($dtoWordBody->type);
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

            $filePaths = [
                $file->path,
                $file->original_path,
                $file->video_poster_path,
            ];

            if ($file->type == File::TYPE_IMAGE) {
                $imagePaths = FresnsFileHelper::fresnsFilePathForImage('name-end', $file->path);

                $imagePathArr = array_filter([
                    $imagePaths['configPath'],
                    $imagePaths['ratioPath'],
                    $imagePaths['squarePath'],
                    $imagePaths['bigPath'],
                ]);

                $fresnsStorage->delete($imagePathArr);
            }

            $filePaths = array_filter($filePaths);

            $fresnsStorage->delete($filePaths);

            $file->update([
                'physical_deletion' => 1,
            ]);

            $file->delete();

            // forget cache
            CacheHelper::forgetFresnsFileUsage($file->id);
            CacheHelper::forgetFresnsKeys([
                "fresns_file_storage_antilink_{$file->id}",
                "fresns_file_storage_antilink_{$file->fid}",
            ], [
                'fresnsPlugins',
                'pluginFileStorage',
            ]);
        }

        return $this->success();
    }

    // audioAndVideoTranscode
    public function audioAndVideoTranscode($wordBody)
    {
        $dtoWordBody = new AudioAndVideoTranscodeDTO($wordBody);

        return $this->success();
    }
}

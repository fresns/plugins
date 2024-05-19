<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Services;

use App\Fresns\Words\File\DTO\GetTemporaryUrlFileInfoDTO;
use App\Fresns\Words\File\DTO\GetTemporaryUrlFileInfoListDTO;
use App\Fresns\Words\File\DTO\GetTemporaryUrlOfOriginalFileDTO;
use App\Fresns\Words\File\DTO\LogicalDeletionFilesDTO;
use App\Fresns\Words\File\DTO\PhysicalDeletionFilesDTO;
use App\Fresns\Words\File\DTO\UploadFileDTO;
use App\Helpers\CacheHelper;
use App\Helpers\FileHelper as FresnsFileHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use App\Models\FileUsage;
use App\Utilities\ConfigUtility;
use App\Utilities\FileUtility;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Illuminate\Support\Facades\Storage;
use Plugins\FileStorage\Helpers\ConfigHelper;
use Plugins\FileStorage\Helpers\FileHelper;
use Plugins\FileStorage\Helpers\TranscodeHelper;

class CmdWordService
{
    use CmdWordResponseTrait;

    // getUploadToken
    public function getUploadToken($wordBody)
    {
        // S3 uploads are not supported by the storage provider
        return $this->failure(32104, ConfigUtility::getCodeMessage(32104));
    }

    // uploadFile
    public function uploadFile($wordBody)
    {
        $dtoWordBody = new UploadFileDTO($wordBody);

        $diskConfig = ConfigHelper::disk($dtoWordBody->type);

        $bodyInfo = [
            'type' => $dtoWordBody->type,
            'warningType' => $dtoWordBody->warningType,

            'usageType' => $dtoWordBody->usageType,
            'platformId' => $dtoWordBody->platformId,
            'tableName' => $dtoWordBody->tableName,
            'tableColumn' => $dtoWordBody->tableColumn,
            'tableId' => $dtoWordBody->tableId,
            'tableKey' => $dtoWordBody->tableKey,
            'moreInfo' => $dtoWordBody->moreInfo,
            'aid' => $dtoWordBody->aid,
            'uid' => $dtoWordBody->uid,
        ];

        $fileModel = FileUtility::uploadFile($bodyInfo, $diskConfig, $dtoWordBody->file);

        if ($bodyInfo['type'] == File::TYPE_IMAGE) {
            TranscodeHelper::imageProcessing($dtoWordBody->file, $fileModel);
        }

        $fileInfo = FresnsFileHelper::fresnsFileInfoById($fileModel->id, $bodyInfo);

        return $this->success($fileInfo);
    }

    // getTemporaryUrlFileInfo
    public function getTemporaryUrlFileInfo($wordBody)
    {
        $dtoWordBody = new GetTemporaryUrlFileInfoDTO($wordBody);

        $fileInfo = FileHelper::info($dtoWordBody->fileIdOrFid);

        return $this->success($fileInfo);
    }

    // getTemporaryUrlFileInfoList
    public function getTemporaryUrlFileInfoList($wordBody)
    {
        $dtoWordBody = new GetTemporaryUrlFileInfoListDTO($wordBody);

        $data = [];
        foreach ($dtoWordBody->fileIdsOrFids as $id) {
            $data[] = FileHelper::info($id);
        }

        return $this->success($data);
    }

    // getTemporaryUrlOfOriginalFile
    public function getTemporaryUrlOfOriginalFile($wordBody)
    {
        $dtoWordBody = new GetTemporaryUrlOfOriginalFileDTO($wordBody);

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

            if ($file->type == File::TYPE_VIDEO && $file->video_poster_path) {
                // code
            }

            if ($file->original_path) {
                // code
            }

            $fileDelete = $fresnsStorage->delete($file->path);

            if (! $fileDelete) {
                return $this->failure(21006);
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

    // audioAndVideoTranscode
    public function audioAndVideoTranscode($wordBody)
    {
        // code

        return $this->success();
    }
}

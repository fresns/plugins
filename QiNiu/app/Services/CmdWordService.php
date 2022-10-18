<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Models\PluginCallback;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Plugins\QiNiu\Services\AntiLinkFileInfo;
use Plugins\QiNiu\Services\AntiLinkFileInfoList;
use Plugins\QiNiu\Services\AntiLinkFileOriginalUrl;
use Plugins\QiNiu\Services\AudioVideoTranscoding;
use Plugins\QiNiu\Services\LogicalDeletionFiles;
use Plugins\QiNiu\Services\PhysicalDeletionFiles;
use Plugins\QiNiu\Services\UploadFile;
use Plugins\QiNiu\Services\UploadFileInfo;
use Plugins\QiNiu\Services\UploadToken;

class CmdWordService
{
    use CmdWordResponseTrait;

    public function getUploadToken(array $wordBody)
    {
        $uploadToken = new UploadToken($wordBody);

        return $this->success([
            'storageId' => $uploadToken->getStorageId(),
            'token' => $uploadToken->getToken(),
            'expireTime' => $uploadToken->getExpireTime(),
        ]);
    }

    public function uploadFile(array $wordBody)
    {
        $uploadFile = new UploadFile($wordBody);

        $uploadFileInfo = $uploadFile->process();

        return $this->success($uploadFileInfo);
    }

    public function uploadFileInfo(array $wordBody)
    {
        $uploadFileInfo = new UploadFileInfo($wordBody);

        $uploadFileInfos = $uploadFileInfo->process();

        return $this->success($uploadFileInfos);
    }

    public function getAntiLinkFileInfo(array $wordBody)
    {
        $antiLinkFileInfo = new AntiLinkFileInfo($wordBody);

        $antiLinkFileInfoData = $antiLinkFileInfo->getAntiLinkFileInfo();

        return $this->success($antiLinkFileInfoData);
    }

    public function getAntiLinkFileInfoList(array $wordBody)
    {
        $antiLinkFileInfoList = new AntiLinkFileInfoList($wordBody);

        $antiLinkFileInfoListData = $antiLinkFileInfoList->getAntiLinkFileInfoList();

        return $this->success($antiLinkFileInfoListData);
    }

    public function getAntiLinkFileOriginalUrl(array $wordBody)
    {
        $antiLinkFileOriginalUrl = new AntiLinkFileOriginalUrl($wordBody);

        $data = $antiLinkFileOriginalUrl->getAntiLinkFileOriginalUrl();

        return $this->success($data);
    }

    public function logicalDeletionFiles(array $wordBody)
    {
        $logicalDeletionFiles = new LogicalDeletionFiles($wordBody);

        $logicalDeletionFiles->delete();

        return $this->success();
    }

    public function physicalDeletionFiles(array $wordBody)
    {
        $physicalDeletionFiles = new PhysicalDeletionFiles($wordBody);
        $physicalDeletionFiles->delete();

        return $this->success();
    }

    public function audioVideoTranscoding(array $wordBody)
    {
        $audioVideoTranscoding = new AudioVideoTranscoding($wordBody);
        $audioVideoTranscoding->process();

        return $this->success();
    }
}

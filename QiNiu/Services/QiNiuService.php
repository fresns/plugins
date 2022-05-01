<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Models\File;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Plugins\QiNiu\Events\UploadTokenGenerated;
use Plugins\QiNiu\FileInfoOfAntiLink;
use Plugins\QiNiu\FresnsUploadFile;
use Plugins\QiNiu\FresnsUploadFiles;
use Plugins\QiNiu\QiNiu;

class QiNiuService
{
    use CmdWordResponseTrait;

    public function getUploadToken(array $wordBody)
    {
        $disk = new QiNiu($wordBody);

        $token = $disk->getAdapter()->getUploadToken($disk->getName(), $disk->getExpireTime());

        event(new UploadTokenGenerated($disk, $token));

        return $this->success([
            'storageId' => $disk->getStorageId(),
            'token' => $token,
            'expireTime' => $disk->getExpireTime(),
        ]);
    }

    public function uploadFile(array $wordBody)
    {
        $fresnsUploadFile = new FresnsUploadFile($wordBody);

        $upload = $fresnsUploadFile->upload();

        return $this->success($upload);
    }

    public function uploadFileInfo(array $wordBody)
    {
        $fresnsUploadFile = new FresnsUploadFiles($wordBody);

        $upload = $fresnsUploadFile->upload();

        return $this->success($upload);
    }

    public function getFileUrlOfAntiLink(array $wordBody)
    {
        $fileUrlOfAntiLink = new FileInfoOfAntiLink($wordBody);

        return $this->success($fileUrlOfAntiLink->getFileUrlOfAntiLink());
    }

    public function getFileInfoOfAntiLink(array $wordBody)
    {
        $fileUrlOfAntiLink = new FileInfoOfAntiLink($wordBody);

        return $this->success($fileUrlOfAntiLink->getFileInfoOfAntiLink());
    }

    public function physicalDeletionFile(array $wordBody)
    {
        \validator()->validate($wordBody, [
            'fileId' => 'required_without:fid|integer',
            'fid' => 'required_without:fileId|string',
        ]);

        $file = File::idOrFid([
            'id' => $wordBody['fileId'],
            'fid' => $wordBody['fid'],
        ])->firstOrFail();

        $cmd = \Plugins\QiNiu\PluginConfig::FRESNS_CMD_PHYSICAL_DELETION_FILE;

        $pluginClass = \App\Fresns\Api\Center\Helper\PluginHelper::findPluginClass('QiNiu');

        $input = [
            'fid' => $file->fid,
        ];

        $resp = \App\Fresns\Api\Center\Helper\CmdRpcHelper::call($pluginClass, $cmd, $input);

        if (\App\Fresns\Api\Center\Helper\CmdRpcHelper::isErrorCmdResp($resp)) {
            // todo: 失败的时候暂时不知道返回了什么信息，先直接输出，后续遇到反馈后再排查调整
            return $resp;
        }

        return $this->success();
    }

    public function qiniuTranscoding(array $wordBody)
    {
        \validator()->validate($wordBody, [
            'tableName' => 'required|string|in:posts,comments',
            'primaryId' => 'required|integer',
            'changeType' => 'required|in:insert,delete',
        ]);

        $cmd = \Plugins\QiNiu\PluginConfig::FRESNS_CMD_QINIU_TRANSCODING;

        $pluginClass = \App\Fresns\Api\Center\Helper\PluginHelper::findPluginClass('QiNiu');

        $input = [
            'tableName' => $wordBody['tableName'],
            'insertId' => $wordBody['primaryId'],
        ];

        $resp = \App\Fresns\Api\Center\Helper\CmdRpcHelper::call($pluginClass, $cmd, $input);

        if (\App\Fresns\Api\Center\Helper\CmdRpcHelper::isErrorCmdResp($resp)) {
            // todo: 失败的时候暂时不知道返回了什么信息，先直接输出，后续遇到反馈后再排查调整
            return $resp;
        }

        return $this->success();
    }
}

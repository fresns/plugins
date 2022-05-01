<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu;

use App\Models\File;

class FileInfoOfAntiLink
{
    protected $file;
    protected $storage;

    public function __construct(array $wordBody)
    {
        $this->validate($wordBody);

        $this->fileId = $wordBody['fileId'] ?? null;
        $this->fid = $wordBody['fid'] ?? null;

        $this->file = $this->getFile();
        $this->storage = $this->getStorage();
    }

    public function validate(array $data)
    {
        \validator()->validate($data, [
            'fileId' => 'required_without:fid|integer',
            'fid' => 'required_without:fileId|string',
        ]);
    }

    public function getFile()
    {
        if (! $this->file) {
            $this->file = File::idOrFid([
                'id' => $this->fileId,
                'fid' => $this->fid,
            ])->firstOrFail();
        }

        return $this->file;
    }

    public function getStorage()
    {
        return new Storage($this->getFile()->file_type);
    }

    public function getFileUrlOfAntiLink()
    {
        $serviceInfo = $this->file->getFileServiceInfo();

        // 未开启防盗链
        if (! $serviceInfo['url_anti_status']) {
            return $this->file->getFileInfo();
        }

        $cmd = match ($this->file->file_type) {
            default => throw new \LogicException('未知文件类型'),
            File::TYPE_IMAGE => \Plugins\QiNiu\PluginConfig::FRESNS_CMD_ANTI_LINK_IMAGE,
            File::TYPE_VIDEO => \Plugins\QiNiu\PluginConfig::FRESNS_CMD_ANTI_LINK_VIDEO,
            File::TYPE_AUDIO => \Plugins\QiNiu\PluginConfig::FRESNS_CMD_ANTI_LINK_AUDIO,
            File::TYPE_DOCUMENT => \Plugins\QiNiu\PluginConfig::FRESNS_CMD_ANTI_LINK_DOCUMENT,
        };

        $pluginClass = \App\Fresns\Api\Center\Helper\PluginHelper::findPluginClass('QiNiu');

        $input = [
            'fid' => $this->file->fid,
        ];

        $resp = \App\Fresns\Api\Center\Helper\CmdRpcHelper::call($pluginClass, $cmd, $input);

        if (\App\Fresns\Api\Center\Helper\CmdRpcHelper::isErrorCmdResp($resp)) {
            // todo: 失败的时候暂时不知道返回了什么信息，先直接输出，后续遇到反馈后再排查调整
            return $resp;
        }

        return array_merge([
            'type' => $this->file->type,
        ], $resp['output']);
    }

    public function getFileInfoOfAntiLink()
    {
        $serviceInfo = $this->file->getFileServiceInfo();

        // 未开启防盗链
        if (! $serviceInfo['url_anti_status']) {
            // return $this->file->getFileInfo();
        }

        $cmd = match ($this->file->file_type) {
            default => throw new \LogicException('未知文件类型'),
            File::TYPE_IMAGE => \Plugins\QiNiu\PluginConfig::FRESNS_CMD_ANTI_LINK_IMAGE,
            File::TYPE_VIDEO => \Plugins\QiNiu\PluginConfig::FRESNS_CMD_ANTI_LINK_VIDEO,
            File::TYPE_AUDIO => \Plugins\QiNiu\PluginConfig::FRESNS_CMD_ANTI_LINK_AUDIO,
            File::TYPE_DOCUMENT => \Plugins\QiNiu\PluginConfig::FRESNS_CMD_ANTI_LINK_DOCUMENT,
        };

        $pluginClass = \App\Fresns\Api\Center\Helper\PluginHelper::findPluginClass('QiNiu');

        $input = [
            'fid' => $this->file->fid,
        ];

        $resp = \App\Fresns\Api\Center\Helper\CmdRpcHelper::call($pluginClass, $cmd, $input);

        if (\App\Fresns\Api\Center\Helper\CmdRpcHelper::isErrorCmdResp($resp)) {
            // todo: 失败的时候暂时不知道返回了什么信息，先直接输出，后续遇到反馈后再排查调整
            return $resp;
        }

        return array_merge($this->file->getFileInfo(), $resp['output']);
    }
}

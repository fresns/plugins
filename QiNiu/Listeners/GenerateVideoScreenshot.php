<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Listeners;

use App\Helpers\ConfigHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Plugins\QiNiu\Events\FileUpdateToQiNiuSuccessfual;
use Plugins\QiNiu\Storage;

class GenerateVideoScreenshot
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 如果是视频文件，则执行配置表 videos_screenshot 键值，生成一条视频封面图并存入 file_appends > video_cover 字段.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FileUpdateToQiNiuSuccessfual $event)
    {
        $fileModel = $event->fileModel;
        $qiniuFilePath = $event->qiniuFilePath;

        if (! $fileModel->isVideo()) {
            return;
        }

        $storage = new Storage($fileModel->file_type);
        $pfop = $storage->getPersistentFop();

        $bucket = $storage->getConfig()['bucket'];
        $transParams = ConfigHelper::fresnsConfigByItemKey('video_screenshot');

        // 原文件位置
        $key = $qiniuFilePath;

        // 截图文件名
        $pathinfo = pathinfo($qiniuFilePath);
        $videoScreenshotName = str_replace($pathinfo['extension'], 'jpg', $pathinfo['basename']);

        // 截图文件存放位置
        $videoScreenshotPath = sprintf('%s/%s', $fileModel->getDestinationPath(), $videoScreenshotName);

        $fops = $transParams.'|saveas/'.\Qiniu\base64_urlSafeEncode("$bucket:$videoScreenshotPath");
        $pipeline = 'default.sys';
        $notifyUrl = $this->getNotifyUrl();
        $force = false;

        $pfop->execute($bucket, $key, $fops, $pipeline, $notifyUrl, $force);

        $fileModel->fileAppend->update([
            'video_cover' => $videoScreenshotPath,
        ]);
    }

    public function getNotifyUrl()
    {
        $bucketDomain = ConfigHelper::fresnsConfigByItemKey('backend_domain');

        $api = '/api/qiniu/trans/notify';

        $notifyUrl = sprintf('%s/%s', rtrim($bucketDomain, '/'), ltrim($api));

        $callbackParam = \request()->input('callback_param');
        if ($callbackParam) {
            $notifyUrl = $notifyUrl.'?callback_param='.$callbackParam;
        }

        return $notifyUrl;
    }
}

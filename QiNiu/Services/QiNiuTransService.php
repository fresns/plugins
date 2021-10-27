<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\QiNiu\Services;

use App\Helpers\CommonHelper;
use App\Http\Center\Common\LogService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use Illuminate\Support\Facades\Cache;
use Qiniu\Config;
use Qiniu\Processing\PersistentFop;

// 加载七牛云 SDK
require_once dirname(dirname(__FILE__)).'/QiNiuSdk/autoload.php';

/**
 * Class QiNiuTransService
 * 七牛云转码服务
 */
class QiNiuTransService extends QiNiuService
{
    const NOTIFY_URI = '/api/qiniu/trans/notify';

    // 回调地址
    public $notifyUrl;

    // 当转码后的文件名与源文件名相同时，是否覆盖源文件
    public $force;

    public $bucket;

    public $pfop;

    // 用户默认没有私有队列，需要在这里创建然后填写
    // https://portal.qiniu.com/dora/media-gate/pipeline
    public $pipeline;

    // 初始化参数
    public function initTrans()
    {
        $this->notifyUrl = $this->getNotifyUrl();
        $this->force = false;
        $this->bucket = $this->qiNiuBucketName;

        $config = new Config();
        $config->useHTTPS = true;
        $this->pfop = new PersistentFop($this->qiNiuAuth, $config);

        $this->pipeline = 'default.sys';
    }

    // 转换基础函数
    public function trans($key, $saveAsKey, $transParams)
    {
        $this->initTrans();

        $bucket = $this->bucket;
        // 进行操作
        $fops = $transParams.'|saveas/'.\Qiniu\base64_urlSafeEncode("$bucket:$saveAsKey");
        // dd($this->notifyUrl);
        [$id, $err] = $this->pfop->execute($bucket, $key, $fops, $this->pipeline, $this->notifyUrl, $this->force);
        if ($err != null) {
            LogService::info('pfop avthumb error ', $err);
        } else {
            LogService::info('pfop avthumb result', $id);
        }

        // 查询转码的进度和状态
        [$ret, $err] = $this->pfop->status($id);
        LogService::info('pfop avthumb status result', $ret);

        return $id;
    }

    /**
     * 音频转码
     *
     * @param $key
     * @param $saveAsKey
     * @param $transParams
     * @param  string  $pipeline
     * @return mixed
     */
    public function transAudio($key, $saveAsKey, $transParams, $tableName = null, $insertId = null)
    {
        $id = $this->trans($key, $saveAsKey, $transParams);
        Cache::put($tableName.'_'.$insertId, $id);

        return $id;
    }

    /**
     * 视频转码
     *
     * @param $key
     * @param $saveAsKey
     * @param $transParams
     * @param  string  $pipeline
     * @return mixed
     */
    public function transVideo($key, $saveAsKey, $transParams, $tableName = null, $insertId = null)
    {
        $id = $this->trans($key, $saveAsKey, $transParams);
        Cache::put($tableName.'_'.$insertId, $id);

        return $id;
    }

    /**
     * 视频帧缩略图
     * 对已经上传到七牛的视频发起异步转码操作.
     *
     * @param $key
     * @param $saveAsKey : 视频处理完毕后保存到空间中的名称
     * @param  string  $pipeline
     *                            https://developer.qiniu.com/dora/api/1313/video-frame-thumbnails-vframe
     * @return mixed
     */
    public function vframe($key, $saveAsKey, $transParams)
    {
        $id = $this->trans($key, $saveAsKey, $transParams);

        return $id;
    }

    // 转码完成后通知到你的业务服务器（需要可以公网访问，并能够相应 200 OK）
    public function getNotifyUrl()
    {
        $domain = ApiConfigHelper::getConfigByItemKey('backend_domain');
        $notifyUrl = $domain.self::NOTIFY_URI;
        $callbackParam = request()->input('callback_param');
        if ($callbackParam) {
            $notifyUrl = $notifyUrl.'?callback_param='.$callbackParam;
        }

        return $notifyUrl;
    }

    //通过转码id去查询进度
    public function searchStatus($id)
    {
        $this->initTrans();

        // 查询转码的进度和状态
        [$ret, $err] = $this->pfop->status($id);
        LogService::info('pfop avthumb status result', $ret);

        $data = [];
        $data['ret'] = $ret;
        $data['err'] = $err;

        return $data;
    }
}

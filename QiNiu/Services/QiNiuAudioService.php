<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\QiNiu\Services;

use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Plugins\QiNiu\QiNiuConfig;

class QiNiuAudioService extends QiNiuService
{
    // 获取音频防盗链地址
    public function getAudioDownloadUrl($url, $options = [])
    {

        // 获取防盗链配置
        $audioUrlStatus = ApiConfigHelper::getConfigByItemKey(QiNiuConfig::AUDIO_URL_STATUS);
        $audioUrlExpire = ApiConfigHelper::getConfigByItemKey(QiNiuConfig::AUDIO_URL_EXPIRE);

        // 判断防盗链状态
        if ($audioUrlStatus === false) {
            return $url;
        }

        // 将地址时效由分钟转换成秒
        $audioUrlExpire = intval($audioUrlExpire * 60);

        // 获取地址
        $downloadUrl = $this->getPrivateDownloadUrl($url, $audioUrlExpire);

        // 输出地址
        return $downloadUrl;
    }
}

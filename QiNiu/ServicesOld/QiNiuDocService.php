<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\ServicesOld;

use App\Helpers\ConfigHelper;
use Plugins\QiNiu\QiNiuConfig;

class QiNiuDocService extends QiNiuService
{
    // 获取文档防盗链地址
    public function getDocDownloadUrl($url, $options = [])
    {

        // 获取防盗链配置
        $docUrlStatus = ConfigHelper::fresnsConfigByItemKey(QiNiuConfig::DOCUMENT_URL_STATUS);
        $docUrlExpire = ConfigHelper::fresnsConfigByItemKey(QiNiuConfig::DOCUMENT_URL_EXPIRE);

        // 判断防盗链状态
        if ($docUrlStatus === false) {
            return $url;
        }

        // 将地址时效由分钟转换成秒
        $docUrlExpire = intval($docUrlExpire * 60);

        // 获取地址
        $downloadUrl = $this->getPrivateDownloadUrl($url, $docUrlExpire);

        // 输出地址
        return $downloadUrl;
    }
}

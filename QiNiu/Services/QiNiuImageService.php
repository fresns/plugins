<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\QiNiu\Services;

use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Plugins\QiNiu\QiNiuConfig;

class QiNiuImageService extends QiNiuService
{
    // 获取图片防盗链地址
    public function getImageDownloadUrl($url, $options = [])
    {

        // 获取防盗链配置
        $imageUrlStatus = ApiConfigHelper::getConfigByItemKey(QiNiuConfig::IMAGE_URL_STATUS);
        $imageUrlExpire = ApiConfigHelper::getConfigByItemKey(QiNiuConfig::IMAGE_URL_EXPIRE);

        // 判断防盗链状态
        if ($imageUrlStatus === false) {
            return $url;
        }

        // 将地址时效由分钟转换成秒
        $imageUrlExpire = intval($imageUrlExpire * 60);

        // 获取地址
        $downloadUrl = $this->getPrivateDownloadUrl($url, $imageUrlExpire);

        // 输出地址
        return $downloadUrl;
    }

    // 上传策略
    public function getPolicy($options = [])
    {
        $returnBody = '{
                "name": $(fname),
                "size": $(fsize),
                "width": $(imageInfo.width),
                "height": $(imageInfo.height),
                "format": $(imageInfo.format),
                "key": $(key),
                "qiNiuUuid": $(uuid),
                "fileType": $(x:fileType), 
                "hash": $(etag)
            }';
        $policy = [
            'returnBody'    => $returnBody,
        ];

        return $policy;
    }
}

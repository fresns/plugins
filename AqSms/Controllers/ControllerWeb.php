<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\AqSms\Controllers;

use App\Base\Controllers\BaseFrontendController;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use Illuminate\Http\Request;

class ControllerWeb extends BaseFrontendController
{
    /**
     * 设置页.
     */
    public function setting(Request $request)
    {
        $data = [];

        $aqsmsType = ApiConfigHelper::getConfigByItemKey('aqsms_type', '', 'number');
        $keyId = ApiConfigHelper::getConfigByItemKey('aqsms_keyid');
        $keySecret = ApiConfigHelper::getConfigByItemKey('aqsms_keysecret');
        $sdkAppId = ApiConfigHelper::getConfigByItemKey('aqsms_appid');

        $data['aqsms_type'] = $aqsmsType;
        $data['key_id'] = $keyId;
        $data['key_secret'] = $keySecret;
        $data['sdk_appid'] = $sdkAppId;

        return view('plugins.AqSms.setting', $data);
    }
}

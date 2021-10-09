<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\AqSms\Controllers;

use App\Base\Controllers\BaseApiController;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Plugins\AqSms\AqSmsHelper;
use Illuminate\Http\Request;

class ControllerApi extends BaseApiController
{
    // Get Status Code
    public function getCodeMap()
    {
        return FresnsPluginConfig::CODE_MAP;
    }

    // 保存设置
    public function saveSetting(Request $request)
    {
        $data = [];

        $keyAndFormFieldMap = [
            'aqsms_type'    => 'aqsms_type',
            'aqsms_keyid'   => 'aqsms_keyid',
            'aqsms_keysecret'   => 'aqsms_keysecret',
            'aqsms_appid'   => 'aqsms_sdk_appid',
        ];
        foreach ($keyAndFormFieldMap as $dbKey => $formField) {
            $formFieldValue = $request->input($formField);
            AqSmsHelper::insertOrUpdateConfigItem($dbKey, $formFieldValue);
            $data[$dbKey] = $formFieldValue;
        }


        $this->success($data);
    }


}

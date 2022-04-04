<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\Http\Controllers;

use Illuminate\Routing\Controller;
use Plugins\EasySms\DTO\SettingDTO;
use Plugins\EasySms\Models\Config;
use Plugins\EasySms\Services\SmsConfig;

class EasySmsController extends Controller
{
    /**
     * @var SmsConfig
     */
    protected $smsSystemConfig;

    public function __construct()
    {
        $this->smsSystemConfig = app(SmsConfig::class);
    }

    public function setting()
    {
        return view('EasySms::setting', [
            'easysms_type' => $this->smsSystemConfig->getEasySmsType(),
            'easysms_keyid' => $this->smsSystemConfig->getKeyId(),
            'easysms_keysecret' => $this->smsSystemConfig->getKeySecret(),
            'easysms_sdk_appid' => $this->smsSystemConfig->getAppId(),
            'easysms_linked' => json_encode($this->smsSystemConfig->getEasySmsLinked()),
        ]);
    }

    public function saveSetting()
    {
        $settingDTO = SettingDTO::make(\request()->all());

        $data = [];
        foreach ($settingDTO->toArray() as $key => $value) {
            if ($key === 'easysms_linked') {
                $value = json_decode($value, true);
            }

            $config = Config::updateOrCreate([
                'item_key' => $key,
            ], [
                'item_value' => $value,
            ]);

            $data[$config->item_key] = $config->item_value;
        }

        return \response()->json([
            'code' => 0,
            'message' => 'success',
            'data' => $data,
        ]);
    }
}

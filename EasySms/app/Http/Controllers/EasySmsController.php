<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\Http\Controllers;

use App\Helpers\PluginHelper;
use App\Models\Config;
use Illuminate\Routing\Controller;
use Plugins\EasySms\DTO\SettingDTO;
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
        $version = PluginHelper::fresnsPluginVersionByUnikey('EasySms');

        return view('EasySms::setting', [
            'version' => $version,
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

            $itemType = match ($key) {
                'easysms_type' => 'number',
                'easysms_linked' => 'object',
                default => 'string',
            };

            $config = Config::updateOrCreate([
                'item_key' => $key,
            ], [
                'item_value' => $value,
                'item_type' => $itemType,
                'item_tag' => 'easysms',
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

<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\Services;

use App\Models\Config;

class SmsConfig
{
    protected $gateways = [
        0 => \Overtrue\EasySms\Gateways\ErrorlogGateway::class, // 阿里云短信
        1 => \Overtrue\EasySms\Gateways\AliyunGateway::class, // 阿里云短信
        2 => \Overtrue\EasySms\Gateways\QcloudGateway::class, // 腾讯云短信
    ];

    public function getValueByConfigItemKey(string $field)
    {
        $value = Config::where('item_key', $field)->first()?->item_value;

        return $value;
    }

    /**
     * 获取国际区号匹配语言标签配置.
     *
     * @return array|null
     */
    public function getEasySmsLinked(): ?array
    {
        $value = $this->getValueByConfigItemKey('easysms_linked');

        $default = [
            "86" => "zh-Hans",
            "other" => "en",
        ];

        return $value ?? $default;
    }

    public function getAppId(): ?string
    {
        $value = $this->getValueByConfigItemKey('easysms_sdk_appid');

        return $value;
    }

    public function getKeyId(): ?string
    {
        $value = $this->getValueByConfigItemKey('easysms_keyid');

        return $value;
    }

    public function getKeySecret(): ?string
    {
        $value = $this->getValueByConfigItemKey('easysms_keysecret');

        return $value;
    }

    public function getEasySmsType()
    {
        $value = $this->getValueByConfigItemKey('easysms_type');

        return $value;
    }

    public function getCodeTemplate(string $templateId, string $langTag)
    {
        $templateBlade = $this->getValueByConfigItemKey('verifycode_template'.$templateId);

        $templateData = $templateBlade;

        $sms = [];
        if ($templateData) {
            foreach ($templateData as $t) {
                if ($t['type'] == 'sms') {
                    $sms = $t['template'];
                }
            }
        }

        $data = [];
        foreach ($sms as $s) {
            if ($s['langTag'] == $langTag) {
                $data['sign_name'] = $s['signName'];
                $data['template'] = $s['templateCode'];
                $data['code_param'] = $s['codeParam'];
                $data['data'][$s['codeParam']] = '';
            }
        }

        return $data;
    }

    public function getVerifyCodesTemplate(string $templateCode, string $langTag = 'en')
    {
        $template = Config::tag('verifyCodes')->get();

        $enableTemplates = $template
            ->where('is_enable')
            ->reduce(function ($carry, $item) use ($templateCode, $langTag) {
                $data = collect($item->item_value)
                    ->where('type', 'sms')
                    ->filter(function ($item) {
                        return $item['isEnable'] ?? false;
                    })
                    ->all();

                // 筛选语言标签
                $template = collect($data)
                    ->pluck('template')
                    ->flatten(1)
                    ->where('templateCode', $templateCode)
                    ->where('langTag', $langTag)
                    ->first();

                $carry[] = [
                    'sence' => $item->item_key,
                    'template_type' => 'sms',
                    'template_code' => $templateCode,
                    'template' => $template ?? null,
                ];

                return $carry;
            }, []);

        $template = collect($enableTemplates)->first();

        return $template;
    }

    /**
     * 国际区号匹配语言标签 easysms_linked.
     *
     * @param  int|null  $countryCode
     * @return string
     */
    public function getLangTagOfEasySmsLinked(?int $countryCode = null): string
    {
        $countryCode = $countryCode ?? 'other';

        $aqSmsLinked = $this->getEasySmsLinked();

        return $aqSmsLinked[$countryCode] ?? 'en';
    }

    /**
     * 获取发送短信的网关.
     *
     * @return string|null
     */
    public function getEasySmsGateway(): ?string
    {
        return $this->gateways[$this->getEasySmsType()] ?? null;
    }

    /**
     * 获取发送短信的网关.
     *
     * @return string|null
     */
    public function getEasySmsGatewayName(): ?string
    {
        $gateway = $this->getEasySmsGateway();

        $name = str_replace(['Overtrue\\EasySms\\Gateways\\', 'Gateway'], '', $gateway);

        $name = lcfirst($name);

        return $name;
    }
}

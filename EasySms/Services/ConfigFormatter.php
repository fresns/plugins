<?php

namespace Plugins\EasySms\Services;

class ConfigFormatter
{
    protected $smsSystemConfig;

    public function __construct()
    {
        $this->smsSystemConfig = app(SmsConfig::class);
    }

    public function formatAliyunGatewayConfig(string $signName)
    {
        return [
            'access_key_id' => $this->smsSystemConfig->getKeyId(),
            'access_key_secret' => $this->smsSystemConfig->getKeySecret(),
            'sign_name' => $signName,
        ];
    }

    public function formatQcloudGatewayConfig(string $signName)
    {
        return [
            'sdk_app_id' => $this->smsSystemConfig->getAppId(),
            'secret_key' => $this->smsSystemConfig->getKeyId(),
            'secret_id' => $this->smsSystemConfig->getKeySecret(),
            'sign_name' => $signName,
        ];
    }
}
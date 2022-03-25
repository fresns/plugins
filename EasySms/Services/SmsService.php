<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\Services;

use Fresns\CmdWordManager\Exceptions\Constants\ExceptionConstant;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\PhoneNumber;
use Plugins\EasySms\DTO\SmsDTO;
use Plugins\EasySms\DTO\SmsSendCodeDTO;
use Plugins\EasySms\Models\VerifyCode;

class SmsService
{
    use \Fresns\CmdWordManager\Traits\CmdWordResponseTrait;

    protected $smsSystemConfig;

    protected $langTag = 'en';

    protected $template;

    public function __construct()
    {
        $this->smsSystemConfig = app(SmsConfig::class);
        $this->configFormatter = app(ConfigFormatter::class);
    }

    public function sms(string $signName)
    {
        $gateway = $this->smsSystemConfig->getEasySmsGatewayName();

        if ($gateway !== 'errorlog') {
            $formatConfigMethod = sprintf('format%sGatewayConfig', ucfirst($gateway));

            $userConfig = $this->configFormatter->$formatConfigMethod($signName);

            config([
                "EasySms.gateways.{$gateway}" => $userConfig,
            ]);
        }

        $config = config('EasySms');

        return new EasySms($config);
    }

    public function sendCode(SmsSendCodeDTO $smsDTO)
    {
        // 国际区号匹配语言标签 easysms_linked
        $this->langTag = $this->smsSystemConfig->getLangTagOfEasySmsLinked($smsDTO->countryCode);

        // 模板
        $template = $this->smsSystemConfig->getCodeTeamplate($smsDTO->templateId, $this->langTag);

        if (empty($template)) {
            ExceptionConstant::getHandleClassByCode(ExceptionConstant::ERROR_CODE_20004)::throw('未找到短信模板');
        }

        if (empty($this->smsSystemConfig->getKeyId()) || empty($this->smsSystemConfig->getKeySecret())) {
            ExceptionConstant::getHandleClassByCode(ExceptionConstant::ERROR_CODE_20004)::throw('缺少服务商配置信息');
        }

        // 发送短信的网关
        $gateway = $this->smsSystemConfig->getEasySmsGatewayName();

        // 发信目标
        $to = $this->getNumber($smsDTO->account, $smsDTO->countryCode);

        // 验证码
        $code = rand(100000, 999999);

        // 发送短信
        try {
            $response = $this->sms($template['sign_name'])->send($to, [
                'content' => $data['content'] ?? '您的验证码是'.$code,
                'template' => $template['template'],
                'data' => [
                    $template['code_param'] => $code,
                ],
            ], [$gateway]);
        } catch (\Throwable $e) {
            $message = $this->formatGatewayResult($e);

            ExceptionConstant::getHandleClassByCode(ExceptionConstant::ERROR_CODE_20005)::throw($message);
        }

        $this->saveCodeToDatabase($code, $to, $smsDTO->templateId);

        return $this->success($response);
    }

    public function saveCodeToDatabase(string $code, PhoneNumber $to, string $templateId)
    {
        $phone = $to->getIDDCode().$to->getNumber();

        $data = [
            'account'     => $phone,
            'template_id'     => $templateId,
            'type'  => 2,
            'code'  => $code,
            'expired_at' => now()->addMinutes(10)->toDateTimeString(),
        ];

        $verifyCode = VerifyCode::create($data);

        return $verifyCode;
    }

    public function sendSms(SmsDTO $smsDTO)
    {
        if (empty($this->smsSystemConfig->getKeyId()) || empty($this->smsSystemConfig->getKeySecret())) {
            ExceptionConstant::getHandleClassByCode(ExceptionConstant::ERROR_CODE_20004)::throw('缺少服务商配置信息');
        }

        // 发送短信的网关
        $gateway = $this->smsSystemConfig->getEasySmsGatewayName();

        // 发信目标
        $to = $this->getNumber($smsDTO->phoneNumber, $smsDTO->countryCode);

        // 发送短信
        try {
            $response = $this->sms($smsDTO->signName)->send($to, [
                'content' => $smsDTO->content,
                'template' => $smsDTO->templateCode,
                'data' => $smsDTO->templateParam,
            ], [$gateway]);
        } catch (\Throwable $e) {
            $message = $this->formatGatewayResult($e);

            ExceptionConstant::getHandleClassByCode(ExceptionConstant::ERROR_CODE_20005)::throw($message);
        }

        return $this->success($response);
    }

    public function getNumber($number, $iddCode)
    {
        return new PhoneNumber($number, $iddCode);
    }

    protected function formatGatewayResult(\Throwable $exception)
    {
        $message = '';

        if (! $exception instanceof \Overtrue\EasySms\Exceptions\NoGatewayAvailableException) {
            return $exception->getMessage();
        }

        foreach ($exception->getExceptions() as $gatewayName => $gatewayResult) {
            $message .= sprintf('%s: %s\n', $gatewayName, $gatewayResult->getMessage());
        }

        return $message;
    }
}

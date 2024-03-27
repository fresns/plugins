<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SmtpEmail\Mail;

use App\Helpers\ConfigHelper;
use App\Helpers\StrHelper;
use App\Models\VerifyCode;

class MailService
{
    public static function makeMailCode($account, $templateId): array
    {
        try {
            $code = StrHelper::generateDigital();

            $data = [
                'type' => VerifyCode::TYPE_EMAIL,
                'account' => $account,
                'template_id' => $templateId,
                'code' => $code,
                'expired_at' => now()->addSeconds(10),
            ];

            $verifyCode = VerifyCode::create($data);

            if ($verifyCode) {
                return [
                    'code' => 0,
                    'message' => 'ok',
                    'data' => [
                        'emailCode' => $code,
                    ],
                ];
            }

            return [
                'code' => 10000,
                'message' => 'Verify code create error',
                'data' => null,
            ];
        } catch (\Error $error) {
            return [
                'code' => 10000,
                'message' => $error->getMessage(),
                'data' => null,
            ];
        }
    }

    // replace config from db setting
    public static function initMailSetting(): void
    {
        $host = ConfigHelper::fresnsConfigByItemKey('fresnsemail_smtp_host');
        $port = ConfigHelper::fresnsConfigByItemKey('fresnsemail_smtp_port') ?? 25;
        $user = ConfigHelper::fresnsConfigByItemKey('fresnsemail_smtp_username');
        $pass = ConfigHelper::fresnsConfigByItemKey('fresnsemail_smtp_password');
        $type = ConfigHelper::fresnsConfigByItemKey('fresnsemail_verify_type');
        $from_addr = ConfigHelper::fresnsConfigByItemKey('fresnsemail_from_mail');
        $from_name = ConfigHelper::fresnsConfigByItemKey('fresnsemail_from_name');

        $smtp = [
            'transport' => 'smtp',
            'host' => $host,
            'port' => (int) $port,
            'encryption' => $type ?? 'tls',
            'username' => $user,
            'password' => $pass,
        ];
        $from = [
            'address' => $from_addr,
            'name' => $from_name,
        ];

        config(['mail.default' => 'smtp']);
        config(['mail.mailers.smtp' => array_merge(config('mail.mailers.smtp'), $smtp)]);
        config(['mail.from' => array_merge(config('mail.from'), $from)]);
    }

    public static function getTemplateValue($templateId, $langTag): ?array
    {
        $templateValue = ConfigHelper::fresnsConfigByItemKey('verifycode_template'.$templateId);

        $templates = $templateValue['email']['templates'] ?? [];

        return StrHelper::languageContent($templates, $langTag);
    }
}

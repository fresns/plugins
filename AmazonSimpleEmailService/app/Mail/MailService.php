<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\AmazonSimpleEmailService\Mail;

use App\Helpers\ConfigHelper;
use App\Helpers\StrHelper;
use App\Models\TempVerifyCode;

class MailService
{
    public static function makeMailCode($account, $templateId): array
    {
        try {
            $code = StrHelper::generateDigital();

            $data = [
                'type' => TempVerifyCode::TYPE_EMAIL,
                'account' => $account,
                'template_id' => $templateId,
                'code' => $code,
                'expired_at' => now()->addMinutes(15),
            ];

            $verifyCode = TempVerifyCode::create($data);

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
        $amazon_ses_access_key_id = ConfigHelper::fresnsConfigByItemKey('amazon_ses_access_key_id');
        $amazon_ses_secret_access_key = ConfigHelper::fresnsConfigByItemKey('amazon_ses_secret_access_key');
        $amazon_ses_region = ConfigHelper::fresnsConfigByItemKey('amazon_ses_region');
        $from_address = ConfigHelper::fresnsConfigByItemKey('amazon_ses_from_mail');
        $from_name = ConfigHelper::fresnsConfigByItemKey('amazon_ses_from_name');

        $services = [
            'key' => $amazon_ses_access_key_id,
            'secret' => $amazon_ses_secret_access_key,
            'region' => $amazon_ses_region,
        ];
        $from = [
            'address' => $from_address,
            'name' => $from_name,
        ];

        config(['mail.default' => 'ses']);
        config(['services.ses' => array_merge(config('services.ses'), $services)]);
        config(['mail.from' => array_merge(config('mail.from'), $from)]);

        putenv('SSL_CERT_FILE=/etc/ssl/certs/ca-certificates.crt');
        putenv('SSL_CERT_DIR=/etc/ssl/certs/');
    }

    public static function getTemplateValue($templateId, $langTag): ?array
    {
        $templateValue = ConfigHelper::fresnsConfigByItemKey('verifycode_template'.$templateId);

        $templates = $templateValue['email']['templates'] ?? [];

        return StrHelper::languageContent($templates, $langTag);
    }
}

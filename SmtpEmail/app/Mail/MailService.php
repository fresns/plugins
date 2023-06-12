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
    /**
     * generate mail code.
     *
     * @param  $account
     * @param  $templateId
     * @return array|string[]
     */
    public static function makeMailCode($account, $templateId)
    {
        try {
            $model = new VerifyCode();
            $code = StrHelper::generateDigital();
            $expired = date('Y-m-d H:i:s', time() + 300);
            $data = [
                'type'          => 1,
                'account'       => $account,
                'template_id'   => $templateId,
                'code'          => $code,
                'expired_at'    => $expired,
            ];
            $id = $model->insert($data);

            return $id ? ['code'=>'000000', 'mailCode'=>$code, 'expired'=>$expired] : ['code'=>'51000', 'message'=>'insert error'];
        } catch (\Error $error) {
            return ['code'=>'500000', 'message'=>$error->getMessage()];
        }
    }

    // replace config from db setting
    public static function initMailSetting()
    {
        $host = ConfigHelper::fresnsConfigByItemKey('fresnsemail_smtp_host');
        $port = ConfigHelper::fresnsConfigByItemKey('fresnsemail_smtp_port') ?? 25;
        $user = ConfigHelper::fresnsConfigByItemKey('fresnsemail_smtp_username');
        $pass = ConfigHelper::fresnsConfigByItemKey('fresnsemail_smtp_password');
        $type = ConfigHelper::fresnsConfigByItemKey('fresnsemail_verify_type');
        $from_name = ConfigHelper::fresnsConfigByItemKey('fresnsemail_from_name');
        $from_addr = ConfigHelper::fresnsConfigByItemKey('fresnsemail_from_mail');
        $smtp = [
            'transport' => 'smtp',
            'host' => $host,
            'port' => (int) $port,
            'username' => $user,
            'password' => $pass,
            'encryption' => $type,
            'timeout' => null,
            'auth_mode' => null,
        ];
        $from = [
            'name' => $from_name,
            'address' => $from_addr,
        ];
        config(['mail.mailers.smtp'  =>  array_merge(config('mail.mailers.smtp'), $smtp)]);
        config(['mail.from'  =>  array_merge(config('mail.from'), $from)]);
    }

    /**
     * Get code template content.
     *
     * @param  $templateId
     * @param  $langTag
     * @return array|mixed
     */
    public static function getTemplateValue($templateId, $langTag)
    {
        $templateValue = ConfigHelper::fresnsConfigByItemKey('verifycode_template'.$templateId);
        if ($templateValue) {
            foreach ($templateValue as $template) {
                if ($template['type'] == 'email' && $template['isEnabled']) {
                    foreach ($template['template'] as $tmp) {
                        if ($tmp['langTag'] == $langTag) {
                            return $tmp;
                        }
                    }
                }
            }
        }

        return [];
    }

    /**
     * get preg_replace title.
     *
     * @param  $title
     * @param  $sitename
     * @return string|string[]|null
     */
    public static function getTitle($title, $sitename)
    {
        return preg_replace('/\{sitename\}/', $sitename, $title);
    }

    /**
     * get preg_replace content.
     *
     * @param  $content
     * @param  $code
     * @param  $time
     * @return string|string[]|null
     */
    public static function getContent($content, $sitename, $code, $time)
    {
        $patterns = ['/\{sitename\}/', '/\{code\}/', '/\{time\}/'];
        $replace = [$sitename, $code, $time];

        return preg_replace($patterns, $replace, $content);
    }
}

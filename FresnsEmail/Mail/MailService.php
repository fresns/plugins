<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\FresnsEmail\Mail;

use App\Helpers\StrHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;
use App\Http\FresnsDb\FresnsVerifyCodes\FresnsVerifyCodes;

class MailService
{
    /**
     * generate mail code.
     *
     * @param $account
     * @param $templateId
     * @return array|string[]
     */
    public static function makeMailCode($account, $templateId)
    {
        try {
            $model = new FresnsVerifyCodes();
            $code = StrHelper::randSmsCode();
            $expired = date('Y-m-d H:i:s', time() + 300);
            $data = [
                'type'          => 1,
                'account'       => $account,
                'template_id'   => $templateId,
                'code'          => $code,
                'expired_at'    => $expired,
            ];
            $id = $model->store($data);

            return $id ? ['code'=>'0', 'mailCode'=>$code, 'expired'=>$expired] : ['code'=>'51000', 'message'=>'insert error'];
        } catch (\Error $error) {
            return ['code'=>'50000', 'message'=>$error->getMessage()];
        }
    }

    // replace config from db setting
    public static function initMailSetting()
    {
        $host = ApiConfigHelper::getConfigByItemKey('fresnsemail_smtp_host');
        $port = ApiConfigHelper::getConfigByItemKey('fresnsemail_smtp_port');
        $user = ApiConfigHelper::getConfigByItemKey('fresnsemail_smtp_user');
        $pass = ApiConfigHelper::getConfigByItemKey('fresnsemail_smtp_password');
        $type = ApiConfigHelper::getConfigByItemKey('fresnsemail_verify_type');
        $from_name = ApiConfigHelper::getConfigByItemKey('send_email_from_name');
        $from_addr = ApiConfigHelper::getConfigByItemKey('send_email_from_mail');
        $smtp = [
            'transport' => 'smtp',
            'host' => $host,
            'port' => $port,
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
     * @param $templateId
     * @param $langTag
     * @return array|mixed
     */
    public static function getTemplateValue($templateId, $langTag)
    {
        $templateValue = ApiConfigHelper::getConfigByItemKey('verifycode_template'.$templateId);
        $templateValue = json_decode($templateValue, true);
        if ($templateValue) {
            foreach ($templateValue as $template) {
                if ($template['type'] == 'email' && $template['isEnable']) {
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
     * @param $title
     * @param $sitename
     * @return string|string[]|null
     */
    public static function getTitle($title, $sitename)
    {
        return preg_replace('/\{sitename\}/', $sitename, $title);
    }

    /**
     * get preg_replace content.
     *
     * @param $content
     * @param $code
     * @param $time
     * @return string|string[]|null
     */
    public static function getContent($content, $sitename, $code, $time)
    {
        $patterns = ['/\{sitename\}/', '/\{code\}/', '/\{time\}/'];
        $replace = [$sitename, $code, $time];

        return preg_replace($patterns, $replace, $content);
    }
}

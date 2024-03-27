<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SmtpEmail\Mail;

use App\Helpers\ConfigHelper;
use App\Helpers\DateHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailCmdService
{
    public function sendCode($wordBody)
    {
        try {
            $codeRes = MailService::makeMailCode($wordBody['account'], $wordBody['templateId']);
            if ($codeRes['code'] != 0) {
                return [
                    'code' => 10001,
                    'message' => 'Data does not exist',
                    'data' => [],
                ];
            }

            $template = MailService::getTemplateValue($wordBody['templateId'], $wordBody['langTag']);

            if (empty($template) || empty($template['title']) || empty($template['content'])) {
                return [
                    'code' => 10001,
                    'message' => 'Data does not exist',
                    'data' => [],
                ];
            }

            $siteLogo = ConfigHelper::fresnsConfigFileUrlByItemKey('site_logo');
            $siteIcon = ConfigHelper::fresnsConfigFileUrlByItemKey('site_icon');
            $siteName = ConfigHelper::fresnsConfigByItemKey('site_name', $wordBody['langTag']);
            $code = $codeRes['data']['emailCode'];
            $time = DateHelper::fresnsFormatConversion(now(), $wordBody['langTag']);

            $title = Str::replace('{name}', $siteName, $template['title']);
            $title = Str::replace('{code}', $code, $title);
            $title = Str::replace('{time}', $time, $title);

            $content = Str::replace('{logo}', $siteLogo, $template['content']);
            $content = Str::replace('{icon}', $siteIcon, $content);
            $content = Str::replace('{name}', $siteName, $content);
            $content = Str::replace('{code}', $code, $content);
            $content = Str::replace('{time}', $time, $content);

            MailService::initMailSetting();

            Mail::to($wordBody['account'])->send(new MailSend($title, $content));

            return [
                'code' => 0,
                'message' => 'ok',
                'data' => [],
            ];
        } catch (\Error $error) {
            return [
                'code' => 10000,
                'message' => $error->getMessage(),
                'data' => [],
            ];
        }
    }

    public function sendEmail($wordBody)
    {
        try {
            $siteLogo = ConfigHelper::fresnsConfigFileUrlByItemKey('site_logo');
            $siteIcon = ConfigHelper::fresnsConfigFileUrlByItemKey('site_icon');
            $siteName = ConfigHelper::fresnsConfigByItemKey('site_name', $wordBody['langTag']);
            $time = DateHelper::fresnsFormatConversion(now(), $wordBody['langTag']);

            $title = Str::replace('{name}', $siteName, $wordBody['title']);
            $title = Str::replace('{time}', $time, $title);

            $content = Str::replace('{logo}', $siteLogo, $wordBody['content']);
            $content = Str::replace('{icon}', $siteIcon, $content);
            $content = Str::replace('{name}', $siteName, $content);
            $content = Str::replace('{time}', $time, $content);

            MailService::initMailSetting();

            Mail::to($wordBody['email'])->send(new MailSend($title, $content));

            return [
                'code' => 0,
                'message' => 'ok',
                'data' => [],
            ];
        } catch (\Error $error) {
            return [
                'code' => 10000,
                'message' => $error->getMessage(),
                'data' => [],
            ];
        }
    }
}

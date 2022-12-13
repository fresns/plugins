<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SmtpEmail\Mail;

use App\Helpers\ConfigHelper;
use App\Helpers\DateHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailCmdService
{
    public function sendCode($input)
    {
        try {
            $codeRes = MailService::makeMailCode($input['account'], $input['templateId']);
            if ($codeRes['code'] != '000000') {
                return ['code' => 1002, 'message' => 'Data does not exist', 'data' => []];
            }
            $code = $codeRes['mailCode'];
            $sitename = ConfigHelper::fresnsConfigByItemKey('site_name', $input['langTag']);
            $template = MailService::getTemplateValue($input['templateId'], $input['langTag']);
            if (empty($template) || empty($template['title']) || empty($template['content'])) {
                return ['code' => 1002, 'message' => 'Data does not exist', 'data' => []];
            }

            $datetime = date('Y-m-d H:i:s', time());
            $time = DateHelper::fresnsDateTimeByTimezone($datetime, null, $input['langTag']);

            $title = MailService::getTitle($template['title'], $sitename);
            $content = MailService::getContent($template['content'], $sitename, $code, $time);

            Log::info('Email Params: ', [$input, $title, $content]);

            MailService::initMailSetting();
            Mail::to($input['account'])->send(new MailSend($title, $content));

            return ['code' => 0, 'message' => 'ok', 'data' => []];
        } catch (\Error $error) {
            return ['code' => 50000, 'message' => $error->getMessage(), 'data' => []];
        }
    }

    public function sendEmail($input)
    {
        try {
            Log::info('Email Params: ', $input);

            MailService::initMailSetting();
            Mail::to($input['email'])->send(new MailSend($input['title'], $input['content']));

            return ['code' => 0, 'message' => 'ok', 'data' => []];
        } catch (\Error $error) {
            return ['code' => 50000, 'message' => $error->getMessage(), 'data' => []];
        }
    }
}

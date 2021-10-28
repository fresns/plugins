<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\FresnsEmail;

use App\Http\Center\Base\BasePlugin;
use App\Http\Center\Base\BasePluginConfig;
use App\Http\Center\Common\LogService;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguagesService;
use App\Plugins\FresnsEmail\Mail\MailSend;
use App\Plugins\FresnsEmail\Mail\MailService;
use Illuminate\Support\Facades\Mail;

class Plugin extends BasePlugin
{
    // Constructors
    public function __construct()
    {
        $this->pluginConfig = new PluginConfig();
        $this->pluginCmdHandlerMap = PluginConfig::FRESNS_CMD_HANDLE_MAP;
    }

    // Get Error Code
    public function getCodeMap()
    {
        return PluginConfig::CODE_MAP;
    }

    /**
     * Send Verification Code.
     *
     * @param $input
     *  type
     *  account
     *  templateId
     *  langTag
     * @return array
     */
    protected function sendCodeHandler($input)
    {
        try {
            $codeRes = MailService::makeMailCode($input['account'], $input['templateId']);
            if ($codeRes['code'] != '000000') {
                return $this->pluginError(BasePluginConfig::CODE_NOT_EXIST);
            }
            $code = $codeRes['mailCode'];
            $expired = $codeRes['expired'];
            $sitename = FresnsLanguagesService::getLanguageByTableKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', 'site_name', $input['langTag']);
            $template = MailService::getTemplateValue($input['templateId'], $input['langTag']);
            if (empty($template) || empty($template['title']) || empty($template['content'])) {
                return $this->pluginError(BasePluginConfig::CODE_NOT_EXIST);
            }

            $title = MailService::getTitle($template['title'], $sitename);
            $content = MailService::getContent($template['content'], $sitename, $code, $expired);

            LogService::info('Email Params: ', [$input, $title, $content]);

            MailService::initMailSetting();
            Mail::to($input['account'])->send(new MailSend($title, $content));

            return $this->pluginSuccess();
        } catch (\Error $error) {
            return $this->pluginError(50000, [], $error->getMessage());
        }
    }

    /**
     * Send email.
     *
     * @param $input
     *  email
     *  title
     *  content
     * @return array
     */
    public function sendEmailHandler($input)
    {
        try {
            LogService::info('Email Params: ', $input);

            MailService::initMailSetting();
            Mail::to($input['email'])->send(new MailSend($input['title'], $input['content']));

            return $this->pluginSuccess();
        } catch (\Error $error) {
            return $this->pluginError(50000, [], $error->getMessage());
        }
    }
}

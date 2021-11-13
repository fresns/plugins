<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\AqSms;

use App\Http\Center\Base\BasePlugin;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Plugin extends BasePlugin
{
    public $service;

    public function __construct()
    {
        $this->pluginConfig = new PluginConfig();
        $this->pluginCmdHandlerMap = PluginConfig::PLG_CMD_HANDLE_MAP;
    }

    public function getCodeMap()
    {
        return PluginConfig::CODE_MAP;
    }

    // 发送验证码命令字
    public function sendCodeHandler($input)
    {
        $type = $input['type'];
        if ($type == 1) {
            return $this->pluginError(PluginConfig::SEND_TYPE_ERROR);
        }
        $countryCode = $input['countryCode'];
        $templateId = $input['templateId'];
        $langTag = $input['langTag'];
        // 根据传参国际区号去匹配模板语言标签
        $langTag = $this->getLangTagByContryCode($countryCode);
        $data = $this->getCodeTeamplate($templateId, $langTag);
        // 未找到模板
        if (empty($data)) {
            return $this->pluginError(PluginConfig::TEMPLATE_ERROR);
        }
        $appid = ApiConfigHelper::getConfigByItemKey('aqsms_appid');
        $keyId = ApiConfigHelper::getConfigByItemKey('aqsms_keyid');
        $keySecret = ApiConfigHelper::getConfigByItemKey('aqsms_keysecret');
        // 缺少配置信息
        if (empty($keyId) || empty($keySecret)) {
            return $this->pluginError(PluginConfig::CONFIG_ERROR);
        }
        $data['countryCode'] = $countryCode;
        $data['account'] = $input['account'];
        $data['codeSms'] = rand(100000, 999999);
        $data['appid'] = $appid;
        $data['keyId'] = $keyId;
        $data['keySecret'] = $keySecret;
        $aqSmsType = ApiConfigHelper::getConfigByItemKey('aqsms_type');
        if ($aqSmsType == 1) {
            // 发送阿里云短信
            $this->service = new AliSmsService();
            $date = $this->service->sendCodeSms($data);
            // 未发送成功
            if ($date != 'OK') {
                return $this->pluginError(PluginConfig::SEND_ERROR);
            }
        } else {
            // 发送腾讯云短信
            $this->service = new TencentSmsService();
            $date = $this->service->sendCodeSms($data);
            // 未发送成功
            if ($date != 'Ok') {
                return $this->pluginError(PluginConfig::SEND_ERROR);
            }
        }
        if (! $date) {
            return $this->pluginError(PluginConfig::CODE_PARAM_ERROR);
        }
        // 数据库插入验证码
        $input = [
            'account'     => $countryCode.$data['account'],
            'template_id'     => $templateId,
            'type'  => 2,
            'code'  => $data['codeSms'],
            'expired_at' => date('Y-m-d H:i:s', time() + (60 * 10)),
        ];
        DB::table('verify_codes')->insert($input);

        return $this->pluginSuccess($date);
    }

    // 自定义发信命令字
    public function sendSmsHandler($input)
    {
        $countryCode = $input['countryCode'];
        $phoneNumber = $input['phoneNumber'];
        $signName = $input['signName'];
        $templateCode = $input['templateCode'];
        $templateParam = $input['templateParam'];
        $aqSmsType = ApiConfigHelper::getConfigByItemKey('aqsms_type');
        $appid = ApiConfigHelper::getConfigByItemKey('aqsms_appid');
        $keyId = ApiConfigHelper::getConfigByItemKey('aqsms_keyid');
        $keySecret = ApiConfigHelper::getConfigByItemKey('aqsms_keysecret');
        $data = [];
        $data['countryCode'] = $countryCode;
        $data['phoneNumber'] = $phoneNumber;
        $data['signName'] = $signName;
        $data['templateCode'] = $templateCode;
        $data['templateParam'] = $templateParam;
        $data['appid'] = $appid;
        $data['keyId'] = $keyId;
        $data['keySecret'] = $keySecret;
        // 缺少配置信息
        if (empty($aqSmsType) || empty($keyId) || empty($keySecret)) {
            return $this->pluginError(PluginConfig::CONFIG_ERROR);
        }
        if ($aqSmsType == 1) {
            // 发送阿里云短信
            $this->service = new AliSmsService();
            $date = $this->service->sendSms($data);
            // 未发送成功
            if ($date != 'OK') {
                return $this->pluginError(PluginConfig::SEND_ERROR);
            }
        } else {
            // 发送腾讯云短信
            $this->service = new TencentSmsService();
            $date = $this->service->sendSms($data);
            // 未发送成功
            if ($date != 'Ok') {
                return $this->pluginError(PluginConfig::SEND_ERROR);
            }
        }
        if (! $date) {
            return $this->pluginError(PluginConfig::CODE_PARAM_ERROR);
        }

        return $this->pluginSuccess($date);
    }

    // 根据传参国际区号去匹配模板语言标签
    public function getLangTagByContryCode($contryCode)
    {
        $aqsmsLinked = ApiConfigHelper::getConfigByItemKey('aqsms_linked');
        $aqsmsLinkedArr = json_decode($aqsmsLinked, true);
        $langTag = $aqsmsLinkedArr['other'];
        if ($contryCode) {
            foreach ($aqsmsLinkedArr as $key => $value) {
                if ($key == $contryCode) {
                    $langTag = $aqsmsLinkedArr[$key];
                }
            }
        }

        return $langTag;
    }

    // 根据 teamplateId 和 langTag 匹配需要发信的验证码模板
    public function getCodeTeamplate($templateId, $langTag)
    {
        $templateBlade = ApiConfigHelper::getConfigByItemKey('verifycode_template'.$templateId);
        $templateData = json_decode($templateBlade, true);
        $sms = [];
        if ($templateData) {
            foreach ($templateData as $t) {
                if ($t['type'] == 'sms') {
                    $sms = $t['template'];
                }
            }
        }
        $data = [];
        if ($sms) {
            foreach ($sms as $s) {
                if ($s['langTag'] == $langTag) {
                    $data['signName'] = $s['signName'];
                    $data['templateCode'] = $s['templateCode'];
                    $data['codeParam'] = $s['codeParam'];
                }
            }
        }

        return $data;
    }
}

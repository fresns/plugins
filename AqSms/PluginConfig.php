<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\AqSms;

use App\Http\Center\Base\BasePluginConfig;

class PluginConfig extends BasePluginConfig
{
    public $type = 2; //1.网站引擎 2.扩展插件 3.移动应用 4.控制面板 5.主题模板
    public $uniKey = 'AqSms';
    public $name = '阿 Q 短信插件';
    public $description = 'Fresns 官方开发的「阿里云」和「腾讯云」二合一短信服务插件。';
    public $author = 'Fresns';
    public $authorLink = 'https://fresns.org';
    public $currVersion = '1.1.0';
    public $currVersionInt = 2;
    public $settingPath = '/aqsms/setting';
    public $sceneArr = [
        'sms', // 短信服务商
    ];

    // 默认命令字功能同「自定义发信」一样
    public const FRESNS_CMD_DEFAULT = 'fresns_cmd_default';
    // 发送验证码
    public const FRESNS_CMD_SEND_CODE = 'fresns_cmd_send_code';
    // 自定义发信
    public const FRESNS_CMD_SEND_SMS = 'fresns_cmd_send_sms';

    // 插件命令字回调映射
    const PLG_CMD_HANDLE_MAP = [
        self::FRESNS_CMD_DEFAULT => 'sendSmsHandler',
        self::FRESNS_CMD_SEND_CODE => 'sendCodeHandler',
        self::FRESNS_CMD_SEND_SMS => 'sendSmsHandler',
    ];
}

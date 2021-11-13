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
    public $currVersion = '1.2.0';
    public $currVersionInt = 3;
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

    // 插件状态码
    const OK = 0;
    const FAIL = 1001;
    const CODE_PARAMS_ERROR = 1002;
    const SEND_TYPE_ERROR = 1003;
    const TEMPLATE_ERROR = 1004;
    const CONFIG_ERROR = 1005;
    const SEND_ERROR = 1006;

    // 插件状态码映射
    const CODE_MAP = [
        self::OK => 'ok',
        self::FAIL => '处理失败',
        self::CODE_PARAMS_ERROR => '参数错误',
        self::SEND_TYPE_ERROR => '服务商不支持邮件发信',
        self::TEMPLATE_ERROR => '未找到短信模板',
        self::CONFIG_ERROR => '缺少服务商配置信息',
        self::SEND_ERROR => '请求服务商发信失败',
    ];
}

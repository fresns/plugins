<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\AqSms;

use App\Http\Center\Base\BaseInstaller;

class Installer extends BaseInstaller
{
    protected $pluginConfig;

    // 初始化
    public function __construct()
    {
        $this->pluginConfig = new PluginConfig();
    }

    // 插件安装
    public function install()
    {
        parent::install();
        // 插入字段
        AqSmsHelper::insertConfigs('aqsms_type', '1', 'number');
        AqSmsHelper::insertConfigs('aqsms_keyid');
        AqSmsHelper::insertConfigs('aqsms_keysecret');
        AqSmsHelper::insertConfigs('aqsms_appid');
        AqSmsHelper::insertConfigs('aqsms_linked', '{"86":"zh-Hans","other":"en"}', 'object');
    }

    // 插件升级
    public function upgrade()
    {
        //code
    }

    // 插件卸载
    public function uninstall()
    {
        $request = request();
        $clear_plugin_data = $request->input('clear_plugin_data');
        // 如果 clear_plugin_data 为 1 则删除插件的数据
        if ($clear_plugin_data == 1) {
            AqSmsHelper::deleteConfigItem('aqsms_type');
            AqSmsHelper::deleteConfigItem('aqsms_keyid');
            AqSmsHelper::deleteConfigItem('aqsms_keysecret');
            AqSmsHelper::deleteConfigItem('aqsms_appid');
            AqSmsHelper::deleteConfigItem('aqsms_linked');
        }

        parent::uninstall();
    }
}

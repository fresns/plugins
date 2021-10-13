<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\QiNiu;

use App\Http\Center\Base\BaseInstaller;
use App\Http\FresnsCmd\FresnsCrontabPluginConfig;
use App\Http\FresnsCmd\FresnsCrontabPlugin;
use App\Http\Center\Helper\CmdRpcHelper;

class Installer extends BaseInstaller
{
    protected $pluginConfig;

    public function __construct(){
        $this->pluginConfig = new PluginConfig();
    }

    // 插件安装
    public function install()
    {
        parent::install();
        // 建立订阅 add_sub_plugin_item
        // 订阅发表命令字，当命令字完成内容发表后，通知我检查是否有文件需要转码
        $cmd = FresnsCrontabPluginConfig::ADD_SUB_PLUGIN_ITEM;

        $sub_table_plugin_item = [
            'subscribe_type' => 5,
            'subscribe_plugin_unikey' => 'QiNiu',
            'subscribe_plugin_cmd' => 'fresns_cmd_qiniu_transcoding',
            'subscribe_command_word' => 'fresns_cmd_direct_release_content',
        ];
        $input['sub_table_plugin_item'] = $sub_table_plugin_item;
        CmdRpcHelper::call(FresnsCrontabPlugin::class, $cmd, $input);
    }

    /// 插件卸载
    public function uninstall()
    {
        // 取消订阅 delete_sub_plugin_item
        $cmd = FresnsCrontabPluginConfig::DELETE_SUB_PLUGIN_ITEM;

        $sub_table_plugin_item = [
            'subscribe_type' => 5,
            'subscribe_plugin_unikey' => 'QiNiu',
        ];
        $input['sub_table_plugin_item'] = $sub_table_plugin_item;
        CmdRpcHelper::call(FresnsCrontabPlugin::class, $cmd, $input);
        parent::uninstall();
    }
}
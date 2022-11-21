<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Support;

use App\Fresns\Subscribe\Subscribe;
use App\Models\FileUsage;

class Installer
{
    protected $subscribes = [
        [
            'type' => Subscribe::SUBSCRIBE_TYPE_TABLE_DATA_CHANGE,
            'unikey' => 'QiNiu',
            'cmdWord' => 'audioVideoTranscoding',
            'subTableName' => FileUsage::class,
        ],
    ];

    public function handleSubscribes(callable $callable)
    {
        foreach ($this->subscribes as $subscribe) {
            $callable($subscribe);
        }
    }

    // 插件安装
    public function install()
    {
        try {
            $this->handleSubscribes(fn ($subscribe) => \FresnsCmdWord::plugin()->addSubscribeItem($subscribe));
        } catch (\Throwable $e) {
            \info('add config fail: '.$e->getMessage());
            throw $e;
        }
    }

    /// 插件卸载
    public function uninstall()
    {
        try {
            $this->handleSubscribes(fn ($subscribe) => \FresnsCmdWord::plugin()->deleteSubscribeItem($subscribe));
        } catch (\Throwable $e) {
            \info('remove config fail: '.$e->getMessage());
            throw $e;
        }
    }
}

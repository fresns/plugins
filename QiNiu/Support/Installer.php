<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Support;

use App\Models\Post;
use App\Models\Comment;
use App\Fresns\Subscribe\Subscribe;
use App\Fresns\Api\Center\Base\BaseInstaller;

class Installer extends BaseInstaller
{

    protected $subscribes = [
        [
            'type' => Subscribe::SUBSCRIBE_TYPE_TABLE_DATA_CHANGE,
            'unikey' => 'QiNiu',
            'cmdWord' => 'notifyAudioVideoTranscoding', 
            'subTableName' => Post::class,
        ],
        [
            'type' => Subscribe::SUBSCRIBE_TYPE_TABLE_DATA_CHANGE,
            'unikey' => 'QiNiu',
            'cmdWord' => 'notifyAudioVideoTranscoding',
            'subTableName' => Comment::class,
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
        $this->handleSubscribes(fn ($subscribe) => \FresnsCmdWord::plugin()->addSubscribeItem($subscribe));
    }

    /// 插件卸载
    public function uninstall()
    {
        $this->handleSubscribes(fn ($subscribe) => \FresnsCmdWord::plugin()->deleteSubscribeItem($subscribe));
    }
}

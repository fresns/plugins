<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\Support;

use Plugins\EasySms\Models\Config;

class Installer
{
    protected $fresnsConfigItems = [
        [
            'item_key' => 'easysms_type',
            'item_value' => '1',
            'item_type' => 'number',
            'item_tag' => 'easysms',
        ],
        [
            'item_key' => 'easysms_keyid',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'easysms',
        ],
        [
            'item_key' => 'easysms_keysecret',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'easysms',
        ],
        [
            'item_key' => 'easysms_sdk_appid',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'easysms',
        ],
        [
            'item_key' => 'easysms_linked',
            'item_value' => [
                '86' => 'zh-Hans',
                'other' => 'en',
            ],
            'item_type' => 'object',
            'item_tag' => 'easysms',
        ],
    ];

    protected function process(callable $callback)
    {
        foreach ($this->fresnsConfigItems as $item) {
            $callback($item);
        }
    }

    public function install()
    {
        $this->process(function ($item) {
            Config::firstOrCreate([
                'item_key' => $item['item_key'],
            ], $item);
        });
    }

    public function uninstall(bool $clearPluginData = false)
    {
        if (! $clearPluginData) {
            return;
        }

        $this->process(function ($item) {
            Config::query()->where('item_key', $item['item_key'])->forceDelete();
        });
    }
}

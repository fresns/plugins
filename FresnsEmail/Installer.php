<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\FresnsEmail;

use App\Http\Center\Base\BaseInstaller;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;

class Installer extends BaseInstaller
{
    protected $pluginConfig;

    protected $installItemKey = [
        'fresnsemail_smtp_host',
        'fresnsemail_smtp_port',
        'fresnsemail_smtp_user',
        'fresnsemail_smtp_password',
        'fresnsemail_verify_type',
        'fresnsemail_from_mail',
        'fresnsemail_from_name',
    ];

    // Initialization
    public function __construct()
    {
        $this->pluginConfig = new PluginConfig();
    }

    /**
     * @throws \Throwable
     */
    public function install()
    {
        $this->installItemKey();
    }

    /**
     * uninstall, example:execute some sql delete
     */
    public function uninstall()
    {
        $request = request();
        $clear_plugin_data = $request->input('clear_plugin_data');
        if ($clear_plugin_data == 1) {
            $this->deletePluginItemKey();
        }

        parent::uninstall();
    }

    /**
     * @throws \Throwable
     */
    public function installItemKey(): void
    {
        collect($this->installItemKey)->filter(function (string $value) {
            return ! FresnsConfigs::query()->where('item_key', $value)->exists();
        })->each(function (string $value) {
            $fresnsConfigs = FresnsConfigs::query()->newModelInstance();
            $fresnsConfigs->item_key = $value;
            $fresnsConfigs->item_value = '';
            $fresnsConfigs->item_type = 'string';
            $fresnsConfigs->item_tag = 'fresnsemail';
            $fresnsConfigs->saveOrFail();
        });
    }

    public function deletePluginItemKey():void
    {
        FresnsConfigs::query()->whereIn('item_key', $this->installItemKey)->delete();
    }
}

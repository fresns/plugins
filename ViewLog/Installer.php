<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\ViewLog;

use App\Http\Center\Base\BaseInstaller;

class Installer extends BaseInstaller
{
    protected $pluginConfig;

    // Initialization
    public function __construct()
    {
        $this->pluginConfig = new PluginConfig();
    }
}

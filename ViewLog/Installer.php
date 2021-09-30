<?php

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

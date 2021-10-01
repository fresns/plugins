<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\ViewLog;

use App\Http\Center\Base\BasePluginConfig;

class PluginConfig extends BasePluginConfig
{
    public $type = 2;
    public $uniKey = 'ViewLog';
    public $name = 'View Log';
    public $description = 'Laravel Log Viewer';
    public $author = 'Fresns';
    public $authorLink = 'https://fresns.org';
    public $currVersion = '1.0';
    public $currVersionInt = 1;
    public $settingPath = '/viewLog';
}

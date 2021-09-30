<?php

namespace App\Plugins\ViewLog;

use App\Http\Center\Base\BasePluginConfig;

class PluginConfig extends BasePluginConfig
{
    public $type = 2;
    public $uniKey = "ViewLog";
    public $name = 'View Log';
    public $description = "Laravel Log Viewer";
    public $author = "Fresns";
    public $authorLink = "https://fresns.org";
    public $currVersion = '1.0';
    public $currVersionInt = 1;
    public $settingPath = "/viewLog";

    // Plugin default command word, any plugin must have
    public CONST PLG_CMD_DEFAULT = 'plg_cmd_default';

    // Plugin command word callback mapping
    CONST PLG_CMD_HANDLE_MAP = [
        self::PLG_CMD_DEFAULT => 'defaultHandler',
    ];
}
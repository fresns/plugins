<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\FresnsEmail;

use App\Http\Center\Base\BasePluginConfig;

class PluginConfig extends BasePluginConfig
{
    /**
     * $type.
     *
     * @param number
     * 1.Website Engine
     * 2.Extension Plugin
     * 3.App Management
     * 4.Control Panel
     * 5.Theme Template
     */
    public $type = 2;
    public $uniKey = 'FresnsEmail';
    public $name = 'Fresns Email';
    public $description = 'Fresns official development of the SMTP sending method of mail plugin.';
    public $author = 'Fresns';
    public $authorLink = 'https://fresns.org';
    public $currVersion = '1.0.0';
    public $currVersionInt = 1;
    public $settingPath = '/fresnsemail/settings';
    public $sceneArr = [
        'email', // Email Service Provider
    ];

    // Default command word
    public const PLG_CMD_DEFAULT = 'plg_cmd_default';
    // Send verify code
    public const PLG_CMD_SEND_CODE = 'plg_cmd_send_code';
    // Customize sending emails
    public const PLG_CMD_SEND_EMAIL = 'plg_cmd_send_email';

    // Command word callback mapping
    const PLG_CMD_HANDLE_MAP = [
        self::PLG_CMD_DEFAULT => 'sendEmailHandler',
        self::PLG_CMD_SEND_CODE => 'sendCodeHandler',
        self::PLG_CMD_SEND_EMAIL => 'sendEmailHandler',
    ];
}

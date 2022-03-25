<?php

namespace Plugins\FresnsEmail\Support;

use App\Utilities\ConfigUtility;

class Installer
{
    protected $fresnsConfigItems = [
        [
            'item_key' => 'fresnsemail_smtp_host',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'fresnsemail',
            'is_multilingual' => 0,
            'is_restful' => 0,
        ],
        [
            'item_key' => 'fresnsemail_smtp_port',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'fresnsemail',
            'is_multilingual' => 0,
            'is_restful' => 0,
        ],
        [
            'item_key' => 'fresnsemail_smtp_user',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'fresnsemail',
            'is_multilingual' => 0,
            'is_restful' => 0,
        ],
        [
            'item_key' => 'fresnsemail_smtp_password',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'fresnsemail',
            'is_multilingual' => 0,
            'is_restful' => 0,
        ],
        [
            'item_key' => 'fresnsemail_verify_type',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'fresnsemail',
            'is_multilingual' => 0,
            'is_restful' => 0,
        ],
        [
            'item_key' => 'fresnsemail_from_mail',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'fresnsemail',
            'is_multilingual' => 0,
            'is_restful' => 0,
        ],
        [
            'item_key' => 'fresnsemail_from_name',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'fresnsemail',
            'is_multilingual' => 0,
            'is_restful' => 0,
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
        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);
    }

    public function uninstall(bool $clearPluginData = false)
    {
        if (!$clearPluginData) {
            return;
        }

        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);

    }
}

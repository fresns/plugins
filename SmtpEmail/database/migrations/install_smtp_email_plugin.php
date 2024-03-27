<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\ConfigUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $fresnsConfigItems = [
        [
            'item_key' => 'fresnsemail_smtp_host',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'fresnsemail_smtp_port',
            'item_value' => '',
            'item_type' => 'number',
        ],
        [
            'item_key' => 'fresnsemail_smtp_username',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'fresnsemail_smtp_password',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'fresnsemail_verify_type',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'fresnsemail_from_mail',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'fresnsemail_from_name',
            'item_value' => '',
            'item_type' => 'string',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);
    }
};

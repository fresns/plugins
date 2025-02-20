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
            'item_key' => 'amazon_ses_access_key_id',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'amazon_ses_secret_access_key',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'amazon_ses_region',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'amazon_ses_from_mail',
            'item_value' => '',
            'item_type' => 'string',
        ],
        [
            'item_key' => 'amazon_ses_from_name',
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

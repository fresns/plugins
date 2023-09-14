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
            'item_key' => 'wechatlogin_official_account',
            'item_value' => null,
            'item_type' => 'object',
            'item_tag' => 'wechatlogin',
        ], [
            'item_key' => 'wechatlogin_mini_program',
            'item_value' => null,
            'item_type' => 'object',
            'item_tag' => 'wechatlogin',
        ], [
            'item_key' => 'wechatlogin_open_platform',
            'item_value' => null,
            'item_type' => 'object',
            'item_tag' => 'wechatlogin',
        ], [
            'item_key' => 'wechatlogin_mini_app',
            'item_value' => null,
            'item_type' => 'object',
            'item_tag' => 'wechatlogin',
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

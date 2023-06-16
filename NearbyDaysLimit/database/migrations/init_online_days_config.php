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
            'item_key' => 'nearby_days_limit',
            'item_value' => null,
            'item_type' => 'number',
            'item_tag' => 'NearbyDaysLimit',
        ],
    ];

    // Run the migrations.
    public function up(): void
    {
        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);
    }

    // Reverse the migrations.
    public function down(): void
    {
        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);
    }
};

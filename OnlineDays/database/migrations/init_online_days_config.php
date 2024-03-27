<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\ConfigUtility;
use App\Utilities\SubscribeUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $fresnsWordBody = [
        'type' => SubscribeUtility::TYPE_USER_ACTIVITY,
        'fskey' => 'OnlineDays',
        'cmdWord' => 'stats',
    ];

    protected $fresnsConfigItems = [
        [
            'item_key' => 'online_days_extcredits_id',
            'item_value' => null,
            'item_type' => 'number',
        ],
    ];

    // Run the migrations.
    public function up(): void
    {
        \FresnsCmdWord::plugin()->addSubscribeItem($this->fresnsWordBody);

        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);
    }

    // Reverse the migrations.
    public function down(): void
    {
        \FresnsCmdWord::plugin()->removeSubscribeItem($this->fresnsWordBody);

        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);
    }
};

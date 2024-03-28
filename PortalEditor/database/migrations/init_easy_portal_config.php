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
    protected $fresnsWordBody = [
        'fskey' => 'PortalEditor',
        'cmdWord' => 'generateContent',
        'cronTableFormat' => '* */1 * * *',
    ];

    protected $fresnsConfigItems = [
        [
            'item_key' => 'portal_editor_auto',
            'item_value' => 'true',
            'item_type' => 'boolean',
        ],
    ];

    // Run the migrations.
    public function up(): void
    {
        \FresnsCmdWord::plugin()->addCrontabItem($this->fresnsWordBody);

        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);
    }

    // Reverse the migrations.
    public function down(): void
    {
        \FresnsCmdWord::plugin()->removeCrontabItem($this->fresnsWordBody);

        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);
    }
};

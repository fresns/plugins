<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Operation;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $fresnsOperationItems = [
        [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'success',
            'name' => 'Available',
            'image_file_url' => '/assets/plugins/TitleIcons/operations/available.png',
            'plugin_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'success',
            'name' => 'Completed',
            'image_file_url' => '/assets/plugins/TitleIcons/operations/completed.png',
            'plugin_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'secondary',
            'name' => 'Duplicate',
            'image_file_url' => '/assets/plugins/TitleIcons/operations/duplicate.png',
            'plugin_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'secondary',
            'name' => 'Wontfix',
            'image_file_url' => '/assets/plugins/TitleIcons/operations/wontfix.png',
            'plugin_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'info',
            'name' => 'Invalid',
            'image_file_url' => '/assets/plugins/TitleIcons/operations/invalid.png',
            'plugin_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'primary',
            'name' => 'Planned',
            'image_file_url' => '/assets/plugins/TitleIcons/operations/planned.png',
            'plugin_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'primary',
            'name' => 'In-Progress',
            'image_file_url' => '/assets/plugins/TitleIcons/operations/in-progress.png',
            'plugin_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'warning',
            'name' => 'Reward',
            'image_file_url' => '/assets/plugins/TitleIcons/operations/reward.png',
            'plugin_fskey' => 'TitleIcons',
        ],
    ];

    protected function process(callable $callback)
    {
        foreach ($this->fresnsOperationItems as $item) {
            $callback($item);
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->process(function ($item) {
            Operation::updateOrCreate([
                'name' => $item['name'],
                'plugin_fskey' => $item['plugin_fskey'],
            ], $item);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->process(function ($item) {
            Operation::where('image_file_url', $item['image_file_url'])->forceDelete();
        });
    }
};

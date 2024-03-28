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
            'image_file_url' => '/assets/TitleIcons/operations/available.png',
            'app_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'success',
            'name' => 'Completed',
            'image_file_url' => '/assets/TitleIcons/operations/completed.png',
            'app_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'secondary',
            'name' => 'Duplicate',
            'image_file_url' => '/assets/TitleIcons/operations/duplicate.png',
            'app_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'secondary',
            'name' => 'Wontfix',
            'image_file_url' => '/assets/TitleIcons/operations/wontfix.png',
            'app_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'info',
            'name' => 'Invalid',
            'image_file_url' => '/assets/TitleIcons/operations/invalid.png',
            'app_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'primary',
            'name' => 'Planned',
            'image_file_url' => '/assets/TitleIcons/operations/planned.png',
            'app_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'primary',
            'name' => 'In-Progress',
            'image_file_url' => '/assets/TitleIcons/operations/in-progress.png',
            'app_fskey' => 'TitleIcons',
        ], [
            'type' => Operation::TYPE_DIVERSIFY_IMAGE,
            'code' => 'title',
            'style' => 'warning',
            'name' => 'Reward',
            'image_file_url' => '/assets/TitleIcons/operations/reward.png',
            'app_fskey' => 'TitleIcons',
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
                'app_fskey' => $item['app_fskey'],
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

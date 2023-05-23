<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Comment;
use App\Models\Post;
use App\Utilities\SubscribeUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $subscribes = [
        [
            'type' => SubscribeUtility::TYPE_TABLE_DATA_CHANGE,
            'fskey' => 'SubscribeExample',
            'cmdWord' => 'dataChange',
            'subject' => Post::class,
        ],
        [
            'type' => SubscribeUtility::TYPE_TABLE_DATA_CHANGE,
            'fskey' => 'SubscribeExample',
            'cmdWord' => 'dataChange',
            'subject' => Comment::class,
        ],
        [
            'type' => SubscribeUtility::TYPE_USER_ACTIVITY,
            'fskey' => 'SubscribeExample',
            'cmdWord' => 'userActivity',
        ],
        [
            'type' => SubscribeUtility::TYPE_ACCOUNT_AND_USER_LOGIN,
            'fskey' => 'SubscribeExample',
            'cmdWord' => 'accountAndUserLogin',
        ],
    ];

    public function handleSubscribes(callable $callable)
    {
        foreach ($this->subscribes as $subscribe) {
            $callable($subscribe);
        }
    }

    // Run the migrations.
    public function up(): void
    {
        try {
            $this->handleSubscribes(function ($subscribe) {
                \FresnsCmdWord::plugin()->addSubscribeItem($subscribe);
            });
        } catch (\Throwable $e) {
            \info('FileStorage add config fail: '.$e->getMessage());
        }
    }

    // Reverse the migrations.
    public function down(): void
    {
        try {
            $this->handleSubscribes(function ($subscribe) {
                \FresnsCmdWord::plugin()->removeSubscribeItem($subscribe);
            });
        } catch (\Throwable $e) {
            \info('FileStorage add config fail: '.$e->getMessage());
        }
    }
};

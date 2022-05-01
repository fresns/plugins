<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Plugins\QiNiu\Events\FileUpdateToQiNiuSuccessfual;
use Plugins\QiNiu\Events\UploadTokenGenerated;
use Plugins\QiNiu\Listeners\GenerateVideoScreenshot;
use Plugins\QiNiu\Listeners\RemoveLocalFile;
use Plugins\QiNiu\Listeners\SaveQiNiuFilePath;
use Plugins\QiNiu\Listeners\SaveTokenToDatabase;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        'plugins.cleandata' => [
            // When the user uninstalls, if the data needs to be deleted, the listener is configured here.
        ],
        UploadTokenGenerated::class => [
            SaveTokenToDatabase::class,
        ],
        FileUpdateToQiNiuSuccessfual::class => [
            SaveQiNiuFilePath::class,
            GenerateVideoScreenshot::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}

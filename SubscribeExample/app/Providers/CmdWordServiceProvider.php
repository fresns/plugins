<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SubscribeExample\Providers;

use Fresns\CmdWordManager\Contracts\CmdWordProviderContract;
use Fresns\CmdWordManager\Traits\CmdWordProviderTrait;
use Illuminate\Support\ServiceProvider;
use Plugins\SubscribeExample\Services\CmdWordService;

class CmdWordServiceProvider extends ServiceProvider implements CmdWordProviderContract
{
    use CmdWordProviderTrait;

    protected $fsKeyName = 'SubscribeExample';

    /**
     * @var array[]
     */
    protected $cmdWordsMap = [
        ['word' => 'dataChange', 'provider' => [CmdWordService::class, 'dataChange']],
        ['word' => 'userActivity', 'provider' => [CmdWordService::class, 'userActivity']],
        ['word' => 'accountAndUserLogin', 'provider' => [CmdWordService::class, 'accountAndUserLogin']],
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerCmdWordProvider();
    }
}

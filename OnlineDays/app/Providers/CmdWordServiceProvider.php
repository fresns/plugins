<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\OnlineDays\Providers;

use Fresns\CmdWordManager\Contracts\CmdWordProviderContract;
use Fresns\CmdWordManager\Traits\CmdWordProviderTrait;
use Illuminate\Support\ServiceProvider;
use Plugins\OnlineDays\Services\CmdWordService;

class CmdWordServiceProvider extends ServiceProvider implements CmdWordProviderContract
{
    use CmdWordProviderTrait;

    protected $fsKeyName = 'OnlineDays';

    /**
     * @var array[]
     */
    protected $cmdWordsMap = [
        ['word' => 'stats', 'provider' => [CmdWordService::class, 'stats']],
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerCmdWordProvider();
    }
}

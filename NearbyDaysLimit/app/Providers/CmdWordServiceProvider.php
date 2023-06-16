<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\NearbyDaysLimit\Providers;

use Fresns\CmdWordManager\Contracts\CmdWordProviderContract;
use Fresns\CmdWordManager\Traits\CmdWordProviderTrait;
use Illuminate\Support\ServiceProvider;
use Plugins\NearbyDaysLimit\Services\CmdWordService;

class CmdWordServiceProvider extends ServiceProvider implements CmdWordProviderContract
{
    use CmdWordProviderTrait;

    protected $fsKeyName = 'NearbyDaysLimit';

    /**
     * @var array[]
     */
    protected $cmdWordsMap = [
        ['word' => 'getPostByNearby', 'provider' => [CmdWordService::class, 'getPostByNearby']],
        ['word' => 'getCommentByNearby', 'provider' => [CmdWordService::class, 'getCommentByNearby']],
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerCmdWordProvider();
    }
}

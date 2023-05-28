<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\PortalEditor\Providers;

use Fresns\CmdWordManager\Contracts\CmdWordProviderContract;
use Fresns\CmdWordManager\Traits\CmdWordProviderTrait;
use Illuminate\Support\ServiceProvider;
use Plugins\PortalEditor\Services\CmdWordService;

class CmdWordServiceProvider extends ServiceProvider implements CmdWordProviderContract
{
    use CmdWordProviderTrait;

    protected $fsKeyName = 'PortalEditor';

    /**
     * @var array[]
     */
    protected $cmdWordsMap = [
        ['word' => 'generateContent', 'provider' => [CmdWordService::class, 'generateContent']],
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerCmdWordProvider();
    }
}

<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Providers;

use Fresns\CmdWordManager\Contracts\CmdWordProviderContract;
use Fresns\CmdWordManager\Traits\CmdWordProviderTrait;
use Illuminate\Support\ServiceProvider;
use Plugins\FileStorage\Services\CmdWordService;

class CmdWordServiceProvider extends ServiceProvider implements CmdWordProviderContract
{
    use CmdWordProviderTrait;

    protected $unikeyName = 'FileStorage';

    /**
     * @var array[]
     */
    protected $cmdWordsMap = [
        ['word' => 'getUploadToken', 'provider' => [CmdWordService::class, 'getUploadToken']],
        ['word' => 'uploadFile', 'provider' => [CmdWordService::class, 'uploadFile']],
        ['word' => 'uploadFileInfo', 'provider' => [CmdWordService::class, 'uploadFileInfo']],
        ['word' => 'getAntiLinkFileInfo', 'provider' => [CmdWordService::class, 'getAntiLinkFileInfo']],
        ['word' => 'getAntiLinkFileInfoList', 'provider' => [CmdWordService::class, 'getAntiLinkFileInfoList']],
        ['word' => 'getAntiLinkFileOriginalUrl', 'provider' => [CmdWordService::class, 'getAntiLinkFileOriginalUrl']],
        ['word' => 'logicalDeletionFiles', 'provider' => [CmdWordService::class, 'logicalDeletionFiles']],
        ['word' => 'physicalDeletionFiles', 'provider' => [CmdWordService::class, 'physicalDeletionFiles']],
        ['word' => 'audioAndVideoTranscode', 'provider' => [CmdWordService::class, 'audioAndVideoTranscode']],
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCmdWordProvider();
    }
}

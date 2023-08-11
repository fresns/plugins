<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\Cloudinary\Providers;

use Fresns\CmdWordManager\Contracts\CmdWordProviderContract;
use Fresns\CmdWordManager\Traits\CmdWordProviderTrait;
use Illuminate\Support\ServiceProvider;
use Plugins\Cloudinary\Services\CmdWordService;

class CmdWordServiceProvider extends ServiceProvider implements CmdWordProviderContract
{
    use CmdWordProviderTrait;

    protected $fsKeyName = 'Cloudinary';

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
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerCmdWordProvider();
    }
}

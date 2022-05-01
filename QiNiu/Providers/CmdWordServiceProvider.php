<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Providers;

use Illuminate\Support\ServiceProvider;
use Plugins\QiNiu\Services\QiNiuService;

class CmdWordServiceProvider extends ServiceProvider implements \Fresns\CmdWordManager\Contracts\CmdWordProviderContract
{
    use \Fresns\CmdWordManager\Traits\CmdWordProviderTrait;

    protected $unikeyName = 'QiNiu';

    /**
     * @example
     *
     * use Plugins\BarBaz\Models\TestModel;
     * use Plugins\BarBaz\Services\AWordService;
     * use Plugins\BarBaz\Services\BWordService;
     *
     * protected $cmdWordsMap = [
     * ['word' => AWordService::CMD_TEST, 'provider' => [AWordService::class, 'handleTest']],
     * ['word' => BWordService::CMD_STATIC_TEST, 'provider' => [BWordService::class, 'handleStaticTest']],
     * ['word' => TestModel::CMD_MODEL_TEST, 'provider' => [TestModel::class, 'handleModelTest']],
     * ];
     *
     * @var array[]
     */
    protected $cmdWordsMap = [
        ['word' => 'getUploadToken', 'provider' => [QiNiuService::class, 'getUploadToken']],
        ['word' => 'uploadFile', 'provider' => [QiNiuService::class, 'uploadFile']],
        ['word' => 'uploadFileInfo', 'provider' => [QiNiuService::class, 'uploadFileInfo']],
        ['word' => 'getFileUrlOfAntiLink', 'provider' => [QiNiuService::class, 'getFileUrlOfAntiLink']],
        ['word' => 'getFileInfoOfAntiLink', 'provider' => [QiNiuService::class, 'getFileInfoOfAntiLink']],
        ['word' => 'physicalDeletionFile', 'provider' => [QiNiuService::class, 'physicalDeletionFile']],
        ['word' => 'qiniuTranscoding', 'provider' => [QiNiuService::class, 'qiniuTranscoding']],
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

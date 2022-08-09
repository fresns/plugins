<?php

namespace Plugins\QiNiu\Providers;

use Illuminate\Support\ServiceProvider;
use Plugins\QiNiu\Services\CmdWordService;

class CmdWordServiceProvider extends ServiceProvider implements \Fresns\CmdWordManager\Contracts\CmdWordProviderContract
{
    use \Fresns\CmdWordManager\Traits\CmdWordProviderTrait;

    protected $unikeyName = 'QiNiu';

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
        ['word' => 'audioVideoTranscoding', 'provider' => [CmdWordService::class, 'audioVideoTranscoding']],
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

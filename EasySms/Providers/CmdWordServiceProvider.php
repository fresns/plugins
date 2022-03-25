<?php

namespace Plugins\EasySms\Providers;

use Illuminate\Support\ServiceProvider;
use Plugins\EasySms\Services\SmsService;

class CmdWordServiceProvider extends ServiceProvider implements \Fresns\CmdWordManager\Contracts\CmdWordProviderContract
{
    use \Fresns\CmdWordManager\Traits\CmdWordProviderTrait;

    protected $unikeyName = 'EasySms';

    /* This is a map of command word and its provider. */
    protected $cmdWordsMap = [
        ['word' => 'sendCode', 'provider' => [SmsService::class, 'makeMailCode']],
        ['word' => 'sendSms', 'provider' => [SmsService::class, 'sendSms']],
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

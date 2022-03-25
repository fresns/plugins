<?php

namespace Plugins\FresnsEmail\Providers;

use Plugins\FresnsEmail\Mail\MailCmdService;
use Illuminate\Support\ServiceProvider;

class CmdWordServiceProvider extends ServiceProvider implements \Fresns\CmdWordManager\Contracts\CmdWordProviderContract
{
    use \Fresns\CmdWordManager\Traits\CmdWordProviderTrait;

    protected $unikeyName = 'FresnsEmail';

    /* This is a map of command word and its provider. */
    protected $cmdWordsMap = [
        ['word' => 'sendCode', 'provider' => [MailCmdService::class, 'sendCode']],
        ['word' => 'sendEmail', 'provider' => [MailCmdService::class, 'sendEmail']],
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

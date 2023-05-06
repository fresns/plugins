<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SmtpEmail\Providers;

use Illuminate\Support\ServiceProvider;
use Plugins\SmtpEmail\Mail\MailCmdService;

class CmdWordServiceProvider extends ServiceProvider implements \Fresns\CmdWordManager\Contracts\CmdWordProviderContract
{
    use \Fresns\CmdWordManager\Traits\CmdWordProviderTrait;

    protected $fsKeyName = 'SmtpEmail';

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

<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Listeners;

use Plugins\QiNiu\Support\Installer;

class ClearPluginDataListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $unikey = $event['unikey'] ?? null;
        if ($unikey !== 'QiNiu') {
            return;
        }

        return (new Installer)->uninstall(true);
    }
}

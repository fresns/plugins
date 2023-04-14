<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Listeners;

use Plugins\FileStorage\Support\Installer;

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
        if ($unikey !== 'FileStorage') {
            return;
        }

        return (new Installer)->uninstall(true);
    }
}

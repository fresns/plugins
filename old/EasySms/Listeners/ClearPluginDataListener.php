<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\Listeners;

use Plugins\EasySms\Support\Installer;

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
    public function handle($plugin)
    {
        return (new Installer)->uninstall(\request()->input('clear_plugin_data'));
    }
}

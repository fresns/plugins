<?php

namespace Plugins\EasySms\Listeners;

use Plugins\EasySms\Support\Installer;

class CliearPluginDataListener
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
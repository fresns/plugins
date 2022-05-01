<?php

namespace Plugins\QiNiu\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Plugins\QiNiu\Events\FileUpdateToQiNiuSuccessfual;

class SaveQiNiuFilePath
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
    public function handle(FileUpdateToQiNiuSuccessfual $event)
    {
        $event->fileModel->update([
            'file_path' => $event->qiniuFilePath,
        ]);
    }
}
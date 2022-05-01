<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Events;

use App\Models\File;
use Illuminate\Queue\SerializesModels;

class FileUpdateToQiNiuSuccessfual
{
    use SerializesModels;

    public $fileModel;
    public $qiniuFilePath;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(File $fileModel, string $qiniuFilePath)
    {
        $this->fileModel = $fileModel;
        $this->qiniuFilePath = $qiniuFilePath;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}

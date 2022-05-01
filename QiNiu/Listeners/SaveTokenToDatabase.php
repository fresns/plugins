<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Listeners;

use App\Models\PluginCallback;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Plugins\QiNiu\Events\UploadTokenGenerated;
use Plugins\QiNiu\QiNiu;

class SaveTokenToDatabase
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
    public function handle(UploadTokenGenerated $event)
    {
        $data = $this->getData($event->disk, $event->token);

        PluginCallback::create($data);
    }

    public function getData(QiNiu $disk, string $token)
    {
        return [
            'plugin_unikey' => 'QiNiu',
            'user_id' => 0,
            'uuid' => Str::uuid()->getHex(),
            'types' => $disk->getType(),
            'content' => $this->getContent($disk, $token),
        ];
    }

    public function getContent(QiNiu $disk, string $token)
    {
        /** @see https://gitee.com/fresns/extensions/blob/master/QiNiu/docs/%E8%8E%B7%E5%8F%96%E4%B8%8A%E4%BC%A0%E5%87%AD%E8%AF%81.md */
        return [
            'callbackType' => 1, // 固定
            'dataType' => 'object', // 固定
            'dataValue' => [
                'storageId' => $disk->getStorageId(),
                'fileType' => $disk->getType(),
                'token' => $token,
            ],
        ];
    }
}

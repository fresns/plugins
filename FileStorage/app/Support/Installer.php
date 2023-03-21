<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Support;

use App\Fresns\Subscribe\Subscribe;
use App\Models\FileUsage;
use App\Utilities\ConfigUtility;

class Installer
{
    protected $fresnsConfigItems = [
        // image
        [
            'item_key' => 'filestorage_image_driver',
            'item_value' => 'local', // local, ftp, sftp
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_private_key',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_passphrase',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_host_fingerprint',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_processing_library',
            'item_value' => 'gd',
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_processing_params',
            'item_value' => '{"config":400,"ratio":400,"square":200,"big":1500}',
            'item_type' => 'object',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_watermark_file',
            'item_value' => null,
            'item_type' => 'file',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_watermark_position',
            'item_value' => 'top-left',
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],

        // video
        [
            'item_key' => 'filestorage_video_driver',
            'item_value' => 'local', // local, ftp, sftp
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_video_private_key',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_video_passphrase',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_video_host_fingerprint',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],

        // audio
        [
            'item_key' => 'filestorage_audio_driver',
            'item_value' => 'local', // local, ftp, sftp
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_audio_private_key',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_audio_passphrase',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_audio_host_fingerprint',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],

        // document
        [
            'item_key' => 'filestorage_document_driver',
            'item_value' => 'local', // local, ftp, sftp
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_document_private_key',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_document_passphrase',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_document_host_fingerprint',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
    ];

    protected $subscribes = [
        [
            'type' => Subscribe::TYPE_TABLE_DATA_CHANGE,
            'unikey' => 'FileStorage',
            'cmdWord' => 'audioAndVideoTranscode',
            'subTableName' => FileUsage::class,
        ],
    ];

    public function handleSubscribes(callable $callable)
    {
        foreach ($this->subscribes as $subscribe) {
            $callable($subscribe);
        }
    }

    // plugin install
    public function install()
    {
        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);

        try {
            $this->handleSubscribes(function ($subscribe) {
                \FresnsCmdWord::plugin()->addSubscribeItem($subscribe);
            });
        } catch (\Throwable $e) {
            \info('FileStorage add config fail: '.$e->getMessage());
            throw $e;
        }
    }

    // plugin uninstall
    public function uninstall()
    {
        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);

        try {
            $this->handleSubscribes(function ($subscribe) {
                \FresnsCmdWord::plugin()->deleteSubscribeItem($subscribe);
            });
        } catch (\Throwable $e) {
            \info('FileStorage remove config fail: '.$e->getMessage());
            throw $e;
        }
    }
}

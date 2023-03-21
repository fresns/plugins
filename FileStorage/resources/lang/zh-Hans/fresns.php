<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Email Language Lines
    |--------------------------------------------------------------------------
    */

    'name' => 'Fresns 文件存储',
    'description' => 'Fresns 官方开发的「文件存储」服务插件，支持本地、FTP、SFTP 三种存储方式。',

    'test' => '测试',

    'driver' => '磁盘',
    'driverIntro' => '支持 local, ftp, sftp',

    'privateKeyIntro' => 'SFTP 专用，如果使用 SSH 密钥验证方式必填，否则留空',
    'passphraseIntro' => 'SFTP 专用，如果密钥无密码则留空',
    'hostFingerprintIntro' => 'SFTP 专用，没有要求则留空',

    'imageProcessingLibrary' => '图片处理库',
    'imageProcessingLibraryIntro' => '支持 GD 和 Imagick',

    'imageMaxWidth' => '宽度上限，单位：像素 px',
    'imageSquareSize' => '正方形尺寸，单位：像素 px',

    'imageWatermarkFile' => '水印图片',
    'imageWatermarkConfig' => '水印配置',
];

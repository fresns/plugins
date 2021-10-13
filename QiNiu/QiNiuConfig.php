<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\QiNiu;

class QiNiuConfig
{
    CONST TYPE_IMAGE = 1;   // 图片
    CONST TYPE_VIDEO = 2;   // 视频
    CONST TYPE_AUDIO = 3;   // 音频
    CONST TYPE_DOC = 4;     // 文档

    // 防盗链 图片
    CONST IMAGE_URL_STATUS = 'images_url_status';   // 防盗链开启状态
    CONST IMAGE_URL_KEY = 'images_url_key';         // 防盗链 key
    CONST IMAGE_URL_EXPIRE = 'images_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 视频
    CONST VIDEO_URL_STATUS = 'videos_url_status';   // 防盗链开启状态
    CONST VIDEO_URL_KEY = 'videos_url_key';         // 防盗链 key
    CONST VIDEO_URL_EXPIRE = 'videos_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 音频
    CONST AUDIO_URL_STATUS = 'audios_url_status';   // 防盗链开启状态
    CONST AUDIO_URL_KEY = 'audios_url_key';         // 防盗链 key
    CONST AUDIO_URL_EXPIRE = 'audios_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 音频
    CONST DOC_URL_STATUS = 'docs_url_status';       // 防盗链开启状态
    CONST DOC_URL_KEY = 'docs_url_key';             // 防盗链 key
    CONST DOC_URL_EXPIRE = 'docs_url_expire';       // 防盗链过期时间，单位：分钟
}

<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\QiNiu;

class QiNiuConfig
{
    const TYPE_IMAGE = 1;   // 图片
    const TYPE_VIDEO = 2;   // 视频
    const TYPE_AUDIO = 3;   // 音频
    const TYPE_DOC = 4;     // 文档

    // 防盗链 图片
    const IMAGE_URL_STATUS = 'images_url_status';   // 防盗链开启状态
    const IMAGE_URL_KEY = 'images_url_key';         // 防盗链 key
    const IMAGE_URL_EXPIRE = 'images_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 视频
    const VIDEO_URL_STATUS = 'videos_url_status';   // 防盗链开启状态
    const VIDEO_URL_KEY = 'videos_url_key';         // 防盗链 key
    const VIDEO_URL_EXPIRE = 'videos_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 音频
    const AUDIO_URL_STATUS = 'audios_url_status';   // 防盗链开启状态
    const AUDIO_URL_KEY = 'audios_url_key';         // 防盗链 key
    const AUDIO_URL_EXPIRE = 'audios_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 音频
    const DOC_URL_STATUS = 'docs_url_status';       // 防盗链开启状态
    const DOC_URL_KEY = 'docs_url_key';             // 防盗链 key
    const DOC_URL_EXPIRE = 'docs_url_expire';       // 防盗链过期时间，单位：分钟
}

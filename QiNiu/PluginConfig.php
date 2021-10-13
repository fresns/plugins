<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\QiNiu;

use App\Http\Center\Base\BasePluginConfig;

class PluginConfig extends BasePluginConfig
{
    public $type = 2; //1.网站引擎 2.扩展插件 3.移动应用 4.控制面板 5.主题模板
    public $uniKey = 'QiNiu';
    public $name = '七牛云';
    public $description = 'Fresns 官方开发的「七牛云」存储服务插件。';
    public $author = 'Fresns';
    public $authorLink = 'https://fresns.org';
    public $currVersion = '1.0';
    public $currVersionInt = 1;
    public $accessPath = '/qiniu/upload?sign={sign}&token={uploadToken}&uploadInfo={uploadInfo}&callback={uuid}&lang={langtag}';
    public $sceneArr = [
        'storage', // 存储服务商
    ];

    // 插件默认命令字
    public const FRESNS_CMD_DEFAULT = 'fresns_cmd_default';
    // 获取上传凭证
    public const FRESNS_CMD_GET_UPLOAD_TOKEN = 'fresns_cmd_get_upload_token';
    // 上传文件
    public const FRESNS_CMD_UPLOAD_FILE = 'fresns_cmd_upload_file';
    // 获取带防盗链签名的地址
    public const FRESNS_CMD_ANTI_LINK_IMAGE = 'fresns_cmd_anti_link_image';
    public const FRESNS_CMD_ANTI_LINK_VIDEO = 'fresns_cmd_anti_link_video';
    public const FRESNS_CMD_ANTI_LINK_AUDIO = 'fresns_cmd_anti_link_audio';
    public const FRESNS_CMD_ANTI_LINK_DOC = 'fresns_cmd_anti_link_doc';
    // 删除文件
    public const FRESNS_CMD_PHYSICAL_DELETION_FILE = 'fresns_cmd_physical_deletion_file';
    //转码命令字
    public const FRESNS_CMD_QINIU_TRANSCODING = 'fresns_cmd_qiniu_transcoding';
    // 插件命令字回调映射
    const FRESNS_CMD_HANDLE_MAP = [
        self::FRESNS_CMD_DEFAULT => 'defaultHandler',
        self::FRESNS_CMD_GET_UPLOAD_TOKEN => 'plgCmdGetUploadTokenHandler',
        self::FRESNS_CMD_UPLOAD_FILE => 'plgCmdUploadFileHandler',
        self::FRESNS_CMD_ANTI_LINK_IMAGE => 'plgCmdAntiLinkImageHandler',
        self::FRESNS_CMD_ANTI_LINK_VIDEO => 'plgCmdAntiLinkVideoHandler',
        self::FRESNS_CMD_ANTI_LINK_AUDIO => 'plgCmdAntiLinkAudioHandler',
        self::FRESNS_CMD_ANTI_LINK_DOC => 'plgCmdAntiLinkDocHandler',
        self::FRESNS_CMD_PHYSICAL_DELETION_FILE => 'plgCmdPhysicalDeletionFileHandler',
        self::FRESNS_CMD_QINIU_TRANSCODING => 'fresnsCmdQiniuTranscodingHandler',
    ];

    // 插件状态码
    const OK = 0;
    const FAIL = 1001;
    const CODE_NOT_EXIST = 1002;
    const CODE_PARAMS_ERROR = 1003;

    // 插件状态码映射
    const CODE_MAP = [
        self::OK => 'ok',
        self::FAIL => '处理失败',
        self::CODE_NOT_EXIST => '数据不存在',
        self::CODE_PARAMS_ERROR => '参数错误',
    ];

    public function fresnsCmdQiniuTranscodingHandlerRule()
    {
        $rule = [
            'tableName' => 'required',
            'insertId' => 'required',
        ];

        return $rule;
    }
}

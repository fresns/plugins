<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Http\Controllers;

use App\Fresns\Api\Base\Controllers\BaseFrontendController;
use App\Fresns\Api\Center\Common\ErrorCodeService;
use App\Fresns\Api\Center\Helper\CmdRpcHelper;
use App\Fresns\Api\Center\Scene\FileSceneService;
use App\Helpers\ConfigHelper;
use App\Fresns\Api\FsCmd\FresnsCmdWords;
use App\Fresns\Api\FsCmd\FresnsCmdWordsConfig;
use App\Fresns\Api\FsDb\FresnsPluginCallbacks\FresnsPluginCallbacks;
use Plugins\QiNiu\ServicesOld\QiNiuService;
use Illuminate\Http\Request;

class QiNiuControllerWeb extends BaseFrontendController
{
    // Version Info
    public function __construct()
    {
    }

    // 网页功能：上传文件
    // https://github.com/fresns/extensions/tree/main/QiNiu#%E7%BD%91%E9%A1%B5%E5%8A%9F%E8%83%BD
    public function upload(Request $request)
    {
        $wordBody = [
            'fileId' => 1,
        ];

        $resp = \FresnsCmdWord::plugin('QiNiu')->physicalDeletionFile($wordBody);
        
        dd($resp);
        $callback = $request->input('callback');
        $base64DecodeSign = $request->input('sign');
        $base64DecodeToken = $request->input('token');
        $base64DecodeUploadInfo = $request->input('uploadInfo');
        $sign = base64_decode(urldecode($base64DecodeSign));
        parse_str($sign, $signArr);
        //解析token
        $token = base64_decode(urldecode($base64DecodeToken));
        // dd($token);
        // 1 : 解析并判断 sign 是否正确, 封装方法
        $cmd = FresnsCmdWordsConfig::FRESNS_CMD_VERIFY_SIGN;
        $input = [];
        $input['platform'] = $signArr['platform'] ?? null;
        $input['version'] = $signArr['version'] ?? null;
        $input['versionInt'] = $signArr['versionInt'] ?? null;
        $input['appId'] = $signArr['appId'] ?? null;
        $input['timestamp'] = $signArr['timestamp'] ?? null;
        $input['uid'] = $signArr['uid'] ?? null;
        $input['mid'] = $signArr['mid'] ?? null;
        $input['token'] = $signArr['token'] ?? null;
        $input['sign'] = $signArr['sign'] ?? null;
        $resp = CmdRpcHelper::call(FresnsCmdWords::class, $cmd, $input);
        if (CmdRpcHelper::isErrorCmdResp($resp)) {
            $this->errorCheckInfo($resp, [], $resp['output']);
        }
        // 2 : 解析并判断 uploadToken 是否正确, 封装方法
        // 与 获取上传凭证 接口中 写入 plugin_callbacks 表的数据比对
        // 这个 token 的 content 里面包括了要上传的文件类型等信息
        // 10分钟内有效
        $start = date('Y-m-d H:i:s', strtotime('-10 min'));
        $end = date('Y-m-d H:i:s', time());
        $pluginCallBacks = FresnsPluginCallbacks::where('plugin_unikey', 'QiNiu')->where('created_at', '>=', $start)->where('created_at', '<=', $end)->where('content', 'LIKE', "%$token%")->first();

        if (empty($pluginCallBacks)) {
            $this->error(ErrorCodeService::HEADER_SIGN_EXPIRED);
        }
        // 3 : 解析 uploadInfo 参数
        if (empty($base64DecodeUploadInfo)) {
            $this->error(ErrorCodeService::CODE_PARAM_ERROR);
        }
        //解析，并校验必传
        $uploadInfo = base64_decode(urldecode($base64DecodeUploadInfo));
        $uploadInfoArr = json_decode($uploadInfo, true);

        $requiredArr = ['fileType', 'tableType', 'tableName', 'tableField'];
        foreach ($requiredArr as $required) {
            if (empty($uploadInfoArr[$required])) {
                $info = [
                    $required => $required.' is null',
                ];
                $this->error(ErrorCodeService::CODE_PARAM_ERROR, $info);
            }
        }
        // 4: 渲染上传页面
        $type = $uploadInfoArr['fileType'] ?? 1;
        $qiNiuService = new QiNiuService($type);

        $options = [];
        $options['file_type'] = $uploadInfoArr['fileType'] ?? 1;
        $options['table_type'] = $uploadInfoArr['tableType'] ?? 1;
        $key = FileSceneService::getFormalEditorPath($options);
        $uploadToken = $qiNiuService->getUploadToken($type, $key);
        switch ($type) {
            case 1:
                $fileArea = ConfigHelper::fresnsConfigByItemKey('image_bucket_area');
                $fileDomain = ConfigHelper::fresnsConfigByItemKey('image_bucket_domain');
                $fileExt = ConfigHelper::fresnsConfigByItemKey('image_ext');
                $fileSize = ConfigHelper::fresnsConfigByItemKey('image_max_size');
                break;
            case 2:
                $fileArea = ConfigHelper::fresnsConfigByItemKey('video_bucket_area');
                $fileDomain = ConfigHelper::fresnsConfigByItemKey('video_bucket_domain');
                $fileExt = ConfigHelper::fresnsConfigByItemKey('video_ext');
                $fileSize = ConfigHelper::fresnsConfigByItemKey('video_max_size');
                break;
            case 3:
                $fileArea = ConfigHelper::fresnsConfigByItemKey('audio_bucket_area');
                $fileDomain = ConfigHelper::fresnsConfigByItemKey('audio_bucket_domain');
                $fileExt = ConfigHelper::fresnsConfigByItemKey('audio_ext');
                $fileSize = ConfigHelper::fresnsConfigByItemKey('audio_max_size');
                break;
            default:
                $fileArea = ConfigHelper::fresnsConfigByItemKey('document_bucket_area');
                $fileDomain = ConfigHelper::fresnsConfigByItemKey('document_bucket_domain');
                $fileExt = ConfigHelper::fresnsConfigByItemKey('document_ext');
                $fileSize = ConfigHelper::fresnsConfigByItemKey('document_max_size');
                break;
        }

        $customName1 = 'custom_name_1';
        $customValue1 = 'custom_value_1';
        $data = [
            'upload_token'  => $uploadToken,
            'resource_key'  => $key,
            'custom_name_1'  => $customName1,
            'custom_value_1'  => $customValue1,
            'file_domain'  => $fileDomain,
            'file_area'  => $fileArea,
            'file_ext'  => $fileExt,
            'file_size'  => $fileSize,
            'callback' => $callback,
            'file_type' => $type,
            'table_type' => $uploadInfoArr['tableType'] ?? 1,
            'table_name' => $uploadInfoArr['tableName'] ?? 1,
            'table_field' => $uploadInfoArr['tableField'] ?? 'id',
            'file_token' => $base64DecodeToken,
            'file_sign' => $base64DecodeSign,
        ];

        // 文件列表
        $fileArr = $qiNiuService->listFiles();
        $data['file_arr'] = $fileArr;

        return view('QiNiu::upload', $data);
    }
}

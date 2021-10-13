<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\QiNiu;

use App\Base\Controllers\BaseApiController;
use App\Helpers\StrHelper;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\LogService;
use App\Http\Center\Helper\CmdRpcHelper;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Center\Scene\FileSceneService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsCmd\FresnsCmdWords;
use App\Http\FresnsCmd\FresnsCmdWordsConfig;
use App\Http\FresnsDb\FresnsFileAppends\FresnsFileAppends;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsPluginCallbacks\FresnsPluginCallbacks;
use App\Plugins\QiNiu\Services\QiNiuService;
use App\Plugins\QiNiu\Services\QiNiuTransService;
use Illuminate\Http\Request;

class QiNiuControllerApi extends BaseApiController
{
    public function __construct()
    {
    }

    // 网页功能：上传文件
    // https://gitee.com/fresns/extensions/tree/master/QiNiu#%E7%BD%91%E9%A1%B5%E5%8A%9F%E8%83%BD
    public function uploadCallback(Request $request)
    {
        $qiNiuUploadResult = $request->input('qiNiuUploadResult');
        $appendParams = $request->input('appendParams');
        LogService::info('callback params', $qiNiuUploadResult);

        $qiNiuUploadResult = $request->input('qiNiuUploadResult');
        $appendParams = $request->input('appendParams');
        LogService::info('callback params', $qiNiuUploadResult);

        // 网页功能第 4 步, 将文件信息存储到 plugin_callbacks 数据表中。
        $path = '/'.$appendParams['key'];
        $uuid = StrHelper::createUuid();
        $item = [];
        //将数据存入表中
        $item['file_type'] = $appendParams['file_type'];
        $item['file_name'] = $qiNiuUploadResult['name'];
        $item['file_extension'] = $appendParams['fil_suffix'];
        $item['file_path'] = $path;
        $item['rank_num'] = 9;
        $item['uuid'] = $uuid;
        $item['table_type'] = $appendParams['table_type'];
        $item['table_name'] = $appendParams['table_name'];
        $item['table_field'] = $appendParams['table_field'];
        $item['table_id'] = $tableId ?? null;
        $item['table_key'] = $tableKey ?? null;
        $fieldId = FresnsFiles::insertGetId($item);
        $fileIdArr[] = $fieldId;
        $fidArr[] = $item['uuid'];
        $append = [];
        $append['file_id'] = $fieldId;
        $append['user_id'] = 0;
        $append['member_id'] = 0;
        $append['file_original_path'] = $item['file_path'];
        if ($appendParams['file_type'] == 1) {
            $append['file_mime'] = 'images/'.$appendParams['fil_suffix'];
        }
        if ($appendParams['file_type'] == 2) {
            $append['file_mime'] = 'video/'.$appendParams['fil_suffix'];
        }
        if ($appendParams['file_type'] == 3) {
            $append['file_mime'] = 'audio/'.$appendParams['fil_suffix'];
        }
        if ($appendParams['file_type'] == 4) {
            $append['file_mime'] = 'doc/'.$appendParams['fil_suffix'];
        }
        $append['file_size'] = $qiNiuUploadResult['size'];
        $append['image_width'] = $qiNiuUploadResult['width'] ?? null;
        $append['image_height'] = $qiNiuUploadResult['height'] ?? null;
        $imageLong = 0;
        if (! empty($fileInfo['imageLong'])) {
            $length = strlen($fileInfo['imageLong']);
            if ($length == 1) {
                $imageLong = $fileInfo['imageLong'];
            }
        }
        $append['image_is_long'] = $imageLong;
        $append['platform_id'] = 1;
        if ($appendParams['file_type'] == 2) {
            $transService = new QiNiuTransService($appendParams['file_type']);
            $dateStr = date('YmdHis', time());
            $options = [];
            $options['file_type'] = $appendParams['file_type'] ?? 1;
            $options['table_type'] = $appendParams['table_type'];
            $newFilePath = FileSceneService::getFormalEditorPath($options);
            // 视频缩略图，转码参数来自配置表 videos_screenshot
            $transAudioParams = ApiConfigHelper::getConfigByItemKey('videos_screenshot');
            $saveAsKey = "$newFilePath"."/{$dateStr}.jpg";
            $id = $transService->vframe($appendParams['key'], $saveAsKey, $transAudioParams);
            $append['video_cover'] = '/'.$saveAsKey;
        }

        FresnsFileAppends::insert($append);

        $callback['callbackType'] = 4;
        $callback['dataType'] = 'array';
        $dataValue['fid'] = $uuid;
        $dataValue['type'] = $appendParams['file_type'] ?? 1;
        $dataValue['name'] = $qiNiuUploadResult['name'];
        $dataValue['extension'] = $appendParams['fil_suffix'];
        $dataValue['size'] = $qiNiuUploadResult['size'];
        $dataValue['rankNum'] = 9;
        if ($appendParams['file_type'] == 1) {
            $dataValue['imageWidth'] = $qiNiuUploadResult['width'] ?? null;
            $dataValue['imageHeight'] = $qiNiuUploadResult['height'] ?? null;
            $imageLong = 0;
            if (! empty($dataValue['imageWidth']) && ! empty($dataValue['imageHeight'])) {
                if ($dataValue['imageWidth'] >= 700) {
                    if ($dataValue['imageHeight'] >= $dataValue['imageWidth'] * 4) {
                        $imageLong = 1;
                    }
                }
            }
            $dataValue['imageLong'] = $imageLong;
            $cmd = FresnsCmdWordsConfig::FRESNS_CMD_ANTI_LINK_IMAGE;
            $input['fid'] = $uuid;
            $resp = CmdRpcHelper::call(FresnsCmdWords::class, $cmd, $input);
            if (CmdRpcHelper::isErrorCmdResp($resp)) {
                return false;
            }
            $output = $resp['output'];
            $dataValue['imageRatioUrl'] = $output['imageRatioUrl'];
            $dataValue['imageSquareUrl'] = $output['imageSquareUrl'];
            $dataValue['imageBigUrl'] = $output['imageBigUrl'];
        }
        if ($appendParams['file_type'] == 2) {
            $dataValue['videoTime'] = 0;
            $cmd = FresnsCmdWordsConfig::FRESNS_CMD_ANTI_LINK_VIDEO;
            $input['fid'] = $uuid;
            $resp = CmdRpcHelper::call(FresnsCmdWords::class, $cmd, $input);
            if (CmdRpcHelper::isErrorCmdResp($resp)) {
                return false;
            }
            $output = $resp['output'];
            $dataValue['videoCover'] = $output['videoCover'];
            $dataValue['videoGif'] = $output['videoGif'];
            $dataValue['videoUrl'] = $output['videoUrl'];
        }
        if ($appendParams['file_type'] == 3) {
            $dataValue['audioTime'] = 0;
            $cmd = FresnsCmdWordsConfig::FRESNS_CMD_ANTI_LINK_VIDEO;
            $input['fid'] = $uuid;
            $resp = CmdRpcHelper::call(FresnsCmdWords::class, $cmd, $input);
            if (CmdRpcHelper::isErrorCmdResp($resp)) {
                return false;
            }
            $output = $resp['output'];
            $dataValue['audioUrl'] = $output['audioUrl'];
            $dataValue['transcodingStatus'] = 1;
        }
        if ($appendParams['file_type'] == 4) {
        }

        $dataValue['moreJson'] = json_encode([]);

        $callback['dataValue'] = json_encode([$dataValue]);

        $data['plugin_unikey'] = 'QiNiu';
        $data['member_id'] = 0;
        $data['uuid'] = $appendParams['callbackUuid'] ?? 0;
        $data['types'] = 4;
        $data['content'] = json_encode($callback);
        FresnsPluginCallbacks::insert($data);

        //网页功能第 5 步, 返回文件信息供页面显示
        $fileResultInfo = [];
        if ($dataValue['type'] == 1) {
            $fileResultInfo['name'] = $dataValue['name'];
            $fileResultInfo['extension'] = $dataValue['extension'];
            $fileResultInfo['mime'] = $append['file_mime'];
            $fileResultInfo['size'] = $dataValue['size'];
            $fileResultInfo['rankNum'] = 9;
            $fileResultInfo['imageRatioUrl'] = $dataValue['imageRatioUrl'];
            $fileResultInfo['imageSquareUrl'] = $dataValue['imageSquareUrl'];
            $fileResultInfo['imageBigUrl'] = $dataValue['imageBigUrl'];
            $fileResultInfo['imageLong'] = $dataValue['imageLong'];
        }
        if ($dataValue['type'] == 2) {
            $fileResultInfo['name'] = $dataValue['name'];
            $fileResultInfo['extension'] = $dataValue['extension'];
            $fileResultInfo['mime'] = $append['file_mime'];
            $fileResultInfo['size'] = $dataValue['size'];
            $fileResultInfo['rankNum'] = 9;
            $fileResultInfo['videoTime'] = $dataValue['videoTime'];
            $fileResultInfo['videoCover'] = $dataValue['videoCover'];
            $fileResultInfo['videoGif'] = $dataValue['videoGif'];
            $fileResultInfo['videoUrl'] = $dataValue['videoUrl'];
        }
        if ($dataValue['type'] == 3) {
            $fileResultInfo['name'] = $dataValue['name'];
            $fileResultInfo['extension'] = $dataValue['extension'];
            $fileResultInfo['mime'] = $append['file_mime'];
            $fileResultInfo['size'] = $dataValue['size'];
            $fileResultInfo['rankNum'] = 9;
            $fileResultInfo['audioTime'] = $dataValue['audioTime'];
            $fileResultInfo['audioUrl'] = $dataValue['audioUrl'];
        }
        if ($dataValue['type'] == 4) {
            $fileResultInfo['name'] = $dataValue['name'];
            $fileResultInfo['extension'] = $dataValue['extension'];
            $fileResultInfo['mime'] = $append['file_mime'];
            $fileResultInfo['size'] = $dataValue['size'];
            $fileResultInfo['rankNum'] = 9;
        }

        $data = [
            'qiNiuUploadResult'  => $qiNiuUploadResult,
            'appendParams'  => $appendParams,
            'fileResultInfo'  => $fileResultInfo,
        ];

        $this->success($data);
    }

    public function getToken(Request $request)
    {
        $fileType = $request->input('file_type');
        $key = $request->input('key');
        $qiNiuService = new QiNiuService($fileType);
        $uploadToken = $qiNiuService->getUploadToken($fileType, $key);

        $data = [];
        $data['token'] = $uploadToken;

        $this->success($data);
    }

    //测试插件转码命令字
    public function testTrans(Request $request)
    {
        $unikey = ApiConfigHelper::getConfigByItemKey('images_service');
        $pluginUniKey = $unikey;

        $pluginClass = PluginHelper::findPluginClass($pluginUniKey);
        if (empty($pluginClass)) {
            LogService::error('Plugin Class Not Found');

            return $this->pluginError(ErrorCodeService::PLUGINS_CONFIG_ERROR);
        }
        $tableName = $request->input('table_name');
        $id = $request->input('id');
        $cmd = 'fresns_cmd_qiniu_transcoding';
        $input = [];
        $input['table_name'] = $tableName;
        $input['id'] = $id;
        $resp = CmdRpcHelper::call($pluginClass, $cmd, $input);
        if (CmdRpcHelper::isErrorCmdResp($resp)) {
            return $this->error($resp['code']);
        }

        $this->success();
    }
}

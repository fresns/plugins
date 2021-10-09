<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\AqSms;
use AlibabaCloud\Client\AlibabaCloud;

// 阿里短信 SDK 加载开始
require_once (__DIR__ . "/alibabacloud/client/autoload.php");
require_once (__DIR__ . "/alibabacloud/sdk/autoload.php");

require_once (__DIR__ . "/misc/Dot.php");
require_once (__DIR__ . "/misc/helpers.php");

require_once (__DIR__ . "/guzzlehttp/guzzle/autoload.php");
require_once (__DIR__ . "/guzzlehttp/psr7/autoload.php");
require_once (__DIR__ . "/guzzlehttp/promises/autoload.php");
// 阿里短信 SDK 加载结束

class AliSmsService 
{

    // 发送验证码短信
    public function sendCodeSms($input){
        $ak = $input['keyId'];
        $as = $input['keySecret'];

        $phoneNumbers = $input['account'];
        $aliSign = $input['signName'];
        $templateCode = $input['templateCode'];
        $countryCode = $input['countryCode'];  

        $params = [
            'code'  => $input['codeSms'],
        ];
        $templateParams = json_encode($params);

        $data = [];

        AlibabaCloud::accessKeyClient($ak,$as)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "default",
                        'PhoneNumbers' => '+' . $countryCode . $phoneNumbers,
                        'SignName' => $aliSign,
                        'TemplateCode' => $templateCode,
                        'TemplateParam' => $templateParams,
                    ],
                ])
                ->request();
            $data = $result->toArray();
        } catch (\Exception $e) {
            $data = [
                'info' => $e->getMessage(),
            ];
            return false;
        }

        return $data;
    }

    // 发送自定义短信
    public function sendSms($input){
        $ak = $input['keyId'];
        $as = $input['keySecret'];

        $phoneNumbers = $input['phoneNumber'];
        $aliSign = $input['signName'];
        $templateCode = $input['templateCode'];
        $templateParams = $input['templateParam'];  
        $countryCode = $input['countryCode'];  
        $data = [];

        AlibabaCloud::accessKeyClient($ak,$as)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "default",
                        'PhoneNumbers' => '+' . $countryCode . $phoneNumbers,
                        'SignName' => $aliSign,
                        'TemplateCode' => $templateCode,
                        'TemplateParam' => $templateParams,
                    ],
                ])
                ->request();
            $data = $result->toArray();
        } catch (\Exception $e) {
            $data = [
                'info' => $e->getMessage(),
            ];
            return false;
        }

        return $data;
    }

}
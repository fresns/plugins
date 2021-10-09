<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\AqSms;

use App\Http\Center\Base\BasePlugin;
use Illuminate\Http\Request;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\SmsClient;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Center\Common\ErrorCodeService;

// 腾讯短信 SDK 加载开始
require_once (__DIR__ . "/tencentcloud/autoload.php");

class TencentSmsService
{
    // 发送验证码短信
    public function sendCodeSms($input){
        try{
            $cred = new Credential($input['keyId'], $input['keySecret']);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");
    
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new SmsClient($cred, "ap-beijing", $clientProfile);
    
            $req = new SendSmsRequest();
            $smsCode = $input['codeSms'];
            $params = array(
                "PhoneNumberSet" => array( '+' . $input['countryCode'] . $input['account']),
                "SmsSdkAppId" => $input['appid'],
                "SignName" => $input['signName'],
                "TemplateId" => $input['templateCode'],
                "TemplateParamSet" => array( "{$smsCode}" )
            );
            // dd($params);
            $req->fromJsonString(json_encode($params));
    
            $resp = $client->SendSms($req);
    
            $data = $resp->toJsonString();

        }catch(TencentCloudSDKException $e) {
            return false;
        }
        

        return $data;
    }

    // 发送自定义短信
    public function sendSms($input){
        try{
            $cred = new Credential($input['keyId'], $input['keySecret']);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");
    
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new SmsClient($cred, "ap-beijing", $clientProfile);
    
            $req = new SendSmsRequest();
            $templateParam = $input['templateParam'];
            $params = array(
                "PhoneNumberSet" => array( '+' . $input['countryCode'] . $input['phoneNumber']),
                "SmsSdkAppId" => $input['appid'],
                "SignName" => $input['signName'],
                "TemplateId" => $input['templateCode'],
                "TemplateParamSet" => explode(',',$templateParam)
            );
            // dd($params);
            $req->fromJsonString(json_encode($params));
    
            $resp = $client->SendSms($req);
    
            $data = $resp->toJsonString();

        }catch(TencentCloudSDKException $e) {
            return false;
        }
        

        return $data;
    }
}
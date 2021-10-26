# 阿 Q 短信插件

Fresns 官方开发的「[阿里云](https://www.aliyun.com/product/sms)」和「[腾讯云](https://cloud.tencent.com/product/sms)」二合一短信服务插件。

## 插件安装教程

- 1、下载插件安装包；
- 2、解压后把 `AqSms` 文件夹上传到主程序根目录 `extensions` 文件夹；
- 3、登录 Fresns 控制台，在控制台仪表盘选择「本地安装」，输入文件夹名 `AqSms` 执行安装；
- 4、安装完成后到控制台「插件」频道“启用”插件；
- 5、启用后进入设置页配置短信服务商信息。

## 插件开发说明

### 配置项键名

- 服务商类型 `aqsms_type`
    - 1.阿里云
    - 2.腾讯云
- App ID `aqsms_appid`
    - 腾讯云 SmsSdkAppId
    - 阿里云留空
- Key ID `aqsms_keyid`
- Key Secret `aqsms_keysecret`
- 国际区号匹配语言标签 `aqsms_linked`

```json
// aqsms_linked
// 根据传参国际区号去匹配模板语言标签，如果区号查询不到匹配，就使用 other 匹配的语言标签模板
{
    "国际区号": "验证码模板语言标签",
    "other": "其他区号使用该模板"
}
// 示例
{
    "86": "zh-Hans",
    "other": "en"
}
```

### API 参考资料

- 阿里云：[https://help.aliyun.com/document_detail/101414.htm](https://help.aliyun.com/document_detail/101414.htm)
- 腾讯云：[https://cloud.tencent.com/document/api/382/55981](https://cloud.tencent.com/document/api/382/55981)

### 功能介绍

- 验证码短信模板配置 [https://fresns.org/database/keyname/sends.html#%E9%AA%8C%E8%AF%81%E7%A0%81%E6%A8%A1%E6%9D%BF%E8%AE%BE%E7%BD%AE](https://fresns.org/database/keyname/sends.html#%E9%AA%8C%E8%AF%81%E7%A0%81%E6%A8%A1%E6%9D%BF%E8%AE%BE%E7%BD%AE)
- 根据国际区号判断是国内短信还是国际。
- 支持 `fresns_cmd_send_code` 命令字
    - 1、接收到命令字请求后，判断配置表 `aqsms_type` 类型，决定发哪家短信；
    - 2、根据区号判断是发国内短信还是国际短信；
    - 3、根据命令字传参 templateId 和 langTag 两个参数（找不到 langTag 匹配的模板，则使用默认语言），去匹配需要发信的模板；
    - 4、生成验证码（验证码有效期设定为 10 分钟），验证码位于短信中 templateParam 变量名；
    - 5、使用模板配置和替换模板中变量，请求云服务商发送短信。
- 支持 `fresns_cmd_send_sms` 命令字，直接传参短信配置，插件解析配置直接请求发送短信。
    - 1、接收到命令字请求后，判断配置表 `aqsms_type` 类型，决定发哪家短信；
    - 2、根据区号判断是发国内短信还是国际短信；
    - 3、根据命令字传参 templateCode 去发送模板短信；
    - 4、如果 templateParam 参数有值，直接传给服务商；
    - 具体「自定义发信」示例见下方表格。

| 命令字参数 | 腾讯云参数 | 腾讯云参数示例 | 阿里云参数 | 阿里云参数示例 |
| --- | --- | --- | --- | --- |
| countryCode |  | 86 |  | 86 |
| phoneNumber | PhoneNumberSet | 13900120012 | PhoneNumbers | 13900120012 |
| signName | SignName | Fresns | SignName | Fresns |
| templateCode | TemplateId | 1145184 | TemplateCode | SMS_225391766 |
| templateParam | templateParamSet1 | "123456","5分钟" | TemplateParam | {"password":"1234567890","time":"5分钟"} |

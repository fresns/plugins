# Fresns 短信插件

Fresns 官方开发的支持多家服务商的短信插件。

## 插件安装

- 在 Fresns 应用市场搜索 `EasySms` 并点击安装。

## 开发说明

### 配置项键名

- 服务商类型 `easysms_type`
    - 1.阿里云
    - 2.腾讯云
- App ID `easysms_appid`
    - 腾讯云 SmsSdkAppId
    - 阿里云留空
- Key ID `easysms_keyid`
- Key Secret `easysms_keysecret`
- 国际区号匹配语言标签 `easysms_linked`

```json
// easysms_linked
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

### 功能介绍

- 验证码短信模板配置 [https://fresns.cn/database/keyname/sends.html#%E9%AA%8C%E8%AF%81%E7%A0%81%E6%A8%A1%E6%9D%BF%E8%AE%BE%E7%BD%AE](https://fresns.cn/database/keyname/sends.html#%E9%AA%8C%E8%AF%81%E7%A0%81%E6%A8%A1%E6%9D%BF%E8%AE%BE%E7%BD%AE)
- 根据国际区号判断是国内短信还是国际。
- 支持 `sendCode` 命令字
    - 1、接收到命令字请求后，判断配置表 `easysms_type` 类型，决定发哪家短信；
    - 2、根据区号判断是发国内短信还是国际短信（根据区号匹配模板语言标签）；
    - 3、根据命令字传参 templateId 和 langTag 两个参数（找不到 langTag 匹配的模板，则使用默认语言），去匹配需要发信的模板；
    - 4、生成验证码（验证码有效期设定为 10 分钟），验证码位于短信中 templateParam 变量名；
    - 5、使用模板配置和替换模板中变量，请求云服务商发送短信。
- 支持 `sendSms` 命令字，直接传参短信配置，插件解析配置直接请求发送短信。
    - 1、接收到命令字请求后，判断配置表 `easysms_type` 类型，决定发哪家短信；
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

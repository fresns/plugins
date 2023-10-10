# 微信登录

Fresns 官方开发的「微信登录」插件，支持网站、小程序、App 等各端的微信登录。

## 插件安装

- 使用标识名安装: `WeChatLogin`
- 使用指令安装: `php artisan market:require WeChatLogin`

## 开发者使用说明

### 接口列表

| 接口说明 | 接口地址 | 必传参数 |
| --- | --- | --- |
| 小程序登录 | `/api/wechat-login/mini-program/oauth` | `code` |
| 使用小程序授权网页登录 | `/api/wechat-login/mini-program/oauth-website`<br><br>小程序入口（网页扫码后打开并访问的小程序页面）<br>`/pages/account/wechat-login/website-oauth` | `code`, `ulid` |
| 开放平台移动应用登录 | `/api/wechat-login/open-platform/oauth` | `code` |
| 多端应用 Apple 账号登录 | `/api/wechat-login/mini-app/oauth-apple` | `code` |
| 获取 JS-SDK 签名 | `/api/wechat-login/js-sdk/sign` | `url` 当前页面地址 |

- 请求方式：`POST`
- 传参方式：`application/json`

### Body 参数

| 参数名 | 类型 | 说明 |
| --- | --- | --- |
| code | String | 小程序用户登录凭证 |
| ulid | String | 专用: 使用小程序授权网页登录 |
| autoRegister | Boolean | 如果账号不存在，是否自动注册一个新账号 |
| nickname | String | `autoRegister` 自动注册账号时，指定昵称，如果为空则随机生成 |
| avatarUrl | String | `autoRegister` 自动注册账号时，指定头像图片 URL，如果为空则使用默认头像 |

**小程序授权网页登录的 ulid 参数，附带在小程序码的 scene 参数中**

```js
onLoad: async function (options) {
  console.log('Website Auth Ulid', options.scene);
},
```

- 示例代码: [https://github.com/fresns/wechat](https://github.com/fresns/wechat/blob/2.x/pages/account/wechat-login/website-oauth.js#L48-L51)

### 接口使用建议

- 建议第一次请求不传参 `autoRegister`，当账号不存在时，让用户选择是绑定账号还是生成新账号，避免用户已经有账号了，重复生成。
- 文案：
    - 您已经使用 `nickname` 授权成功，但是本站并未查询到对应的账号。
    - 我有账号，我要关联绑定
    - 我没有账号，帮我生成新账号
- 返回结果，参见 [https://docs.fresns.cn/api/account/login.html](https://docs.fresns.cn/api/account/login.html)

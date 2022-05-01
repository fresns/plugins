# 七牛云存储插件

Fresns 官方开发的「七牛云」存储服务插件。请通过[点击此链接](https://s.qiniu.com/YNJrYv)注册七牛云账号，不仅是对 Fresns 研发支持，注册后凭关联账号还能获得额外优惠和服务支持。

- 七牛云账号注册链接：[https://s.qiniu.com/YNJrYv](https://s.qiniu.com/YNJrYv)
- 配置信息：[https://fresns.cn/database/keyname/storages.html](https://fresns.cn/database/keyname/storages.html)
- 命令字信息：[https://fresns.cn/extensions/command.html](https://fresns.cn/extensions/command.html)
- 回调返参表：[https://fresns.cn/database/plugin/plugin-callbacks.html](https://fresns.cn/database/plugin/plugin-callbacks.html)

## 插件安装

- 使用标识名安装: `QiNiu`
- 使用指令安装: `php artisan fresns:require QiNiu`

## 插件配置

- 在「控制面板 > 系统 > 存储设置」配置存储服务商参数。
- 确保你的服务器安全组“出站规则”开放了 TCP:443 端口，否则需要从服务器转存到七牛云的文件无法出站上传到七牛。

| 存储设置 | 介绍 |
| --- | --- |
| Secret ID | [七牛云 AK(AccessKey)](https://portal.qiniu.com/user/key) |
| Secret Key | [七牛云 SK(SecretKey)](https://portal.qiniu.com/user/key) |
| 存储配置名称 | 空间名称 |
| 存储配置地域 | 华东 `z0`<br>华北 `z1`<br>华南 `z2`<br>北美 `na0`<br>东南亚 `as0`<br>华东-浙江2 `cn-east-2` |
| 存储配置域名 | 空间绑定的域名 |
| 图片处理功能配置 | 图片样式名包含样式分隔符 |

## 开发说明

### 安装和卸载

- 安装时，请求订阅命令字，新增订阅。

```php
\FresnsCmdWord::plugin('Fresns')->addSubscribeItem([
    "type" => 1,
    "unikey" => "QiNiu",
    "cmdWord" => "qiniuTranscoding"
    "subTableName" => "posts"
])

\FresnsCmdWord::plugin('Fresns')->addSubscribeItem([
    "type" => 1,
    "unikey" => "QiNiu",
    "cmdWord" => "qiniuTranscoding"
    "subTableName" => "comments"
])
```

- 卸载时再次请求取消订阅。

```php
\FresnsCmdWord::plugin('Fresns')->deleteSubscribeItem([
    "type" => 1,
    "unikey" => "QiNiu",
    "cmdWord" => "qiniuTranscoding"
    "subTableName" => "posts"
])

\FresnsCmdWord::plugin('Fresns')->deleteSubscribeItem([
    "type" => 1,
    "unikey" => "QiNiu",
    "cmdWord" => "qiniuTranscoding"
    "subTableName" => "comments"
])
```

### 获取上传凭证

- 命令字：`getUploadToken`
- [需求介绍](docs/%E8%8E%B7%E5%8F%96%E4%B8%8A%E4%BC%A0%E5%87%AD%E8%AF%81.md)

### 上传文件

- 命令字：`uploadFile`
- [需求介绍](docs/%E4%B8%8A%E4%BC%A0%E6%96%87%E4%BB%B6.md)

### 上传文件信息

- 命令字：`uploadFileInfo`
- [需求介绍](docs/%E4%B8%8A%E4%BC%A0%E6%96%87%E4%BB%B6%E4%BF%A1%E6%81%AF.md)

### 获取防盗链的文件地址

- 命令字：`getFileUrlOfAntiLink`
- [需求介绍](docs/%E8%8E%B7%E5%8F%96%E9%98%B2%E7%9B%97%E9%93%BE%E7%9A%84%E6%96%87%E4%BB%B6%E5%9C%B0%E5%9D%80.md)

### 获取防盗链的文件信息

- 命令字：`getFileInfoOfAntiLink`
- [需求介绍](docs/%E8%8E%B7%E5%8F%96%E9%98%B2%E7%9B%97%E9%93%BE%E7%9A%84%E6%96%87%E4%BB%B6%E4%BF%A1%E6%81%AF.md)

### 命令字功能：删除物理文件

- 命令字：`physicalDeletionFile`
- [需求介绍](docs/%E5%88%A0%E9%99%A4%E7%89%A9%E7%90%86%E6%96%87%E4%BB%B6.md)

### 网页上传文件

- [需求介绍](docs/%E7%BD%91%E9%A1%B5%E4%B8%8A%E4%BC%A0%E6%96%87%E4%BB%B6.md)

### 转码功能

- 命令字：`qiniuTranscoding`
- [需求介绍](docs/%E8%BD%AC%E7%A0%81%E5%8A%9F%E8%83%BD.md)

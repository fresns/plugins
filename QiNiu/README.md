# 七牛云存储插件

Fresns 官方开发的「七牛云」存储服务插件。请通过[点击此链接](https://s.qiniu.com/YNJrYv)注册七牛云账号，不仅是对 Fresns 研发支持，注册后凭关联账号还能获得额外优惠和服务支持。

- 七牛云账号注册链接：[https://s.qiniu.com/YNJrYv](https://s.qiniu.com/YNJrYv)
- 配置信息：[https://fresns.cn/database/keyname/storages.html](https://fresns.cn/database/keyname/storages.html)
- 命令字信息：[https://fresns.cn/extensions/command.html](https://fresns.cn/extensions/command.html)
- 回调返参表：[https://fresns.cn/database/plugin/plugin-callbacks.html](https://fresns.cn/database/plugin/plugin-callbacks.html)

## 插件安装教程

- 1、下载插件安装包；
- 2、解压后把 `QiNiu` 文件夹上传到主程序根目录 `extensions` 文件夹；
- 3、登录 Fresns 控制台，在控制台仪表盘选择「本地安装」，输入文件夹名 `QiNiu` 执行安装；
- 4、安装完成后到控制台「插件」频道“启用”插件；
- 5、启用后可在「[控制面板 > 存储设置](https://fresns.cn/prototype/control-panel/system-storage-image.html)」配置存储服务商参数。

## 插件开发说明

### 命令字功能：获取上传凭证

- 七牛文档：[https://developer.qiniu.com/kodo/1208/upload-token](https://developer.qiniu.com/kodo/1208/upload-token)
- 命令字：`fresns_cmd_get_upload_token`
- 生成后，不仅输出，还要保存到 `plugin_callbacks` 数据表中，主要字段介绍如下：
    - plugin_unikey = QiNiu
    - member_id = 0
    - uuid = 550e8400e29b41d4a716446655440000
    - types = 1
    - content = {"callbackType":1,"dataType":"object","dataValue": {"storageId":17,"fileType":1,"token":"JmTcjiVWsoRxql3OA2krgoW-Fu9bzBZZGCd2lXem:hnuOE5rHuQjyfTIBTH06IaFY0ME=:eyJzY29wZSI6ImF0ZXN0IiwiZGVhZGxpbmUiOjE1MDY5Mzg5NDF9"}}
- uuid 是标准的 32 位数的 16 进制，生成后删除中划线
- content 是压缩后的 Object 对象信息，其中 fileType 根据传参决定，token 是生成的上传凭证。
- **使用场景：**
    - 1、客户端通过 SDK 直接上传到云服务商，通过接口获取上传 Token，该命令字就是生成 Token 用的。
    - 2、客户端访问插件页，在插件页面上传，因为链接需要验权，权限就是 URL 传 Token，具体逻辑见下方「网页功能-上传文件」。

### 命令字功能：上传文件

- 命令字：`fresns_cmd_upload_file`
- 主程序会传参告之上传模式
    - mode = 1 上传文件
    - mode = 2 上传文件信息
- 模式 1 逻辑流程：
    - 1、凭文件 fid 查询文件临时位置 `files > file_path`；
    - 2、按类型和 table_type 编号，将文件上传到七牛云，并更新 `files > file_path` 为七牛云路径；
    - 3、如果是视频文件，则执行配置表 `videos_screenshot` 键值，生成一条视频封面图并存入 `file_appends > video_cover` 字段；
    - 4、删除本地临时文件；
    - 5、输出完整的 fileInfo 文件信息，格式为数组，详情见 [https://fresns.cn/api/editor/upload.html#%E8%BF%94%E5%9B%9E%E7%BB%93%E6%9E%9C](https://fresns.cn/api/editor/upload.html#%E8%BF%94%E5%9B%9E%E7%BB%93%E6%9E%9C)
- 模式 2 逻辑流程：
    - 1、凭文件 fid 查询文件类型，如果是视频，查询 `file_appends > video_cover` 是否有封面图（字段为空则没有），没有则执行配置表 `videos_screenshot` 键值，生成一条视频封面图并存入 `file_appends > video_cover` 字段；
    - 2、无论是哪种类型文件，都需要输出完整的 fileInfo 文件信息，格式为数组，详情见 [https://fresns.cn/api/editor/upload.html#%E8%BF%94%E5%9B%9E%E7%BB%93%E6%9E%9C](https://fresns.cn/api/editor/upload.html#%E8%BF%94%E5%9B%9E%E7%BB%93%E6%9E%9C)
- **使用场景：**
    - 1、客户端直接用主程序接口上传了文件，主程序通过该命令字告之插件，插件做后续操作，比如转存位置、转码等。
    - 2、客户端通过 SDK 直接上传到云服务商，通过接口将上传后的文件信息存档，主程序会通过该命令字告之插件有文件存储，插件可执行自定义功能，比如转码。

### 命令字功能：获取带防盗链签名的地址

- 七牛文档：[https://developer.qiniu.com/kodo/1202/download-token](https://developer.qiniu.com/kodo/1202/download-token)
- 命令字：`fresns_cmd_anti_link_image`
- 命令字：`fresns_cmd_anti_link_video`
- 命令字：`fresns_cmd_anti_link_audio`
- 命令字：`fresns_cmd_anti_link_doc`
- 凭 fid 和 file type 输出该文件地址
- 图片配置：images_url_status、images_url_key、images_url_expire
- 视频配置：videos_url_status、videos_url_key、videos_url_expire
- 音频配置：audios_url_status、audios_url_key、audios_url_expire
- 文档配置：docs_url_status、docs_url_key、docs_url_expire
- **使用场景：**
    - 主程序会判断 url_status 是否开启了防盗链，开启了，会凭 fid 找插件索要文件链接。

### 命令字功能：删除物理文件

- 七牛文档：[https://developer.qiniu.com/kodo/1257/delete](https://developer.qiniu.com/kodo/1257/delete)
- 命令字：`fresns_cmd_physical_deletion_file`
- 凭 fid 物理删除该文件，并将数据表 deleted_at 逻辑删除。

### 网页功能：上传文件

- 访问路径 /qiniu/upload?sign={sign}&token={uploadToken}&uploadInfo={uploadInfo}&callback={uuid}&lang={langtag}
- 七牛文档：[https://developer.qiniu.com/kodo/1272/form-upload](https://developer.qiniu.com/kodo/1272/form-upload)
- 1、解析并判断 sign 是否正确。
    - 参见 https://fresns.cn/api/sign.html
    - 解析后请求命令字 `fresns_cmd_verify_sign` 判断是否有效
- 2、解析并判断 uploadToken 是否正确。
    - 参见 https://fresns.cn/database/plugin/plugin-callbacks.html
    - 该值是由「获取上传凭证」`fresns_cmd_get_upload_token` 的时候生成的。
    - 凭 plugin_unikey 查询近 10 分钟内生成的记录，查询是否有记录并且 token 正确。
    - 参数值解析：
        - 假如 uploadToken 传参值为：`Sm1UY2ppVldzb1J4cWwzT0Eya3Jnb1ctRnU5YnpCWlpHQ2QybFhlbTpobnVPRTVySHVRanlmVElCVEgwNklhRlkwTUU9OmV5SnpZMjl3WlNJNkltRjBaWE4wSWl3aVpHVmhaR3hwYm1VaU9qRTFNRFk1TXpnNU5ERjk%3D`
        - 1、先将参数通过 url_encode 解码，得到 `Sm1UY2ppVldzb1J4cWwzT0Eya3Jnb1ctRnU5YnpCWlpHQ2QybFhlbTpobnVPRTVySHVRanlmVElCVEgwNklhRlkwTUU9OmV5SnpZMjl3WlNJNkltRjBaWE4wSWl3aVpHVmhaR3hwYm1VaU9qRTFNRFk1TXpnNU5ERjk=`
        - 2、再通过 base64_encode 解码，得到最终的参数值 `JmTcjiVWsoRxql3OA2krgoW-Fu9bzBZZGCd2lXem:hnuOE5rHuQjyfTIBTH06IaFY0ME=:eyJzY29wZSI6ImF0ZXN0IiwiZGVhZGxpbmUiOjE1MDY5Mzg5NDF9`
- 3、解析 uploadInfo 参数
    - uploadInfo 是经过 base64_encode 和 url_encode 处理过的一组参数
    - 参数分别是 fileType、tableType、tableName、tableField、tableId、tableKey
    - 格式为 `{"fileType":1,"tableType":1,"tableName":"post_logs","tableField":"id","tableId":1,"tableKey":"key"}`
    - tableId 和 tableKey 可空，逻辑同 [API](https://fresns.cn/api/editor/upload.html) 一致。
    - 参数值解析：
        - 假如 uploadToken 传参值为：`eyJmaWxlVHlwZSI6MSwidGFibGVUeXBlIjoxLCJ0YWJsZU5hbWUiOiJwb3N0X2xvZ3MiLCJ0YWJsZUZpZWxkIjoiaWQiLCJ0YWJsZUlkIjoxLCJ0YWJsZUtleSI6ImtleSJ9`
        - 1、先将参数通过 url_encode 解码，得到 `eyJmaWxlVHlwZSI6MSwidGFibGVUeXBlIjoxLCJ0YWJsZU5hbWUiOiJwb3N0X2xvZ3MiLCJ0YWJsZUZpZWxkIjoiaWQiLCJ0YWJsZUlkIjoxLCJ0YWJsZUtleSI6ImtleSJ9`
        - 2、再通过 base64_encode 解码，得到最终的参数值 `{"fileType":1,"tableType":1,"tableName":"post_logs","tableField":"id","tableId":1,"tableKey":"key"}`
- 4、渲染上传页面
    - 根据路径 fileType 参数，决定上传的文件和页面，分别是上传图片、视频、音频、文档。
    - 获取配置表文件限制信息并展示，包括大小、时长等。
        - 图片限制配置：images_ext、images_max_size
        - 视频限制配置：videos_ext、videos_max_size、videos_max_time
        - 音频限制配置：audios_ext、audios_max_size、audios_max_time
        - 文档限制配置：docs_ext、docs_max_size
    - 根据路径 lang 参数决定当前页面显示语言
- 5、当一切验权通过后，用户上传文件后，将文件信息存储到 `plugin_callbacks` 数据表中。
    - plugin_unikey = QiNiu
    - member_id = {根据 sign 签名解析出来的成员}
    - uuid = {根据 URL 传过来的 callback 值}
    - types = 4
    - content = {参见数据表介绍}
- 6、返回文件信息供页面显示。
    - 图片：name、extension、mime、size、rankNum、imageRatioUrl、imageSquareUrl、imageBigUrl、imageLong
    - 视频：name、extension、mime、size、rankNum、videoTime、videoCover、videoGif、videoUrl
    - 音频：name、extension、mime、size、rankNum、audioTime、audioUrl
    - 文档：name、extension、mime、size、rankNum
- **使用场景：**
    - 客户端不开发功能，直接获取 Token 后，凭 Token 替换 URL 访问插件的上传页面，上传后客户端凭回调 uuid 获取上传后的文件信息。

### 转码功能

**订阅命令字 fresns_cmd_direct_release_content**

- 通过[订阅正式发表命令字](https://fresns.cn/extensions/basis.html#%E8%AE%A2%E9%98%85%E5%91%BD%E4%BB%A4%E5%AD%97%E8%A1%8C%E4%B8%BA)，获知内容主表有新内容 posts 或 comments
- 回调 URL：`/qiniu/transcode` 用于接收七牛云反馈。
- 七牛文档：[https://developer.qiniu.com/dora/3685/directions-for-use-av](https://developer.qiniu.com/dora/3685/directions-for-use-av)
- 转码流程：
    - 前提条件：安装插件时，新增订阅事件，订阅命令字 `fresns_cmd_direct_release_content`，当该命令字完成内容发表后，触发我的指定命令字（我是指本插件）；
    - 1、收到订阅推送，触发我的命令字；
    - 2、我的命令字收到 tableName 和 insertId 传参，凭 tableName 确认内容类型（posts 或 comments）；
    - 3、凭主键 ID（insertId）读取该记录的 `more_json` 字段中 `files` 数组记录；
        - 3.1、`files` 为空，流程中止；
        - 3.2、`files` 有值，则判断 `type` 参数不是 2 或 3（2 代表视频，3 代表音频），则流程中止；
        - 3.3、`files` 有值，`type` 参数为 2 或 3，则继续；
    - 4、凭 `files` 数组记录中 `fid` 查询文件附件表 `file_appends > transcoding_state` 是否需要转码；
        - 4.1、`transcoding_state = 1` 待转码状态，流程继续；
        - 4.2、`transcoding_state = 2` 转码中，已经在转码，流程中止；
        - 4.3、`transcoding_state = 3` 转码完成，已经完成转码，流程中止；
        - 4.4、`transcoding_state = 4` 转码失败，流程中止；
    - 5、执行转码，同时修改文件附属表字段 `transcoding_state = 2`
    - 6、七牛回调
        6.1、转码成功：将转码后的文件名填入 `files > file_path`；将转码前的源文件路径填入 `file_appends > file_original_path`；修改转码状态 `file_appends > transcoding_state = 3`
        6.3、转码失败：修改转码状态 `file_appends > transcoding_state = 4`；如果有转码失败其他参数或备注，填入 `file_appends > transcoding_reason` 字段中。
    - 7、以新的 `files > file_path` 参数拼接地址，替换内容记录 `more_json` 字段中 `files` 数组对应的 URL 参数。
- 转码配置：
    - 类型 2 视频，根据配置表 `videos_transcode` 键值（七牛云转码样式名），执行七牛云转码。
    - 类型 3 音频，根据配置表 `audios_transcode` 键值（七牛云转码样式名），执行七牛云转码。

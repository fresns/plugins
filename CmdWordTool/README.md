# 命令字测试工具

Fresns 官方开发的命令字测试工具。

## 插件安装

- 在官方应用市场搜索 `CmdWordTool` 并点击安装。

## 使用说明

- 接口地址： `/api/cmd-word-tool`
- 请求方式： `POST`
- 请求格式： `Form-data`

### body 参数：

| 参数名 | 类型 | 是否必传 | 说明 |
| --- | --- | --- | --- |
| unikey | text | YES | 插件名 |
| wordName | text | YES | 命令字 |
| param[...] | text | NO | 命令字参数名 |
| param[account] | text | NO | 参数名 `account` |

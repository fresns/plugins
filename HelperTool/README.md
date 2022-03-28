# 辅助函数测试工具

Fresns 官方开发的辅助函数测试工具。

## 插件安装

- 在官方应用市场搜索 `HelperTool` 并点击安装。

## 使用说明

- 接口地址： `/api/helper-tool`
- 请求方式： `POST`
- 请求格式： `Form-data`

### body 参数：

| 参数名 | 类型 | 是否必传 | 说明 |
| --- | --- | --- | --- |
| helperClass | text | YES | 辅助类名 |
| helperName | text | YES | 辅助功能名 |
| param[...] | text | NO | 参数名 |
| param[itemKey] | text | NO | 参数名 `itemKey` |
| param[langTag] | text | NO | 参数名 `langTag` |

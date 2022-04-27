# Helper Test Tool

Fresns official helper function test tool.

## Installation

- Installation with key name: `HelperTool`
- Installation using command: `php artisan fresns:require HelperTool`

## Using

- Endpoint Path: `/api/helper-tool`
- Get Cakes: `POST`
- Request: `Form-data`

### Body Arguments:

| Parameter Name | Type | Required | Description |
| --- | --- | --- | --- |
| helperClass | text | required | class name |
| helperName | text | required | helper name |
| param[0] | text | *optional* | param |

**Body Example:**

| Parameter Name | Parameter Value |
| --- | --- |
| helperClass | `ConfigHelper` |
| helperName | `fresnsConfigByItemKey` |
| param[0] | itemKey Param |
| param[1] | langTag Param |

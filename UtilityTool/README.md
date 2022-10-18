# Utility Test Tool

Fresns official utilities function test tool.

## Installation

- Installation with key name: `UtilityTool`
- Installation using command: `php artisan market:require UtilityTool`

## Using

- Endpoint Path: `/api/utility-tool`
- Get Cakes: `POST`
- Request: `Form-data`

### Body Arguments:

| Parameter Name | Type | Required | Description |
| --- | --- | --- | --- |
| utilityClass | text | required | class name |
| utilityName | text | required | utility name |
| param[0] | text | *optional* | param |

**Body Example:**

| Parameter Name | Parameter Value |
| --- | --- |
| utilityClass | `ConfigUtility` |
| utilityName | `getCodeMessage` |
| param[0] | 30000 |
| param[1] | Fresns |
| param[2] | zh-Hans |

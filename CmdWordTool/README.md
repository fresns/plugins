# Cmd Word Test Tool

Fresns official helper function test tool.

## Installation

- Installation with key name: `CmdWordTool`
- Installation using command: `php artisan market:require CmdWordTool`

## Using

- Endpoint Path: `/api/cmd-word-tool`
- Get Cakes: `POST`
- Request: `Form-data`

### Body Arguments:

| Parameters Name | Type | Required | Description |
| --- | --- | --- | --- |
| unikey | text | required | plugin name |
| wordName | text | required | command word name |
| param[...] | text | *optional* | param name |
| param[account] | text | *optional* | param name |

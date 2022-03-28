# Cmd Word Test Tool

Fresns official helper function test tool.

## Installation

- Search for `CmdWordTool` in the official app store market and click Install.

## Using

- Endpoint Path: `/api/cmd-word-tool`
- Get Cakes: `POST`
- Request: `Form-data`

### body Arguments:

| Parameters Name | Type | Required | Description |
| --- | --- | --- | --- |
| unikey | text | required | plugin name |
| wordName | text | required | command word name |
| param[...] | text | *optional* | param name |
| param[account] | text | *optional* | param name |

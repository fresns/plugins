# Cmd Word Test Tool

Fresns official helper function test tool.

## Installation

- Search for `CmdWordTool` in the official app store market and click Install.

## Using

- Endpoint Path: `/api/cmd-word-tool`
- Get Cakes: `POST`
- Request: `JSON`

### body Arguments:

| Parameters Name | Type | Required | Description |
| --- | --- | --- | --- |
| unikey | String | required | plugin name |
| wordName | String | required | command word name |
| params[...][...] | String | *optional* | param name |
| params[wordBody][account] | String | *optional* | param name |

# Helper Test Tool

Fresns official helper function test tool.

## Installation

- Search for `HelperTool` in the official app store market and click Install.

## Using

- Endpoint Path: `/api/helper-tool`
- Get Cakes: `POST`
- Request: `JSON`

### body Arguments:

| Parameters Name | Type | Required | Description |
| --- | --- | --- | --- |
| helperClass | String | required | class name |
| helperName | String | required | helper name |
| params[...] | String | *optional* | param name |
| params[site_name] | String | *optional* | param name |

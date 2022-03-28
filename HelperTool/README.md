# Helper Test Tool

Fresns official helper function test tool.

## Installation

- Search for `HelperTool` in the official app store market and click Install.

## Using

- Endpoint Path: `/api/helper-tool`
- Get Cakes: `POST`
- Request: `Form-data`

### body Arguments:

| Parameters Name | Type | Required | Description |
| --- | --- | --- | --- |
| helperClass | text | required | class name |
| helperName | text | required | helper name |
| param[...] | text | *optional* | param name |
| param[itemKey] | text | *optional* | param name |
| param[langTag] | text | *optional* | param name |

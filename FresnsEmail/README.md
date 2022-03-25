# Fresns Email

Fresns official development of the SMTP sending method of mail plugin.

## Installation

- Search for `FresnsEmail` in the Fresns App Marketplace and click Install.

## Dev Notes

### Config table key name

- SMTP Host `fresnsemail_smtp_host`
- SMTP Port `fresnsemail_smtp_port`
- SMTP User `fresnsemail_smtp_user`
- SMTP Password `fresnsemail_smtp_password`
- SMTP Verify Type `fresnsemail_verify_type`
    - Null
    - TLS
    - SSL
- Sender Email `fresnsemail_from_mail`
- Sender Name `fresnsemail_from_name`

### Command Word

- `sendCode`
    - Supported variable names: `{sitename}` `{code}` `{time}`
- `sendEmail`

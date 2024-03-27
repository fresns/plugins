# SMTP Email

Fresns official development of the SMTP sending method of mail plugin.

## Installation

- Installation with key name: `SmtpEmail`
- Installation using command: `php artisan market:require SmtpEmail`

## Dev Notes

### Config table key name

- SMTP Host `fresnsemail_smtp_host`
- SMTP Port `fresnsemail_smtp_port`
- SMTP User `fresnsemail_smtp_username`
- SMTP Password `fresnsemail_smtp_password`
- SMTP Verify Type `fresnsemail_verify_type`
    - Null
    - TLS
    - SSL
- Sender Email `fresnsemail_from_mail`
- Sender Name `fresnsemail_from_name`

### Command Word

- `sendCode`
    - Supported variable names: `{logo}` `{icon}` `{name}` `{code}` `{time}`
- `sendEmail`

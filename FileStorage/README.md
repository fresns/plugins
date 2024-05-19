# File Storage

The official File Storage service plugin developed by Fresns. Supports local, ftp and sftp storage methods.

## Installation

- Installation with key name: `FileStorage`
- Installation using command: `php artisan market:require FileStorage`

## Configuration

- Configure the storage service provider parameters in `Fresns Panel > System > Storage Settings`.

| Storage Settings | Local | FTP | SFTP |
| --- | --- | --- | --- |
| Secret ID |  | username | username |
| Secret Key |  | password | password |
| Bucket Name |  | ftp host | ftp host |
| Bucket Region |  | ftp port `Default is 21` | sftp port `Default is 22` |
| Bucket Endpoint |  |  |  |
| Access Domain | System URL of the main program | ftp URL | sftp URL |
| Filesystem Disk | `local` | `local` or `remote` | `local` or `remote` |
| Temporary URL Function | Only the expiration date needs to be configured, the temporary url key is not used |
| Image processing location | `name-end` | `name-end` | `name-end` |

- Image processing libraries: The `Imagick` library is recommended. By default PHP already has the GD library installed, if you use Imagick you need to install this PHP extension.

> Image processing function configuration is built-in, no configuration is required.

## Cautions

- Does not support audio and video compression and transcoding at this time (future development)
- When using FTP and SFTP functions, you need the server security rules to open the outbound permission of the corresponding port.

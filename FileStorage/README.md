# File Storage

The official File Storage service plugin developed by Fresns. Supports local, ftp and sftp storage methods.

## Installation

- Installation with key name: `FileStorage`
- Installation using command: `php artisan market:require FileStorage`

## Configuration

- Configure the storage service provider parameters in `Fresns Panel > System > Storage Settings`.

| Storage Settings | Local | FTP | SFTP |
| --- | --- | --- | --- |
| Secret ID | `Useless, feel free to fill in, but do not leave blank` | username | username |
| Secret Key | `Useless, feel free to fill in, but do not leave blank` | password | password |
| Bucket Name | `Useless, feel free to fill in, but do not leave blank` | ftp host | ftp host |
| Bucket Area | `Useless, feel free to fill in, but do not leave blank` | ftp port `Default is 21` | sftp port `Default is 22` |
| Bucket Domain | System URL of the main program | ftp URL | sftp URL |
| Anti-theft chain function | Only the expiration date needs to be configured, the anti-theft chain key is not used |
| Image processing location | `name-end` | `name-end` | `name-end` |

- Image processing libraries: The `Imagick` library is recommended. By default PHP already has the GD library installed, if you use Imagick you need to install this PHP extension.

> Image processing function configuration is built-in, no configuration is required.

## Cautions

- Does not support audio and video compression and transcoding at this time (future development)
- When using FTP and SFTP functions, you need the server security rules to open the outbound permission of the corresponding port.

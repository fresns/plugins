# S3 Storage

S3 storage, available from any storage service that supports the S3 protocol.

## Installation

- Installation with key name: `S3Storage`
- Installation using command: `php artisan market:require S3Storage`

## Configuration

- Configure the storage service provider parameters in `Fresns Panel > System > Storage Settings`.
- Service Provider: `S3 Storage`

| Storage Settings | Configuration Values (AWS Example) |
| --- | --- |
| Secret ID | AWS_ACCESS_KEY_ID |
| Secret Key | AWS_SECRET_ACCESS_KEY |
| Bucket Name | AWS_BUCKET |
| Bucket Region | AWS_DEFAULT_REGION |
| Bucket Domain | AWS_URL or AWS_ENDPOINT |
| Anti Link Key | AWS_ENDPOINT |

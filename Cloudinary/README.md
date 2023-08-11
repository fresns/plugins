# Cloudinary Storage

Store objects to Cloudinary service providers.

## Installation

- Installation with key name: `Cloudinary`
- Installation using command: `php artisan market:require Cloudinary`

## Configuration

- Configure the storage service provider parameters in `Fresns Panel > System > Storage Settings`.
- Service Provider: `Cloudinary`
- [Click here to visit the Cloudinary website](https://console.cloudinary.com/invites/lpov9zyyucivvxsnalc5/qq5tle7olqu96r75rodt?t=default)

| Storage Settings | Configuration Values | Example |
| --- | --- | --- |
| Secret ID | API Key | `691595272023297` |
| Secret Key | API Secret | `NRTJiUPLY8f-E9oJtky_8UP2qMU` |
| Bucket Name | Cloud Name | `fresns` |
| Bucket Region | Delivery Type | `upload` |
| Bucket Domain | https://res.cloudinary.com/`<cloud_name>`/`<asset_type>`/`<delivery_type>` | `https://res.cloudinary.com/fresns/image/upload/`<br>`https://res.cloudinary.com/fresns/video/upload/`<br>`https://res.cloudinary.com/fresns/raw/upload/` |

- **asset_type**
    - The type of asset to deliver. Valid values: `image`, `video`, or `raw`.
    - [https://cloudinary.com/documentation/image_transformations#transformation_url_structure](https://cloudinary.com/documentation/image_transformations#transformation_url_structure)
- **delivery_type**
    - The storage or delivery type. For details on all possible types: `upload`, `private`, or `authenticated`.
    - [https://cloudinary.com/documentation/image_transformations#delivery_types](https://cloudinary.com/documentation/image_transformations#delivery_types)

### Image

| Function | Configuration Values | Example |
| --- | --- | --- |
| Image Handle Position | `path-start` | `path-start` |
| Config Image | `<transformations>` or `Named Transformations` | `q_auto:good/` or Named |
| Ratio Image | `<transformations>` or `Named Transformations` | `c_limit,w_360/c_limit,h_5000/` or Named |
| Square Image | `<transformations>` or `Named Transformations` | `c_limit,h_360,w_360/` or Named |
| Big Image | `<transformations>` or `Named Transformations` | `c_limit,w_1200/c_limit,h_1.00/q_auto:eco/` or Named |

### Video

| Function | Configuration Values | Example |
| --- | --- | --- |
| Transcode Parameter | `<transformations>` or `Named Transformations` | `q_auto:low/c_limit,w_512/c_limit,h_512/f_mp4/` or Named |
| Transcode Parameter Handle Position | `path-start` | `path-start` |
| Poster Parameter | `<transformations>` or `Named Transformations` | `q_auto:eco/` or Named |
| Poster Parameter Handle Position | `path-start` | `path-start` |

### Audio

| Function | Configuration Values | Example |
| --- | --- | --- |
| Transcode Parameter | `<transformations>` or `Named Transformations` | `q_auto/` or Named |
| Transcode Parameter Handle Position | `path-start` | `path-start` |

# Admin Menu

Fresns official development of easy to manage menu.

## Installation

- Installation with key name: `AdminMenu`
- Installation using command: `php artisan market:require AdminMenu`

## Plugin Config

- 1. Install and activate the plug-in
- 2. Configure extensions
    - Background path: Control Panel -> Extensions -> Manage Extensions.
    - Click the 'Add Service Provider' button in the top right hand corner.
    - Fill in the configuration information and associate the Manage Menu plug-in.
    - Caution.
        - Plugin users will be able to delete posts, comments and manage the user's basic information, so be sure to configure the permissions.
        - Permissions can be selected to specify the role or group dedicated to the administrator.
- 3. delete the extended cache
    - Background path: Control Panel -> Dashboard -> Cache
    - Configuration Information, tick the Advanced Configuration option and click Clear Cache.
- 4. Use the
    - Posts: Bottom right corner of the `...` More Options menu (in a row with the Like and other buttons, located in the last row of the content block).
    - Comments: Bottom right corner of the content `...` More options menu (in a row with the Like and other buttons, in the last row of the content block).
    - Users: Top right corner of the user home page `...` More options menu.

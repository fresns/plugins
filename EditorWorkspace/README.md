# Editor Workspace

With this extension, editors can publish content directly from the client as a specified user.

## Installation

- Installation with key name: `EditorWorkspace`
- Installation using command: `php artisan market:require EditorWorkspace`

## Using

You can assign the extension for use by operations or editorial staff, who can manually capture content and then assign a specific user identity to publish it, giving the impression that the community has many people publishing content.

- 1. install and activate the plug-in
- 2. Configure the plugin
    - Background path: `Control Panel > App Center > Plugins`.
    - Go to the plug-in settings page and add optional accounts and users.
- 3. associate extensions
    - Path: `Control Panel > Extends > User Functions`
    - Click the Add Service Provider button in the upper right corner.
    - Fill in the configuration information and associate the EditorWorkspace plug-in.
    - Note:
        - Users of this plug-in can directly bypass the review mechanism and select a specific user to publish content directly, so be sure to configure the permissions.
        - You can choose to specify the role, such as administrator, editor, and so on.
- 4. Empty extension cache
    - Background path: `Control Panel > Dashboard > Cache`
    - Check the "Extension Configuration" option in the configuration information and click "Clear Cache".
- 5. Users
    - Clients can see the extension in `User Center`, and click to enter the plug-in page to use it.
    - You can specify the user and the date and time when posting content.

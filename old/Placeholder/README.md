# Fresns Placeholder

Placeholder plugin for testing and viewing client generated parameters.

## Installation

- Installation with key name: `Placeholder`
- Installation using command: `php artisan fresns:require Placeholder`

## Path variable name

| Variable Name | Associated Fields | Description |
| --- | --- | --- |
| {uuid} | plugin_callbacks > uuid | [Callback Return to Reference (UUID)](https://fresns.org/extensions/info/callback.html) |
| {sign} |  | [User Token Sign](https://fresns.org/api/url-sign.html) |
| {langTag} |  | Current user client language tag |
| {type} | portal<br>user<br>group<br>hashtag<br>post<br>comment<br>message<br>profile<br>editor<br>account | Access source type |
| {scene} | manage: user and content management<br>userList: post specific users<br>allowBtn: post permission button<br>commentBtn: comment function button<br>icon: user function icon<br>extension: group page or personal center extension<br>postEditor: post editor<br>commentEditor: comment editor<br>postExtend: post extend content<br>commentExtend: comment extend content<br>rechargeExpand: wallet recharge<br>withdrawExpand: wallet withdraw<br>register: user register to join<br>connect: third party account management | Entrance Scene |
| {uid} | users > uid | User ID |
| {rid} | roles > id | Role ID |
| {gid} | groups > gid | Group ID |
| {pid} | posts > pid | Post ID |
| {cid} | comments > cid | Comment ID |
| {eid} | extends > eid | Extend ID |
| {fid} | files > fid | File ID |
| {plid} | post_logs > id | Post Log ID |
| {clid} | comment_logs > id | Comment Log ID |
| {uploadToken} |  | [Upload token parameters](https://fresns.org/api/editor/uploadToken.html) |
| {uploadInfo} |  | Upload file information |

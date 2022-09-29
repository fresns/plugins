<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Email Language Lines
    |--------------------------------------------------------------------------
    */

    'name' => 'Fresnsメールプラグイン',
    'description' => 'SMTP送信のためのFresnsメール公式プラグインです。',

    'menuConfig' => 'メール設定',
    'menuTest' => '送信テスト',
    'menuVariable' => '対応する変数名',

    'smtpHost' => 'SMTPホスト',
    'smtpHostIntro' => 'メール配信の設定が正しくないと、サーバーがタイムアウトしてしまう',
    'smtpPort' => 'SMTPポート',
    'smtpPortIntro' => 'サーバーのセキュリティグループ「Public Outbound」が、このTCPポートに対してオープンである必要があります。',
    'smtpUser' => 'SMTPユーザー名',
    'smtpUserIntro' => 'ユーザー名を入力してください。',
    'smtpPassword' => 'SMTPパスワード',
    'smtpPasswordIntro' => 'パスワードまたは認証コードを入力する',
    'smtpVerifyType' => 'SMTPベリファイタイプ',
    'smtpVerifyTypeIntro' => 'メールサーバーの認証方式を選択する',
    'smtpFromMail' => '送信者メールアドレス',
    'smtpFromMailIntro' => 'ID送信用メールアドレス',
    'smtpFromName' => '送信者名',
    'smtpFromNameIntro' => 'ブランド名または会社名',
    'settingButton' => '保存',

    'testMailDesc' => 'メールボックスの設定を保存した後、ここに受信メールボックスを入力し、送信のテストをしてください。',
    'testMailSend' => '送信確認',

    'variableCode' => 'ベリファイコード',
    'variableTime' => '送信時間',
];

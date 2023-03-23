<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Email Language Lines
    |--------------------------------------------------------------------------
    */

    'name' => 'Fresns Email Plugin',
    'description' => 'Fresns official development of the SMTP sending method of mail plugin.',

    'menuConfig' => 'Mail Config',
    'menuTest' => 'Test Send',
    'menuVariable' => 'Supported Variable Names',

    'smtpHost' => 'SMTP Host',
    'smtpHostIntro' => 'Incorrectly configured sending emails can cause server timeouts',
    'smtpPort' => 'SMTP Port',
    'smtpPortIntro' => 'Your server security group "Outbound Rules" needs to open this TCP port',
    'smtpUser' => 'SMTP Username',
    'smtpUserIntro' => 'Fill in the complete user name',
    'smtpPassword' => 'SMTP Password',
    'smtpPasswordIntro' => 'Fill in the password or authorization code',
    'smtpVerifyType' => 'SMTP Verify Type',
    'smtpVerifyTypeIntro' => 'Select mail server authentication method',
    'smtpFromMail' => 'Sender Email',
    'smtpFromMailIntro' => "Mailbox of the sender's identity",
    'smtpFromName' => 'Sender Name',
    'smtpFromNameIntro' => 'Brand name or company name',
    'settingButton' => 'Save',

    'testMailDesc' => 'After saving the mailbox configuration, enter the incoming mailbox here and test sending.',
    'testMailSend' => 'Confirm Send',

    'variableCode' => 'Verify Code',
    'variableTime' => 'Send Time',
];
